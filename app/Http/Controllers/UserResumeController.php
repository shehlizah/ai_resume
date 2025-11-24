<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\UserResume;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserResumeController extends Controller
{
    /**
     * Show all user resumes
     */
    public function index()
    {
        $resumes = UserResume::where('user_id', Auth::id())
            ->with('template')
            ->latest()
            ->get();

        return view('user.resumes.index', compact('resumes'));
    }

    /**
     * Show template selection page
     */
    public function chooseTemplate()
    {
        $templates = Template::where('is_active', 1)->get();
        return view('user.resumes.choose', compact('templates'));
    }

    /**
     * Show form to fill resume details
     */
    public function fillForm($template_id)
    {
        $template = Template::findOrFail($template_id);
        return view('user.resumes.fill', compact('template'));
    }

    /**
     * Generate PDF from HTML template and user data
     * Saves to database, then redirects to success page that opens PDF
     */
    public function old_generate(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'template_id' => 'required|exists:templates,id',
                'name' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string',
                'experience' => 'nullable|string',
                'skills' => 'nullable|string',
                'education' => 'nullable|string',
            ]);

            // Get template
            $template = Template::findOrFail($request->template_id);

            // Prepare user data
            $data = $request->except(['_token', 'template_id']);

            // Read HTML template from public/templates/html/
            $htmlPath = public_path("templates/html/{$template->slug}.html");

            if (!File::exists($htmlPath)) {
                return back()->with('error', 'Template HTML file not found at: templates/html/' . $template->slug . '.html');
            }

            $html = File::get($htmlPath);

            // Read CSS if exists
            $cssPath = public_path("templates/css/{$template->slug}.css");
            $css = '';

            if (File::exists($cssPath)) {
                $css = File::get($cssPath);
            }

            // Replace placeholders with actual user data
            $filledHtml = $this->fillTemplate($html, $css, $data);

            // Generate PDF
            $pdf = Pdf::loadHTML($filledHtml)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            // Generate unique filename
            $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
            $filePath = 'resumes/' . $fileName;

            // Save PDF to storage
            Storage::put('public/' . $filePath, $pdf->output());

            // Save resume record to database
            $resume = UserResume::create([
                'user_id' => Auth::id(),
                'template_id' => $template->id,
                'data' => json_encode($data),
                'generated_pdf_path' => $filePath,
                'status' => 'completed',
            ]);

            // Redirect to success page with resume ID
            return redirect()->route('user.resumes.success', $resume->id)
                ->with('success', 'Resume generated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error generating resume: ' . $e->getMessage());
        }
    }

    public function xgenerate(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'education' => 'nullable|string',
        ]);

        $template = Template::findOrFail($request->template_id);
        $data = $request->except(['_token', 'template_id']);

        // UPDATED: Read from storage/app/public/templates/
        $htmlPath = storage_path("app/public/templates/html/{$template->slug}.html");

        if (!File::exists($htmlPath)) {
            return back()->with('error', 'Template HTML file not found at: ' . $htmlPath);
        }

        $html = File::get($htmlPath);

        // UPDATED: Read CSS from storage
        $cssPath = storage_path("app/public/templates/css/{$template->slug}.css");
        $css = '';

        if (File::exists($cssPath)) {
            $css = File::get($cssPath);
        }

        // Replace placeholders
        $filledHtml = $this->fillTemplate($html, $css, $data);

        // Generate PDF
        $pdf = Pdf::loadHTML($filledHtml)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // Save PDF
        $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
        $filePath = 'resumes/' . $fileName;

        Storage::put('public/' . $filePath, $pdf->output());

        // Save to database
        $resume = UserResume::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'data' => json_encode($data),
            'generated_pdf_path' => $filePath,
            'status' => 'completed',
        ]);

        return redirect()->route('user.resumes.success', $resume->id);

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

    /**
     * Success page after resume generation
     * Auto-opens PDF in new tab
     */
    public function old_success($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->with('template')
            ->findOrFail($id);

        return view('user.resumes.success', compact('resume'));
    }

    /**
     * Fill HTML template with user data
     */
    private function fillTemplate($html, $css, $data)
    {
        // If CSS exists and HTML doesn't already have styles, inject it
        // (Usually CSS is already embedded via getFullTemplate(), so this is just a fallback)
        if (!empty($css) && strpos($html, '<style>') === false) {
            if (strpos($html, '</head>') !== false) {
                $cssTag = "<style>{$css}</style>";
                $html = str_replace('</head>', $cssTag . '</head>', $html);
            } else {
                // Add style tag at the beginning if no head tag
                $html = "<style>{$css}</style>" . $html;
            }
        }

        // Define all possible placeholder keys
        $keys = [
            'name', 'title', 'email', 'phone', 'address', 'summary',
            'experience', 'skills', 'education', 'certifications', 'projects', 'languages', 'interests'
        ];

        // Keys that contain HTML fragments and should NOT be escaped
        $rawHtmlKeys = ['experience', 'skills', 'education', 'certifications', 'projects', 'languages', 'interests'];

        foreach ($keys as $key) {
            $placeholder = '{{' . $key . '}}';
            $value = isset($data[$key]) ? $data[$key] : '';

            // Skip if placeholder doesn't exist in HTML
            if (strpos($html, $placeholder) === false) {
                continue;
            }

            if (in_array($key, $rawHtmlKeys)) {
                // Already HTML or properly formatted - use as-is
                $replaceValue = (string)$value;
            } else {
                // Escape simple text fields for security
                $replaceValue = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }

            $html = str_replace($placeholder, $replaceValue, $html);
        }

        return $html;
    }

    /**
     * Preview template with sample data
     */

     public function preview($template_id)
{
    $template = Template::findOrFail($template_id);

    // Use the template's full HTML wrapper (includes CSS and body) so preview matches admin
    $html = $template->getFullTemplate();
    $css = '';

            // Sample data for preview (match admin preview sample data)
                $sampleData = [
                        // Personal Information
                        'name' => 'John Doe',
                        'full_name' => 'John Michael Doe',
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'john.doe@example.com',
                        'title' => 'Senior Software Engineer',
                        'job_title' => 'Senior Software Engineer',
                        'phone' => '+1 (555) 123-4567',
                        'address' => '123 Main Street, San Francisco, CA 94105',
                        'city' => 'San Francisco',
                        'state' => 'California',
                        'zip' => '94105',
                        'country' => 'United States',
                        'linkedin' => 'linkedin.com/in/johndoe',
                        'github' => 'github.com/johndoe',
                        'website' => 'www.johndoe.com',
                        'portfolio' => 'johndoe.dev',

                        // Summary/Objective
                        'summary' => 'Experienced software engineer with 10+ years of expertise in full-stack development, cloud architecture, and agile methodologies. Proven track record of delivering high-quality solutions and leading cross-functional teams to success. Passionate about building scalable applications and mentoring junior developers.',
                        'objective' => 'Seeking a challenging senior developer role where I can leverage my expertise in modern web technologies to build innovative solutions and contribute to team growth.',

                        // Experience Section (HTML formatted - using heredoc to preserve actual newlines)
                        'experience' => <<<'EOT'
<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Senior Software Engineer</h3>
        <span class="job-date">Jan 2020 - Present</span>
    </div>
    <div class="company-name">TechCorp Inc. - San Francisco, CA</div>
    <ul class="job-responsibilities">
        <li>Led development of microservices architecture serving 1M+ users with 99.9% uptime</li>
        <li>Mentored team of 5 junior developers, conducting code reviews and technical training</li>
        <li>Improved system performance by 40% through database optimization and caching strategies</li>
        <li>Implemented CI/CD pipelines reducing deployment time by 60%</li>
    </ul>
</div>
<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Software Developer</h3>
        <span class="job-date">Jun 2018 - Dec 2019</span>
    </div>
    <div class="company-name">StartUp LLC - Remote</div>
    <ul class="job-responsibilities">
        <li>Developed RESTful APIs using Laravel and Node.js serving 100K+ daily requests</li>
        <li>Collaborated with product team on feature planning and technical specifications</li>
        <li>Implemented automated testing suite reducing production bugs by 60%</li>
        <li>Migrated legacy monolith to modern microservices architecture</li>
    </ul>
</div>
<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Junior Developer</h3>
        <span class="job-date">Jul 2016 - May 2018</span>
    </div>
    <div class="company-name">Digital Agency - New York, NY</div>
    <ul class="job-responsibilities">
        <li>Built responsive websites for clients using React and Vue.js</li>
        <li>Maintained and updated client WordPress sites</li>
        <li>Participated in agile development process with daily standups</li>
    </ul>
</div>
EOT,

                        // Education Section (HTML formatted - using heredoc to preserve actual newlines)
                        'education' => <<<'EOT'
<div class="education-item">
    <div class="degree-header">
        <h3 class="degree-name">Bachelor of Science in Computer Science</h3>
        <span class="education-date">2014 - 2018</span>
    </div>
    <div class="institution-name">University of California, Berkeley</div>
    <div class="education-details">
        <p>GPA: 3.8/4.0 • Dean's List all semesters</p>
        <p>Relevant Coursework: Data Structures, Algorithms, Database Systems, Software Engineering</p>
    </div>
</div>
<div class="education-item">
    <div class="degree-header">
        <h3 class="degree-name">High School Diploma</h3>
        <span class="education-date">2010 - 2014</span>
    </div>
    <div class="institution-name">Lincoln High School</div>
</div>
EOT,

                        // Skills Section (HTML formatted - using heredoc to preserve actual newlines)
                        'skills' => <<<'EOT'
<div class="skills-grid">
    <div class="skill-category">
        <h4>Languages</h4>
        <ul class="skills-list">
            <li>JavaScript / TypeScript</li>
            <li>Python</li>
            <li>PHP</li>
            <li>Java</li>
            <li>SQL</li>
        </ul>
    </div>
    <div class="skill-category">
        <h4>Frameworks</h4>
        <ul class="skills-list">
            <li>React / Vue.js</li>
            <li>Node.js / Express</li>
            <li>Laravel</li>
            <li>Django</li>
            <li>Spring Boot</li>
        </ul>
    </div>
    <div class="skill-category">
        <h4>Tools & Technologies</h4>
        <ul class="skills-list">
            <li>Docker / Kubernetes</li>
            <li>AWS / Azure / GCP</li>
            <li>Git / GitHub</li>
            <li>MySQL / PostgreSQL</li>
            <li>Redis / MongoDB</li>
        </ul>
    </div>
</div>
EOT,

                        // Certifications
                        'certifications' => <<<'EOT'
<ul class="certifications-list">
    <li>
        <strong>AWS Certified Solutions Architect - Professional</strong>
        <span class="cert-date">Amazon Web Services • 2023</span>
    </li>
    <li>
        <strong>Google Cloud Professional Developer</strong>
        <span class="cert-date">Google Cloud • 2022</span>
    </li>
    <li>
        <strong>Certified Scrum Master (CSM)</strong>
        <span class="cert-date">Scrum Alliance • 2021</span>
    </li>
</ul>
EOT,

                        // Projects
                        'projects' => <<<'EOT'
<div class="project-item">
    <h4>E-commerce Platform</h4>
    <p>Built a full-stack e-commerce solution using React, Node.js, and PostgreSQL. Implemented payment processing, inventory management, and real-time order tracking.</p>
</div>
<div class="project-item">
    <h4>Task Management App</h4>
    <p>Developed a collaborative task management application with real-time updates using WebSockets. Features include team collaboration, file sharing, and deadline notifications.</p>
</div>
<div class="project-item">
    <h4>Open Source Contributions</h4>
    <p>Active contributor to Laravel framework and Vue.js ecosystem. Contributed bug fixes and new features to various popular open-source projects.</p>
</div>
EOT,

                        // Languages
                        'languages' => <<<'EOT'
<ul class="languages-list">
    <li><strong>English</strong> - Native</li>
    <li><strong>Spanish</strong> - Fluent</li>
    <li><strong>French</strong> - Intermediate</li>
</ul>
EOT,

                        // Interests/Hobbies
                        'interests' => <<<'EOT'
<ul class="interests-list">
    <li>Open Source Development</li>
    <li>Tech Blogging</li>
    <li>Photography</li>
    <li>Hiking & Travel</li>
</ul>
EOT,
                ];

        // Fill with sample data into the full template
        $filledHtml = $this->fillTemplate($html, $css, $sampleData);

        // Return as HTML for preview
        return response($filledHtml)->header('Content-Type', 'text/html');
}

    public function old_preview($template_id)
    {
        $template = Template::findOrFail($template_id);

        // Read HTML template
        $htmlPath = public_path("templates/html/{$template->slug}.html");

        if (!File::exists($htmlPath)) {
            return back()->with('error', 'Template HTML file not found!');
        }

        $html = File::get($htmlPath);

        // Read CSS if exists
        $cssPath = public_path("templates/css/{$template->slug}.css");
        $css = '';

        if (File::exists($cssPath)) {
            $css = File::get($cssPath);
        }

        // Sample data for preview
        $sampleData = [
            'name' => 'John Doe',
            'title' => 'Software Engineer',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'experience' => "Senior Software Engineer at Tech Corp (2020-Present)\n- Led development of microservices architecture\n- Managed team of 5 developers\n\nSoftware Developer at StartupXYZ (2018-2020)\n- Built RESTful APIs using Laravel\n- Improved application performance by 40%",
            'skills' => "• PHP, Laravel, Vue.js\n• MySQL, PostgreSQL, Redis\n• Docker, AWS, CI/CD\n• RESTful API Design\n• Team Leadership",
            'education' => "Bachelor of Science in Computer Science\nUniversity of Technology (2014-2018)\nGPA: 3.8/4.0\n\nRelevant Coursework:\n- Data Structures & Algorithms\n- Database Systems\n- Web Development",
        ];

        // Fill with sample data
        $filledHtml = $this->fillTemplate($html, $css, $sampleData);

        // Return as HTML for preview
        return response($filledHtml)->header('Content-Type', 'text/html');
    }

    /**
     * Download a specific resume
     */
    public function xdownload($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->findOrFail($id);

        $filePath = 'public/' . $resume->generated_pdf_path;

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        return Storage::download($filePath, 'resume_' . $resume->id . '.pdf');
    }

    /**
     * View/Preview a resume PDF in browser
     */
    public function xview($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->with('template')
            ->findOrFail($id);

        $filePath = 'public/' . $resume->generated_pdf_path;

        if (!Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        // Stream PDF to browser
        return response()->file(storage_path('app/' . $filePath));
    }

    /**
     * Delete a resume
     */
    public function destroy($id)
    {
        $resume = UserResume::where('user_id', Auth::id())
            ->findOrFail($id);

        // Delete file from storage
        $filePath = 'public/' . $resume->generated_pdf_path;
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete database record
        $resume->delete();

        return redirect()->route('user.resumes.index')
            ->with('success', 'Resume deleted successfully!');
    }

    /**
 * Generate PDF - FIXED VERSION
 */
public function generate(Request $request)
{
    try {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            // Legacy free-text arrays
            'experience' => 'nullable|array',
            'experience.*' => 'nullable|string',
            'education' => 'nullable|array',
            'education.*' => 'nullable|string',
            // Structured fields (preferred)
            'job_title' => 'nullable|array',
            'job_title.*' => 'nullable|string',
            'company' => 'nullable|array',
            'company.*' => 'nullable|string',
            'start_date' => 'nullable|array',
            'start_date.*' => 'nullable|string',
            'end_date' => 'nullable|array',
            'end_date.*' => 'nullable|string',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'nullable|string',
            'degree' => 'nullable|array',
            'degree.*' => 'nullable|string',
            'field_of_study' => 'nullable|array',
            'field_of_study.*' => 'nullable|string',
            'university' => 'nullable|array',
            'university.*' => 'nullable|string',
            'graduation_year' => 'nullable|array',
            'graduation_year.*' => 'nullable|string',
            'education_details' => 'nullable|array',
            'education_details.*' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        $template = Template::findOrFail($request->template_id);
        $data = $request->except(['_token', 'template_id']);

        // Build experience HTML from structured fields if provided
        if (isset($data['job_title']) && is_array($data['job_title'])) {
            $count = count($data['job_title']);
            $htmlExperiences = [];
            for ($i = 0; $i < $count; $i++) {
                $title = $data['job_title'][$i] ?? '';
                $company = $data['company'][$i] ?? '';
                $start = $data['start_date'][$i] ?? '';
                $end = $data['end_date'][$i] ?? '';
                $resp = $data['responsibilities'][$i] ?? '';

                // Skip empty entries
                if (trim($title) === '' && trim($company) === '' && trim($resp) === '') {
                    continue;
                }

                $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                $companyEsc = htmlspecialchars($company, ENT_QUOTES, 'UTF-8');
                $startEsc = htmlspecialchars($start, ENT_QUOTES, 'UTF-8');
                $endEsc = htmlspecialchars($end, ENT_QUOTES, 'UTF-8');

                // Build responsibilities list
                $respHtml = '';
                if (trim($resp) !== '') {
                    $lines = preg_split('/\r?\n/', $resp);
                    $items = [];
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '') continue;
                        $items[] = '<li>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
                    }
                    if (!empty($items)) {
                        $respHtml = '<ul class="job-responsibilities">' . implode('', $items) . '</ul>';
                    }
                }

                $jobDate = trim($startEsc . ' - ' . $endEsc);

                $block = '<div class="experience-item">';
                $block .= '<div class="job-header"><h3 class="job-title">' . $titleEsc . '</h3>';
                if ($jobDate) {
                    $block .= '<span class="job-date">' . $jobDate . '</span>';
                }
                $block .= '</div>';
                if ($companyEsc) {
                    $block .= '<div class="company-name">' . $companyEsc . '</div>';
                }
                $block .= $respHtml;
                $block .= '</div>';

                $htmlExperiences[] = $block;
            }

            $data['experience'] = implode("\n", $htmlExperiences);
        } else {
            // Fallback: merge legacy experience[] into HTML blocks
            if (isset($data['experience']) && is_array($data['experience'])) {
                $experiences = array_filter($data['experience']);
                if (!empty($experiences)) {
                    $htmlExperiences = [];
                    foreach ($experiences as $exp) {
                        $escaped = htmlspecialchars($exp, ENT_QUOTES, 'UTF-8');
                        $htmlExperiences[] = "<div class=\"experience-item\">" . nl2br($escaped) . "</div>";
                    }
                    $data['experience'] = implode("\n", $htmlExperiences);
                } else {
                    $data['experience'] = '';
                }
            }
        }

        // Build education HTML from structured fields if provided
        if (isset($data['degree']) && is_array($data['degree'])) {
            $count = count($data['degree']);
            $htmlEducations = [];
            for ($i = 0; $i < $count; $i++) {
                $degree = $data['degree'][$i] ?? '';
                $field = $data['field_of_study'][$i] ?? '';
                $univ = $data['university'][$i] ?? '';
                $grad = $data['graduation_year'][$i] ?? '';
                $details = $data['education_details'][$i] ?? '';

                if (trim($degree) === '' && trim($univ) === '' && trim($details) === '') continue;

                $degreeEsc = htmlspecialchars($degree, ENT_QUOTES, 'UTF-8');
                $fieldEsc = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');
                $univEsc = htmlspecialchars($univ, ENT_QUOTES, 'UTF-8');
                $gradEsc = htmlspecialchars($grad, ENT_QUOTES, 'UTF-8');

                $detailsHtml = '';
                if (trim($details) !== '') {
                    $detailsHtml = '<div class="education-details">' . nl2br(htmlspecialchars($details, ENT_QUOTES, 'UTF-8')) . '</div>';
                }

                $block = '<div class="education-item">';
                $block .= '<div class="degree-header"><h3 class="degree-name">' . $degreeEsc . '</h3>';
                if ($gradEsc) {
                    $block .= '<span class="education-date">' . $gradEsc . '</span>';
                }
                $block .= '</div>';
                if ($univEsc) {
                    $block .= '<div class="institution-name">' . $univEsc . '</div>';
                }
                if ($fieldEsc) {
                    $block .= '<div class="field-of-study">' . $fieldEsc . '</div>';
                }
                $block .= $detailsHtml;
                $block .= '</div>';

                $htmlEducations[] = $block;
            }

            $data['education'] = implode("\n", $htmlEducations);
        } else {
            // Fallback: merge legacy education[] into HTML blocks
            if (isset($data['education']) && is_array($data['education'])) {
                $educations = array_filter($data['education']);
                if (!empty($educations)) {
                    $htmlEducations = [];
                    foreach ($educations as $edu) {
                        $escaped = htmlspecialchars($edu, ENT_QUOTES, 'UTF-8');
                        $htmlEducations[] = "<div class=\"education-item\">" . nl2br($escaped) . "</div>";
                    }
                    $data['education'] = implode("\n", $htmlEducations);
                } else {
                    $data['education'] = '';
                }
            }
        }

        // Use the template's full HTML wrapper (includes CSS and body) so generated resume matches preview
        $html = $template->getFullTemplate();
        $css = '';

        // Fill the full template with user data
        $filledHtml = $this->fillTemplate($html, $css, $data);

        // Save to database with HTML content (instead of PDF file)
        $resume = UserResume::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'data' => json_encode($data),
            'generated_pdf_path' => 'html', // Mark as HTML-based resume
            'status' => 'completed',
        ]);

        // Return the filled HTML as response (user can print/save as PDF from browser)
        return response($filledHtml)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="resume.html"');

    } catch (\Exception $e) {
        return back()->withInput()->with('error', $e->getMessage());
    }
}

/**
 * View Resume (HTML) - Browser Display
 */
public function view($id)
{
    $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);

    // Reconstruct the HTML from stored template and data
    // Note: $resume->data is automatically cast to array by the model
    $template = $resume->template;
    $data = $resume->data ?? [];

    // Regenerate the HTML
    $html = $template->getFullTemplate();
    $filledHtml = $this->fillTemplate($html, '', $data);

    // Return as HTML response for browser display
    return response($filledHtml)
        ->header('Content-Type', 'text/html; charset=UTF-8');
}

/**
 * Download Resume as HTML File
 */
public function download($id)
{
    $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);

    // Reconstruct the HTML from stored template and data
    // Note: $resume->data is automatically cast to array by the model
    $template = $resume->template;
    $data = $resume->data ?? [];

    // Regenerate the HTML
    $html = $template->getFullTemplate();
    $filledHtml = $this->fillTemplate($html, '', $data);

    // Return as downloadable HTML file
    return response($filledHtml)
        ->header('Content-Type', 'text/html; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="resume.html"');
}

/**
 * Show success page after generating resume
 */
public function success($id)
{
    $resume = UserResume::where('id', $id)
        ->where('user_id', auth()->id())
        ->with('template')
        ->firstOrFail();

    // Get available add-ons for upsell
    $addOns = \App\Models\AddOn::active()
        ->orderBy('sort_order')
        ->get();

    // Check which add-ons user has already purchased
    $purchasedAddOnIds = auth()->user()->userAddOns()
        ->where('status', 'active')
        ->pluck('add_on_id')
        ->toArray();

    // Filter out purchased add-ons
    $availableAddOns = $addOns->whereNotIn('id', $purchasedAddOnIds);

    return view('user.resumes.success', compact('resume', 'availableAddOns'));
}

/**
 * Generate Experience Content with AI
 */
public function generateExperienceAI(Request $request)
{
    try {
        $validated = $request->validate([
            'job_title' => 'required|string',
            'company' => 'required|string',
            'years' => 'required|numeric|min:0',
            'responsibilities' => 'nullable|string',
        ]);

        // Use OpenAI API or similar to generate professional experience content
        $prompt = "Generate a professional resume experience entry for someone who worked as a {$validated['job_title']} at {$validated['company']} for {$validated['years']} years";

        if ($validated['responsibilities']) {
            $prompt .= ". Key responsibilities: {$validated['responsibilities']}";
        }

        $prompt .= ". Format as bullet points with 3-4 achievement statements. Make it professional and impactful.";

        // Call OpenAI API
        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

/**
 * Generate Skills Content with AI
 */
public function generateSkillsAI(Request $request)
{
    try {
        $validated = $request->validate([
            'role' => 'required|string',
            'level' => 'required|in:junior,mid,senior',
            'fields' => 'nullable|string',
        ]);

        $levelDescriptions = [
            'junior' => 'junior-level',
            'mid' => 'mid-level',
            'senior' => 'senior-level',
        ];

        $prompt = "Generate a comprehensive skills list for a {$levelDescriptions[$validated['level']]} {$validated['role']}";

        if ($validated['fields']) {
            $prompt .= " with expertise in: {$validated['fields']}";
        }

        $prompt .= ". Include technical skills, programming languages, frameworks, tools, and soft skills. Format as comma-separated or bullet points. Make it professional and industry-relevant.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

/**
 * Generate Education Content with AI
 */
public function generateEducationAI(Request $request)
{
    try {
        $validated = $request->validate([
            'degree' => 'required|string',
            'field_of_study' => 'required|string',
            'university' => 'required|string',
            'graduation_year' => 'required|numeric|min:1950',
        ]);

        $prompt = "Generate a professional education entry for someone with a {$validated['degree']} in {$validated['field_of_study']} from {$validated['university']}, graduated in {$validated['graduation_year']}";

        $prompt .= ". Include relevant coursework, honors, GPA (if applicable), and achievements. Format professionally for a resume. Keep it concise but impressive.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

/**
 * Call OpenAI API to generate content
 */
private function callOpenAI($prompt)
{
    try {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            \Log::warning('OpenAI API key not configured');
            return null;
        }

        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional resume writer. Generate clear, concise, and impactful resume content.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }

        return null;

    } catch (\Exception $e) {
        \Log::error('OpenAI API error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Generate Professional Summary with AI
 */
public function generateSummaryAI(Request $request)
{
    try {
        $validated = $request->validate([
            'role' => 'required|string',
            'years' => 'required|numeric|min:0',
            'skills' => 'nullable|string',
            'goal' => 'nullable|string',
        ]);

        $prompt = "Generate a compelling 2-3 sentence professional summary for a {$validated['role']} with {$validated['years']} years of experience";

        if ($validated['skills']) {
            $prompt .= ". Key skills: {$validated['skills']}";
        }

        if ($validated['goal']) {
            $prompt .= ". Career goal: {$validated['goal']}";
        }

        $prompt .= ". Make it professional, engaging, and suitable for a resume. It should highlight achievements and value proposition.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

}
