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
     * Generate PDF - CORRECTED VERSION
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

            // Build experience HTML from structured fields
            $data['experience'] = $this->buildExperienceHtml($data);
            
            // Build education HTML from structured fields
            $data['education'] = $this->buildEducationHtml($data);
            
            // Build skills HTML if needed
            $data['skills'] = $this->buildSkillsHtml($data);

            // Get the ORIGINAL template HTML and CSS (not PDF-optimized version)
            // This preserves the template designer's intent
            $html = $template->html_content;
            $css = $template->css_content ?? '';
            
            // Fill template with user data
            $filledHtml = $this->fillTemplate($html, $css, $data);

            // Generate PDF using DomPDF
            $pdf = Pdf::loadHTML($filledHtml)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true, // Disable remote for security
                    'chroot' => storage_path('app/public'),
                    
                ]);

            // Create directory if needed
            $directory = storage_path('app/public/resumes');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate filename and save
            $fileName = 'resume_' . Auth::id() . '_' . time() . '.pdf';
            $fullPath = $directory . '/' . $fileName;
            
            File::put($fullPath, $pdf->output());

            // Save to database
            $resume = UserResume::create([
                'user_id' => Auth::id(),
                'template_id' => $template->id,
                'data' => json_encode($data),
                'generated_pdf_path' => 'resumes/' . $fileName,
                'status' => 'completed',
            ]);

            return redirect()->route('user.resumes.success', $resume->id)
                ->with('success', 'Resume generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Resume generation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error generating resume: ' . $e->getMessage());
        }
    }

    /**
     * Build Experience HTML from structured or legacy data
     */
    private function buildExperienceHtml($data)
    {
        // Check if we have structured job data
        if (isset($data['job_title']) && is_array($data['job_title'])) {
            return $this->buildStructuredExperience($data);
        }
        
        // Fallback to legacy experience array
        if (isset($data['experience']) && is_array($data['experience'])) {
            return $this->buildLegacyExperience($data['experience']);
        }
        
        return '';
    }

    /**
     * Build experience from structured fields (job_title, company, etc.)
     */
    private function buildStructuredExperience($data)
    {
        $count = count($data['job_title'] ?? []);
        $htmlExperiences = [];
        
        for ($i = 0; $i < $count; $i++) {
            $title = $data['job_title'][$i] ?? '';
            $company = $data['company'][$i] ?? '';
            $start = $data['start_date'][$i] ?? '';
            $end = $data['end_date'][$i] ?? '';
            $resp = $data['responsibilities'][$i] ?? '';

            // Skip empty entries
            if (empty(trim($title)) && empty(trim($company)) && empty(trim($resp))) {
                continue;
            }

            $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $companyEsc = htmlspecialchars($company, ENT_QUOTES, 'UTF-8');
            $startEsc = htmlspecialchars($start, ENT_QUOTES, 'UTF-8');
            $endEsc = htmlspecialchars($end, ENT_QUOTES, 'UTF-8');

            // Build date range
            $dateRange = '';
            if ($start || $end) {
                $dateRange = trim($startEsc . ($start && $end ? ' - ' : '') . $endEsc);
            }

            // Build responsibilities list
            $respHtml = $this->buildResponsibilitiesList($resp);

            // Build experience block
            $block = '<div class="experience-item">';
            
            // Job header with title and date
            $block .= '<div class="job-header">';
            $block .= '<h3 class="job-title">' . $titleEsc . '</h3>';
            if ($dateRange) {
                $block .= '<span class="job-date">' . $dateRange . '</span>';
            }
            $block .= '</div>';
            
            // Company name
            if ($companyEsc) {
                $block .= '<div class="company-name">' . $companyEsc . '</div>';
            }
            
            // Responsibilities
            $block .= $respHtml;
            $block .= '</div>';

            $htmlExperiences[] = $block;
        }

        return implode("\n", $htmlExperiences);
    }

    /**
     * Build experience from legacy format (simple text array)
     */
    private function buildLegacyExperience($experiences)
    {
        $experiences = array_filter($experiences);
        if (empty($experiences)) {
            return '';
        }

        $htmlExperiences = [];
        foreach ($experiences as $exp) {
            $escaped = htmlspecialchars($exp, ENT_QUOTES, 'UTF-8');
            $htmlExperiences[] = '<div class="experience-item">' . nl2br($escaped) . '</div>';
        }
        
        return implode("\n", $htmlExperiences);
    }

    /**
     * Build responsibilities list from text
     */
    private function buildResponsibilitiesList($resp)
    {
        if (empty(trim($resp))) {
            return '';
        }

        $lines = preg_split('/\r?\n/', $resp);
        $items = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            // Remove bullet points or dashes if present
            $line = preg_replace('/^[-•*]\s*/', '', $line);
            
            if ($line !== '') {
                $items[] = '<li>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
            }
        }
        
        if (!empty($items)) {
            return '<ul class="job-responsibilities">' . implode('', $items) . '</ul>';
        }
        
        return '';
    }

    /**
     * Build Education HTML from structured or legacy data
     */
    private function buildEducationHtml($data)
    {
        // Check if we have structured degree data
        if (isset($data['degree']) && is_array($data['degree'])) {
            return $this->buildStructuredEducation($data);
        }
        
        // Fallback to legacy education array
        if (isset($data['education']) && is_array($data['education'])) {
            return $this->buildLegacyEducation($data['education']);
        }
        
        return '';
    }

    /**
     * Build education from structured fields
     */
    private function buildStructuredEducation($data)
    {
        $count = count($data['degree'] ?? []);
        $htmlEducations = [];
        
        for ($i = 0; $i < $count; $i++) {
            $degree = $data['degree'][$i] ?? '';
            $field = $data['field_of_study'][$i] ?? '';
            $univ = $data['university'][$i] ?? '';
            $grad = $data['graduation_year'][$i] ?? '';
            $details = $data['education_details'][$i] ?? '';

            if (empty(trim($degree)) && empty(trim($univ)) && empty(trim($details))) {
                continue;
            }

            $degreeEsc = htmlspecialchars($degree, ENT_QUOTES, 'UTF-8');
            $fieldEsc = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');
            $univEsc = htmlspecialchars($univ, ENT_QUOTES, 'UTF-8');
            $gradEsc = htmlspecialchars($grad, ENT_QUOTES, 'UTF-8');

            // Build details section
            $detailsHtml = '';
            if (!empty(trim($details))) {
                $detailsHtml = '<div class="education-details">' 
                    . nl2br(htmlspecialchars($details, ENT_QUOTES, 'UTF-8')) 
                    . '</div>';
            }

            // Build education block
            $block = '<div class="education-item">';
            
            // Degree header
            $block .= '<div class="degree-header">';
            $block .= '<h3 class="degree-name">' . $degreeEsc . '</h3>';
            if ($gradEsc) {
                $block .= '<span class="education-date">' . $gradEsc . '</span>';
            }
            $block .= '</div>';
            
            // Institution
            if ($univEsc) {
                $block .= '<div class="institution-name">' . $univEsc . '</div>';
            }
            
            // Field of study
            if ($fieldEsc) {
                $block .= '<div class="field-of-study">' . $fieldEsc . '</div>';
            }
            
            // Additional details
            $block .= $detailsHtml;
            $block .= '</div>';

            $htmlEducations[] = $block;
        }

        return implode("\n", $htmlEducations);
    }

    /**
     * Build education from legacy format
     */
    private function buildLegacyEducation($educations)
    {
        $educations = array_filter($educations);
        if (empty($educations)) {
            return '';
        }

        $htmlEducations = [];
        foreach ($educations as $edu) {
            $escaped = htmlspecialchars($edu, ENT_QUOTES, 'UTF-8');
            $htmlEducations[] = '<div class="education-item">' . nl2br($escaped) . '</div>';
        }
        
        return implode("\n", $htmlEducations);
    }

    /**
     * Build Skills HTML
     */
    private function buildSkillsHtml($data)
    {
        if (!isset($data['skills'])) {
            return '';
        }

        $skills = $data['skills'];
        
        // If it's already HTML, return as-is
        if (strpos($skills, '<') !== false) {
            return $skills;
        }

        // If it's a comma-separated list, convert to skill items
        if (strpos($skills, ',') !== false) {
            $skillsArray = array_map('trim', explode(',', $skills));
            $skillItems = [];
            foreach ($skillsArray as $skill) {
                if (!empty($skill)) {
                    $skillItems[] = '<span class="skill-item">' . htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') . '</span>';
                }
            }
            return implode("\n", $skillItems);
        }

        // If it's line-separated, convert to skill items
        if (strpos($skills, "\n") !== false) {
            $skillsArray = array_map('trim', explode("\n", $skills));
            $skillItems = [];
            foreach ($skillsArray as $skill) {
                $skill = preg_replace('/^[-•*]\s*/', '', $skill); // Remove bullets
                if (!empty($skill)) {
                    $skillItems[] = '<span class="skill-item">' . htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') . '</span>';
                }
            }
            return implode("\n", $skillItems);
        }

        // Otherwise, return as plain text (escaped)
        return htmlspecialchars($skills, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Fill HTML template with user data
     */
    private function fillTemplate($html, $css, $data)
    {
        // If CSS exists and not already in HTML, inject it into the head
        if (!empty($css) && strpos($html, '<style>') === false && strpos($html, '<link') === false) {
            if (strpos($html, '</head>') !== false) {
                // Add CSS before closing head tag
                $cssTag = "\n    <style>\n{$css}\n    </style>";
                $html = str_replace('</head>', $cssTag . "\n</head>", $html);
            } elseif (strpos($html, '<body') !== false) {
                // If no head tag, add before body
                $cssTag = "<style>{$css}</style>\n    ";
                $html = str_replace('<body', $cssTag . '<body', $html);
            } else {
                // Add at the very beginning
                $html = "<style>{$css}</style>\n" . $html;
            }
        }

        // Define all possible placeholders
        $keys = [
            'name', 'title', 'email', 'phone', 'address', 'summary',
            'experience', 'skills', 'education', 
            'certifications', 'projects', 'languages', 'interests'
        ];

        // Keys that contain HTML fragments (should NOT be escaped)
        $rawHtmlKeys = ['experience', 'skills', 'education', 'certifications', 'projects', 'languages', 'interests'];

        foreach ($keys as $key) {
            $placeholder = '{{' . $key . '}}';
            
            // Skip if placeholder doesn't exist in HTML
            if (strpos($html, $placeholder) === false) {
                continue;
            }
            
            $value = $data[$key] ?? '';

            if (in_array($key, $rawHtmlKeys)) {
                // Already HTML - use as-is
                $replaceValue = $value;
            } else {
                // Escape plain text fields
                $replaceValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
        
        // Use the original HTML and CSS, not the PDF-optimized version
        $html = $template->html_content;
        $css = $template->css_content ?? '';

        // Sample data for preview
        $sampleData = $this->getSampleData();

        // Fill with sample data
        $filledHtml = $this->fillTemplate($html, $css, $sampleData);

        return response($filledHtml)->header('Content-Type', 'text/html');
    }

/**
 * Get sample data for preview - FIXED SYNTAX
 */
private function getSampleData()
{
    return [
        'name' => 'John Doe',
        'title' => 'Senior Software Engineer',
        'email' => 'john.doe@example.com',
        'phone' => '+1 (555) 123-4567',
        'address' => 'San Francisco, CA',
        'summary' => 'Experienced software engineer with 10+ years of expertise in full-stack development, cloud architecture, and agile methodologies. Proven track record of delivering high-quality solutions and leading cross-functional teams to success.',
        'experience' => '<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Senior Software Engineer</h3>
        <span class="job-date">Jan 2020 - Present</span>
    </div>
    <div class="company-name">TechCorp Inc.</div>
    <ul class="job-responsibilities">
        <li>Led development of microservices architecture serving 1M+ users with 99.9% uptime</li>
        <li>Mentored team of 5 junior developers, conducting code reviews and technical training</li>
        <li>Improved system performance by 40% through database optimization and caching strategies</li>
    </ul>
</div>
<div class="experience-item">
    <div class="job-header">
        <h3 class="job-title">Software Developer</h3>
        <span class="job-date">Jun 2018 - Dec 2019</span>
    </div>
    <div class="company-name">StartUp LLC</div>
    <ul class="job-responsibilities">
        <li>Developed RESTful APIs using Laravel and Node.js</li>
        <li>Implemented automated testing suite reducing production bugs by 60%</li>
    </ul>
</div>',
        'education' => '<div class="education-item">
    <div class="degree-header">
        <h3 class="degree-name">Bachelor of Science in Computer Science</h3>
        <span class="education-date">2014 - 2018</span>
    </div>
    <div class="institution-name">University of California, Berkeley</div>
</div>',
        'skills' => '<span class="skill-item">PHP</span>
<span class="skill-item">Laravel</span>
<span class="skill-item">JavaScript</span>
<span class="skill-item">React</span>
<span class="skill-item">MySQL</span>
<span class="skill-item">Docker</span>
<span class="skill-item">AWS</span>
<span class="skill-item">Git</span>',
    ];
}

    /**
     * View PDF in browser
     */
    public function view($id)
    {
        $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);
        $fullPath = storage_path('app/public/' . $resume->generated_pdf_path);

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Resume file not found');
        }

        return response()->file($fullPath);
    }

    /**
     * Download PDF
     */
    public function download($id)
    {
        $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);
        $fullPath = storage_path('app/public/' . $resume->generated_pdf_path);

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Resume file not found');
        }

        return response()->download($fullPath, 'resume.pdf');
    }

    /**
     * Delete a resume
     */
    public function destroy($id)
    {
        $resume = UserResume::where('user_id', Auth::id())->findOrFail($id);

        // Delete file
        $filePath = storage_path('app/public/' . $resume->generated_pdf_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $resume->delete();

        return redirect()->route('user.resumes.index')
            ->with('success', 'Resume deleted successfully!');
    }

    /**
     * Show success page
     */
    // public function success($id)
    // {
    //     $resume = UserResume::where('id', $id)
    //         ->where('user_id', auth()->id())
    //         ->with('template')
    //         ->firstOrFail();

    //     return view('user.resumes.success', compact('resume'));
    // }
    
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


    // AI generation methods remain the same...
    // (I'll skip them to keep this focused, but they should stay as-is)
    
    
    // ADD THESE METHODS TO YOUR UserResumeController.php
// Place them at the end of the class, before the closing }

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

        $prompt = "Generate a professional resume experience entry for someone who worked as a {$validated['job_title']} at {$validated['company']} for {$validated['years']} years";

        if (!empty($validated['responsibilities'])) {
            $prompt .= ". Key responsibilities: {$validated['responsibilities']}";
        }

        $prompt .= ". Format as bullet points with 3-4 achievement statements. Make it professional and impactful. Return ONLY the bullet points, no introductory text.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Experience Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
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
            'junior' => 'junior-level (0-3 years experience)',
            'mid' => 'mid-level (3-7 years experience)',
            'senior' => 'senior-level (7+ years experience)',
        ];

        $prompt = "Generate a comprehensive skills list for a {$levelDescriptions[$validated['level']]} {$validated['role']}";

        if (!empty($validated['fields'])) {
            $prompt .= " with expertise in: {$validated['fields']}";
        }

        $prompt .= ". Include technical skills, programming languages, frameworks, tools, and soft skills. Format as a comma-separated list. Make it professional and industry-relevant. Return ONLY the skills list, no introductory text.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Skills Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
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
            'graduation_year' => 'required|numeric|min:1950|max:2030',
        ]);

        $prompt = "Generate professional education details for someone with a {$validated['degree']} in {$validated['field_of_study']} from {$validated['university']}, graduated in {$validated['graduation_year']}";

        $prompt .= ". Include relevant coursework, honors, GPA (if applicable), and achievements. Keep it concise (2-3 lines) and professional. Return ONLY the education details, no introductory text.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Education Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
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

        if (!empty($validated['skills'])) {
            $prompt .= ". Key skills: {$validated['skills']}";
        }

        if (!empty($validated['goal'])) {
            $prompt .= ". Career goal: {$validated['goal']}";
        }

        $prompt .= ". Make it professional, engaging, and suitable for a resume. It should highlight achievements and value proposition. Return ONLY the summary text, no introductory phrases.";

        $content = $this->callOpenAI($prompt);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API configuration.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'content' => $content
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Summary Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Call OpenAI API - WITH PROPER ERROR HANDLING
 */
private function callOpenAI($prompt)
{
    try {
        $apiKey = config('services.openai.api_key');

        // Check if API key is configured
        if (empty($apiKey)) {
            \Log::error('OpenAI API key not configured');
            throw new \Exception('OpenAI API key is not configured. Please add it to your .env file.');
        }

        // Validate API key format
        if (!str_starts_with($apiKey, 'sk-')) {
            \Log::error('Invalid OpenAI API key format');
            throw new \Exception('Invalid OpenAI API key format. Key should start with "sk-".');
        }

        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

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
                        'content' => 'You are a professional resume writer. Generate clear, concise, and impactful resume content. Always provide only the requested content without any introductory phrases or explanations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ],
            'http_errors' => false,
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        // Check if response is HTML (error page)
        if (str_starts_with(trim($body), '<!DOCTYPE') || str_starts_with(trim($body), '<html')) {
            \Log::error('OpenAI API returned HTML instead of JSON', [
                'status_code' => $statusCode,
                'body_preview' => substr($body, 0, 200)
            ]);
            throw new \Exception('API returned an error page. This usually means authentication failed or the API is unavailable.');
        }

        // Try to decode JSON
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('Failed to decode OpenAI API response', [
                'json_error' => json_last_error_msg(),
                'status_code' => $statusCode,
                'body_preview' => substr($body, 0, 200)
            ]);
            throw new \Exception('Invalid response from OpenAI API: ' . json_last_error_msg());
        }

        // Check for API errors
        if ($statusCode !== 200) {
            $errorMessage = $data['error']['message'] ?? 'Unknown error';
            $errorType = $data['error']['type'] ?? 'unknown';
            
            \Log::error('OpenAI API error', [
                'status_code' => $statusCode,
                'error_type' => $errorType,
                'error_message' => $errorMessage
            ]);

            if ($statusCode === 401) {
                throw new \Exception('Invalid OpenAI API key. Please check your configuration.');
            } elseif ($statusCode === 429) {
                throw new \Exception('OpenAI API rate limit exceeded. Please try again later.');
            } elseif ($statusCode === 500) {
                throw new \Exception('OpenAI API is currently unavailable. Please try again later.');
            } else {
                throw new \Exception('OpenAI API error: ' . $errorMessage);
            }
        }

        // Extract content from response
        if (isset($data['choices'][0]['message']['content'])) {
            $content = trim($data['choices'][0]['message']['content']);
            
            // Remove common AI prefixes
            $content = preg_replace('/^(Here are|Here is|Sure,?|Certainly,?|Of course,?).*/i', '', $content);
            $content = trim($content);
            
            return $content;
        }

        \Log::error('Unexpected OpenAI API response structure', ['data' => $data]);
        throw new \Exception('Unexpected response structure from OpenAI API.');

    } catch (\GuzzleHttp\Exception\ConnectException $e) {
        \Log::error('Failed to connect to OpenAI API', ['error' => $e->getMessage()]);
        throw new \Exception('Could not connect to OpenAI API. Please check your internet connection.');
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        \Log::error('OpenAI API request failed', ['error' => $e->getMessage()]);
        throw new \Exception('Request to OpenAI API failed: ' . $e->getMessage());
    } catch (\Exception $e) {
        // Re-throw our custom exceptions
        if (strpos($e->getMessage(), 'OpenAI') !== false) {
            throw $e;
        }
        
        \Log::error('Unexpected error in callOpenAI', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw new \Exception('An unexpected error occurred while generating content.');
    }
}

}