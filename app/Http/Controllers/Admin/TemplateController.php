<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * Preview template in new window
     */
    public function preview(Template $template)
    {
        // Sample data to fill placeholders
        $sampleData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
             'title' => 'Software Developer',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Main Street, San Francisco, CA 94105',
            'linkedin' => 'linkedin.com/in/johndoe',
            'website' => 'www.johndoe.com',
            'summary' => 'Experienced professional with 10+ years in software development and project management. Proven track record of delivering high-quality solutions and leading cross-functional teams to success.',
            'experience' => '
                <div class="experience-item">
                    <h3>Senior Software Engineer</h3>
                    <div class="company">Tech Company Inc.</div>
                    <div class="date">January 2020 - Present</div>
                    <ul>
                        <li>Led development of microservices architecture serving 1M+ users</li>
                        <li>Mentored team of 5 junior developers</li>
                        <li>Improved system performance by 40% through optimization</li>
                    </ul>
                </div>
                <div class="experience-item">
                    <h3>Software Developer</h3>
                    <div class="company">StartUp LLC</div>
                    <div class="date">June 2018 - December 2019</div>
                    <ul>
                        <li>Developed RESTful APIs using Laravel and Node.js</li>
                        <li>Collaborated with product team on feature planning</li>
                        <li>Implemented automated testing reducing bugs by 60%</li>
                    </ul>
                </div>',
            'education' => '
                <div class="education-item">
                    <h3>Bachelor of Science in Computer Science</h3>
                    <div class="institution">University of California, Berkeley</div>
                    <div class="date">2014 - 2018</div>
                    <p>GPA: 3.8/4.0, Dean\'s List all semesters</p>
                </div>',
            'skills' => '
                <ul class="skills-list">
                    <li>JavaScript / TypeScript</li>
                    <li>Python</li>
                    <li>React / Vue.js</li>
                    <li>Node.js</li>
                    <li>Laravel / PHP</li>
                    <li>Docker / Kubernetes</li>
                    <li>AWS / Cloud Services</li>
                    <li>MySQL / PostgreSQL</li>
                </ul>',
            'certifications' => '
                <ul>
                    <li>AWS Certified Solutions Architect - Professional</li>
                    <li>Google Cloud Professional Developer</li>
                    <li>Certified Scrum Master (CSM)</li>
                </ul>',
            'projects' => '
                <ul>
                    <li>Open-source contributor to Laravel framework</li>
                    <li>Built personal portfolio showcasing 10+ projects</li>
                    <li>Developed mobile app with 50K+ downloads</li>
                </ul>'
        ];
$sampleData2 = [
  'name' => 'John Doe',
  'email' => 'john@example.com',
    'title' => 'Software Developer',
  'phone' => '+1 555 987 654',
  'address' => 'New York, NY',
  'summary' => 'Results-driven software engineer with 8+ years of experience developing scalable web applications and leading agile teams.',

  'experience' => '
    <div class="experience-item">
      <h3>Lead Developer</h3>
      <p class="company">TechCorp | 2020 - Present</p>
      <ul>
        <li>Architected a cloud-based CRM system serving 100K users.</li>
        <li>Led a 6-member development team with Agile methodology.</li>
      </ul>
    </div>
    <div class="experience-item">
      <h3>Software Engineer</h3>
      <p class="company">InnovateX | 2017 - 2020</p>
      <ul>
        <li>Developed RESTful APIs and implemented caching for performance.</li>
      </ul>
    </div>
  ',

  'education' => '
    <div class="education-item">
      <h3>BSc Computer Science</h3>
      <p class="school">MIT | 2013 - 2017</p>
    </div>
  ',

  'skills' => '
    <span class="pill">JavaScript</span>
    <span class="pill">React</span>
    <span class="pill">Node.js</span>
    <span class="pill">MySQL</span>
    <span class="pill">Docker</span>
  ',
];

        // Replace placeholders in HTML
        $html = $template->html_content;
        foreach ($sampleData as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }

        // Build complete HTML document
        $output = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($template->name) . ' - Preview</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        body { 
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            padding: 20px;
            background: #ffffff;
            color: #333;
        }
        ' . $template->css_content . '
    </style>
</head>
<body>
' . $html . '
</body>
</html>';

        return response($output)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Content-Type-Options', 'nosniff');
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
}