<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index()
    {
        $templates = Template::latest()->paginate(15);
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $categories = ['professional', 'creative', 'modern', 'classic', 'minimal', 'executive'];
        return view('admin.templates.create', compact('categories'));
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'css_content' => 'required|string',
            'preview_image' => 'nullable|image|max:2048',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Create template
        $template = new Template();
        $template->name = $validated['name'];
        $template->category = $validated['category'];
        $template->description = $validated['description'] ?? null;
        $template->html_content = $validated['html_content'];
        $template->css_content = $validated['css_content'];
        $template->is_premium = $request->has('is_premium');
        $template->is_active = $request->has('is_active');
        $template->slug = Str::slug($validated['name']) . '-' . time();

        // Save HTML/CSS as files (backup)
        $template->html_file_path = $this->saveHtmlFile($validated['html_content'], $template->slug);
        $template->css_file_path = $this->saveCssFile($validated['css_content'], $template->slug);

        // Handle preview image
        if ($request->hasFile('preview_image')) {
            $template->preview_image = $request->file('preview_image')
                ->store('templates/previews', 'public');
        } else {
            // Auto-generate preview if no image provided
            $template->preview_image = $this->generateTemplatePreview($template);
        }

        $template->save();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template created successfully!');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(Template $template)
    {
        $categories = ['professional', 'creative', 'modern', 'classic', 'minimal', 'executive'];
        return view('admin.templates.edit', compact('template', 'categories'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'css_content' => 'required|string',
            'preview_image' => 'nullable|image|max:2048',
            'remove_custom_preview' => 'nullable|boolean',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Update template
        $template->name = $validated['name'];
        $template->category = $validated['category'];
        $template->description = $validated['description'] ?? null;
        $template->html_content = $validated['html_content'];
        $template->css_content = $validated['css_content'];
        $template->is_premium = $request->has('is_premium');
        $template->is_active = $request->has('is_active');

        // Update slug if name changed
        $newSlug = Str::slug($validated['name']);
        if (!str_starts_with($template->slug, $newSlug)) {
            $template->slug = $newSlug . '-' . time();
        }

        // Update HTML/CSS files
        $template->html_file_path = $this->saveHtmlFile($validated['html_content'], $template->slug);
        $template->css_file_path = $this->saveCssFile($validated['css_content'], $template->slug);

        // Handle preview image
        if ($request->has('remove_custom_preview') && $template->preview_image) {
            Storage::disk('public')->delete($template->preview_image);
            $template->preview_image = null;
        } elseif ($request->hasFile('preview_image')) {
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }
            $template->preview_image = $request->file('preview_image')
                ->store('templates/previews', 'public');
        } elseif (!$template->preview_image) {
            // Auto-generate preview if no existing image
            $template->preview_image = $this->generateTemplatePreview($template);
        }

        $template->save();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Template $template)
    {
        // Delete files
        if ($template->preview_image) {
            Storage::disk('public')->delete($template->preview_image);
        }
        if ($template->html_file_path) {
            Storage::disk('public')->delete($template->html_file_path);
        }
        if ($template->css_file_path) {
            Storage::disk('public')->delete($template->css_file_path);
        }

        $template->delete();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    /**
     * Preview template in new window - FIXED VERSION
     */
    public function preview($id)
    {
        $template = Template::findOrFail($id);

        // Log for debugging
        Log::info('Template Preview', [
            'id' => $template->id,
            'name' => $template->name,
            'html_length' => strlen($template->html_content ?? ''),
            'css_length' => strlen($template->css_content ?? ''),
        ]);

        // Check if template has content
        if (empty($template->html_content)) {
            return response('
                <!DOCTYPE html>
                <html>
                <head><title>Preview Error</title></head>
                <body style="font-family: Arial; padding: 40px; text-align: center;">
                    <h1 style="color: #e74c3c;">⚠️ Template Preview Error</h1>
                    <p>This template has no HTML content to display.</p>
                    <p><strong>Template:</strong> ' . htmlspecialchars($template->name) . '</p>
                    <p><a href="' . route('admin.templates.edit', $template->id) . '" style="color: #3498db;">Edit Template</a></p>
                </body>
                </html>
            ', 200)->header('Content-Type', 'text/html');
        }

        // Comprehensive sample data
        $sampleData = $this->getSampleData();

        // Get HTML content
        $html = $template->html_content;

        // Replace all placeholders - try multiple formats
        foreach ($sampleData as $key => $value) {
            // Standard format: {{key}}
            $html = str_replace('{{' . $key . '}}', $value, $html);
            // Alternative format: {{ key }}
            $html = str_replace('{{ ' . $key . ' }}', $value, $html);
            // Uppercase format: {{KEY}}
            $html = str_replace('{{' . strtoupper($key) . '}}', $value, $html);
        }

        // Get CSS content
        $css = $template->css_content ?? '';

        // Build complete HTML document
        $output = $this->buildPreviewHtml($template->name, $css, $html);

        return response($output)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Get comprehensive sample data for preview
     */
    private function getSampleData()
    {
        return [
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
            'picture' => '', // Will be replaced with avatar in fillTemplate

            // Summary/Objective
            'summary' => 'Experienced software engineer with 10+ years of expertise in full-stack development, cloud architecture, and agile methodologies. Proven track record of delivering high-quality solutions and leading cross-functional teams to success. Passionate about building scalable applications and mentoring junior developers.',

            'objective' => 'Seeking a challenging senior developer role where I can leverage my expertise in modern web technologies to build innovative solutions and contribute to team growth.',

            // Experience Section
            'experience' => '
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
                </div>',

            // Education Section
            'education' => '
                <div class="education-item">
                    <div class="degree-header">
                        <h3 class="degree-name">Bachelor of Science in Computer Science</h3>
                        <span class="education-date">2014 - 2018</span>
                    </div>
                    <div class="institution-name">University of California, Berkeley</div>
                    <div class="education-details">
                        <p>GPA: 3.8/4.0 • Dean\'s List all semesters</p>
                        <p>Relevant Coursework: Data Structures, Algorithms, Database Systems, Software Engineering</p>
                    </div>
                </div>
                <div class="education-item">
                    <div class="degree-header">
                        <h3 class="degree-name">High School Diploma</h3>
                        <span class="education-date">2010 - 2014</span>
                    </div>
                    <div class="institution-name">Lincoln High School</div>
                </div>',

            // Skills Section
            'skills' => '
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
                </div>',

            // Certifications
            'certifications' => '
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
                </ul>',

            // Projects
            'projects' => '
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
                </div>',

            // Languages
            'languages' => '
                <ul class="languages-list">
                    <li><strong>English</strong> - Native</li>
                    <li><strong>Spanish</strong> - Fluent</li>
                    <li><strong>French</strong> - Intermediate</li>
                </ul>',

            // Interests/Hobbies
            'interests' => '
                <ul class="interests-list">
                    <li>Open Source Development</li>
                    <li>Tech Blogging</li>
                    <li>Photography</li>
                    <li>Hiking & Travel</li>
                </ul>',
        ];
    }

    /**
     * Build complete preview HTML document
     */
    private function buildPreviewHtml($templateName, $css, $html)
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($templateName) . ' - Preview</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Base styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 0;
            margin: 0;
        }

        /* Common resume elements */
        .resume-container {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 0.5em;
        }

        ul {
            list-style-position: inside;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        /* Template-specific styles */
        ' . $css . '
    </style>
</head>
<body>
' . $html . '
</body>
</html>';
    }

    /**
     * Save HTML content as file
     */
    private function saveHtmlFile($htmlContent, $slug)
    {
        $filename = 'templates/html/' . $slug . '.html';
        Storage::disk('public')->put($filename, $htmlContent);
        return $filename;
    }

    /**
     * Save CSS content as file
     */
    private function saveCssFile($cssContent, $slug)
    {
        $filename = 'templates/css/' . $slug . '.css';
        Storage::disk('public')->put($filename, $cssContent);
        return $filename;
    }

    /**
     * Toggle template active status
     */
    public function toggleActive($id)
    {
        $template = Template::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        return back()->with('success', 'Template status updated successfully!');
    }

    /**
     * Duplicate template
     */
    public function duplicate($id)
    {
        $template = Template::findOrFail($id);

        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->slug = Str::slug($newTemplate->name) . '-' . time();
        $newTemplate->is_active = false; // Duplicates are inactive by default

        // Copy files
        if ($template->html_file_path) {
            $newTemplate->html_file_path = $this->saveHtmlFile($template->html_content, $newTemplate->slug);
        }
        if ($template->css_file_path) {
            $newTemplate->css_file_path = $this->saveCssFile($template->css_content, $newTemplate->slug);
        }

        $newTemplate->save();

        return redirect()
            ->route('admin.templates.edit', $newTemplate->id)
            ->with('success', 'Template duplicated successfully!');
    }

    /**
     * Auto-generate preview image for template using sample data
     *
     * NOTE: This is a placeholder implementation. To actually generate screenshot images,
     * you need to install one of these packages:
     *
     * 1. spatie/browsershot (recommended):
     *    - composer require spatie/browsershot
     *    - Requires Node.js and Puppeteer installed on server
     *    - Usage: Browsershot::html($html)->save($path)
     *
     * 2. intervention/image with headless browser
     *
     * For now, this method creates a simple placeholder image.
     * Replace this with actual screenshot generation when ready.
     */
    private function generateTemplatePreview($template)
    {
        try {
            // Get sample data
            $sampleData = $this->getSampleData();

            // Fill template with sample data
            $htmlContent = $template->html_content;
            $cssContent = $template->css_content ?? '';

            $filledHtml = $this->fillTemplate($htmlContent, $sampleData);

            // Build complete HTML with CSS
            $completeHtml = "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <style>{$cssContent}</style>
</head>
<body>
    {$filledHtml}
</body>
</html>";

            // Create directory if doesn't exist
            $previewDir = public_path('uploads/templates/previews');
            if (!File::exists($previewDir)) {
                File::makeDirectory($previewDir, 0755, true);
            }

            // Generate simple placeholder image using GD
            $filename = $template->slug . '-preview-' . time() . '.png';
            $path = $previewDir . '/' . $filename;

            // Create a simple preview image with template info
            $this->generateSimplePreview($template, $path);

            // Return relative path for database storage
            $relativePath = 'uploads/templates/previews/' . $filename;

            Log::info('Template preview generated', ['template' => $template->slug, 'path' => $relativePath]);

            return $relativePath;

        } catch (\Exception $e) {
            Log::error('Failed to generate template preview', [
                'template' => $template->slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return null - template will save without preview
            return null;
        }
    }

    /**
     * Fill template placeholders with sample data
     */
    private function fillTemplate($html, $data)
    {
        $keys = ['name', 'title', 'email', 'phone', 'address', 'summary', 'experience', 'skills', 'education', 'picture'];

        foreach ($keys as $key) {
            $placeholder = '{{' . $key . '}}';
            $value = $data[$key] ?? '';

            // Handle picture placeholder with avatar
            if ($key === 'picture' && empty($value)) {
                $userName = $data['name'] ?? 'User';
                $initials = strtoupper(substr($userName, 0, 1));
                if (preg_match('/\s+/', $userName)) {
                    $parts = explode(' ', $userName);
                    $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts)-1], 0, 1));
                }
                $value = '<div class="profile-picture profile-avatar" style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; font-family: Arial, sans-serif;">' . $initials . '</div>';
            }

            $html = str_replace($placeholder, $value, $html);
        }

        return $html;
    }

    /**
     * Generate a simple preview image using GD library
     * This creates a card-style preview showing template name and type
     */
    private function generateSimplePreview($template, $path)
    {
        // Image dimensions
        $width = 800;
        $height = 1000;

        // Create image
        $image = imagecreatetruecolor($width, $height);

        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 100, 100, 100);
        $lightGray = imagecolorallocate($image, 240, 240, 240);
        $purple = imagecolorallocate($image, 102, 126, 234);
        $darkPurple = imagecolorallocate($image, 118, 75, 162);

        // Fill background with white
        imagefill($image, 0, 0, $white);

        // Add gradient effect at top (simulate header)
        for ($i = 0; $i < 300; $i++) {
            $color = imagecolorallocatealpha($image, 102, 126, 234, 127 - ($i / 300 * 127));
            imagefilledrectangle($image, 0, $i, $width, $i + 1, $color);
        }

        // Add border
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $gray);

        // Add template name (centered)
        $templateName = strtoupper($template->name);
        $fontSize = 5; // Built-in font size
        $textWidth = imagefontwidth($fontSize) * strlen($templateName);
        $x = ($width - $textWidth) / 2;
        imagestring($image, $fontSize, $x, 50, $templateName, $darkPurple);

        // Add template type
        $category = ucfirst($template->category ?? 'Professional');
        $textWidth2 = imagefontwidth(3) * strlen($category);
        $x2 = ($width - $textWidth2) / 2;
        imagestring($image, 3, $x2, 100, $category, $gray);

        // Add decorative elements (simulate resume sections)
        // Header section
        imagefilledrectangle($image, 40, 200, $width - 40, 280, $lightGray);
        imagestring($image, 3, 50, 220, 'Professional Resume Template', $black);
        imagestring($image, 2, 50, 245, 'Modern design with clean layout', $gray);

        // Content sections (simulate text blocks)
        $sections = [
            ['y' => 320, 'title' => 'EXPERIENCE'],
            ['y' => 480, 'title' => 'EDUCATION'],
            ['y' => 640, 'title' => 'SKILLS'],
        ];

        foreach ($sections as $section) {
            // Section title
            imagestring($image, 4, 50, $section['y'], $section['title'], $purple);

            // Section content lines
            for ($i = 0; $i < 3; $i++) {
                $lineY = $section['y'] + 30 + ($i * 25);
                imagefilledrectangle($image, 50, $lineY, $width - 50, $lineY + 8, $lightGray);
            }
        }

        // Add footer text
        imagestring($image, 2, 50, $height - 40, 'Preview generated on ' . date('M d, Y'), $gray);

        // Save image
        imagepng($image, $path);
        imagedestroy($image);
    }
}
