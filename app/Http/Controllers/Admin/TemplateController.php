<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
}