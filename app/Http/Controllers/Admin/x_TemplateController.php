<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates
     */
    public function index()
    {
        $title = 'All Templates';
        $templates = Template::latest()->get();
        return view('admin.templates.index', compact('templates', 'title'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $title = 'Add New Template';
        // $categories = Template::select('category')
        //     ->distinct()
        //     ->pluck('category')
        //     ->filter()
        //     ->values()
        //     ->toArray();
        
        // if (empty($categories)) {
            $categories = ['professional', 'modern', 'creative', 'minimal', 'executive'];
        // }
        
        return view('admin.templates.create', compact('title', 'categories'));
    }

    /**
     * Store a newly created template
     */
     
     public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:templates,name',
        'category' => 'required|string|max:100',
        'description' => 'nullable|string',
        'template_type' => 'nullable|in:html,pdf',
        'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
        'template_file' => 'nullable|file|mimes:html,htm',
        'css_file' => 'nullable|file|mimes:css',
        'preview_image' => 'nullable|image|max:2048',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ]);

    // ✅ Generate SAFE unique slug
    $slug = Str::slug($validated['name']);
    if (empty($slug)) {
        $slug = Str::random(12);
    }
    // Prevent duplicate slug
    if (Template::where('slug', $slug)->exists()) {
        $slug .= '-' . time();
    }

    $templateData = [
        'name' => $validated['name'],
        'slug' => $slug, // ✅ REQUIRED FIX
        'category' => $validated['category'],
        'description' => $validated['description'],
        'template_type' => $request->input('template_type', 'html'),
        'is_premium' => $request->boolean('is_premium'),
        'is_active' => $request->boolean('is_active', true),
        'sort_order' => Template::max('sort_order') + 10,
        'version' => '1.0',
    ];

    // ✅ Handle PDF upload
    if ($request->hasFile('pdf_file')) {
        $templateData['pdf_file'] = $request->file('pdf_file')->store('templates', 'public');
        $templateData['template_type'] = 'pdf';

        // Auto generate PDF preview only if preview_image NOT given
        if (!$request->hasFile('preview_image')) {
            $autoPreview = $this->generatePreviewFromPdf($templateData['pdf_file']);
            if ($autoPreview) {
                $templateData['preview_image'] = $autoPreview;
            }
        }
    }

    // ✅ Handle HTML upload
    if ($request->hasFile('template_file')) {
        $templateData['template_file'] = $request->file('template_file')->store('templates', 'local');
    }

    if ($request->hasFile('css_file')) {
        $templateData['css_file'] = $request->file('css_file')->store('templates', 'local');
    }

    // ✅ Custom preview image override
    if ($request->hasFile('preview_image')) {
        $templateData['preview_image'] = $request->file('preview_image')->store('templates/previews', 'public');
    }

    // ✅ Create template
    Template::create($templateData);

    return redirect()->route('admin.templates.index')
        ->with('success', 'Template created successfully!');
}

    public function x_store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:templates,name',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'template_type' => 'nullable|in:html,pdf',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'template_file' => 'nullable|file|mimes:html,htm',
            'css_file' => 'nullable|file|mimes:css',
            'preview_image' => 'nullable|image|max:2048',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $templateData = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'template_type' => $request->input('template_type', 'html'),
            'is_premium' => $request->boolean('is_premium'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => Template::max('sort_order') + 10,
            'version' => '1.0',
        ];

        // Handle PDF upload
        if ($request->hasFile('pdf_file')) {
            $templateData['pdf_file'] = $request->file('pdf_file')->store('templates', 'public');
            $templateData['template_type'] = 'pdf';
            
            // ðŸŽ¨ AUTO-GENERATE PREVIEW FROM PDF (unless custom image uploaded)
            if (!$request->hasFile('preview_image')) {
                $autoPreview = $this->generatePreviewFromPdf($templateData['pdf_file']);
                if ($autoPreview) {
                    $templateData['preview_image'] = $autoPreview;
                }
            }
        }

        // Handle HTML upload
        if ($request->hasFile('template_file')) {
            $templateData['template_file'] = $request->file('template_file')->store('templates', 'local');
        }
        
        if ($request->hasFile('css_file')) {
            $templateData['css_file'] = $request->file('css_file')->store('templates', 'local');
        }

        // Handle preview image (custom override)
        if ($request->hasFile('preview_image')) {
            $templateData['preview_image'] = $request->file('preview_image')->store('templates/previews', 'public');
        }

        Template::create($templateData);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template created successfully!');
    }


    public function edit($id)
    {
        $title = 'Edit Template';
        $template = Template::findOrFail($id);

        $html = '';
        $css = '';
        $pdfUrl = '';

         
        $categories = Template::select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();
        
        if (empty($categories)) {
            $categories = ['professional', 'modern', 'creative', 'minimal', 'executive'];
        }
        
        if ($template->template_type === 'pdf') {
            if ($template->pdf_file && Storage::disk('public')->exists($template->pdf_file)) {
                $pdfUrl = asset('storage/' . $template->pdf_file);
            }
        }

        return view('admin.templates.edit', compact('title','template', 'categories','html', 'css', 'pdfUrl'));
    }


    public function preview($id)
    {
        $template = Template::findOrFail($id);
        $html = '';
        $css = '';
        $pdfUrl = '';
        
        // if ($template->template_type === 'pdf') {
            
        //       dd(Storage::disk('public')->files('templates'));

        //     if ($template->pdf_file && Storage::disk('public')->exists($template->pdf_file)) {

        //         $pdfUrl = asset('storage/' . $template->pdf_file);
                


        //     } else {
        //         Log::warning("PDF file not found: {$template->pdf_file}");
        //     }
        // }
        
        if ($template->template_type === 'pdf') {
        $path = $template->pdf_file;
        Log::info("Checking path: $path");
    
        if ($path && Storage::disk('public')->exists($path)) {
            $pdfUrl = asset('storage/' . ltrim($path, '/'));
            Log::info("✅ PDF found at: $pdfUrl");
        } else {
            Log::warning("⚠️ PDF not found. Expected at: storage/app/public/$path");
        }
    }



        return view('admin.templates.preview', compact('template', 'html', 'css', 'pdfUrl'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:templates,name,' . $id,
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'template_file' => 'nullable|file|mimes:html,htm',
            'css_file' => 'nullable|file|mimes:css',
            'preview_image' => 'nullable|image|max:2048',
            'remove_custom_preview' => 'boolean',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
            'html_code' => 'nullable|string',
            'css_code' => 'nullable|string',
        ]);

        // Update basic fields
        $template->name = $validated['name'];
        $template->category = $validated['category'];
        $template->description = $validated['description'];
        $template->is_premium = $request->boolean('is_premium');
        $template->is_active = $request->boolean('is_active');

        // Handle PDF replacement
        if ($request->hasFile('pdf_file')) {
            // Delete old PDF
            if ($template->pdf_file) {
                Storage::disk('public')->delete($template->pdf_file);
            }
            
            // Store new PDF
            $template->pdf_file = $request->file('pdf_file')->store('templates', 'public');
            $template->template_type = 'pdf';
            
            // ðŸŽ¨ AUTO-REGENERATE PREVIEW FROM NEW PDF
            // Delete old preview
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }
            
            // Generate new preview
            $autoPreview = $this->generatePreviewFromPdf($template->pdf_file);
            if ($autoPreview) {
                $template->preview_image = $autoPreview;
            }
        }

        // Handle custom preview image (override auto-generated)
        if ($request->hasFile('preview_image')) {
            // Delete old preview
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }
            // Store new custom preview
            $template->preview_image = $request->file('preview_image')->store('templates/previews', 'public');
        }
        
        // Handle "Remove custom preview" checkbox
        if ($request->boolean('remove_custom_preview') && $template->pdf_file) {
            // Delete custom preview
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }
            // Regenerate from PDF
            $autoPreview = $this->generatePreviewFromPdf($template->pdf_file);
            if ($autoPreview) {
                $template->preview_image = $autoPreview;
            }
        }

        // Handle HTML code from editor
        if ($request->filled('html_code')) {
            if ($template->template_file) {
                Storage::put($template->template_file, $request->html_code);
            } else {
                $htmlFilename = 'templates/' . str_replace(' ', '_', strtolower($validated['name'])) . '_' . time() . '.html';
                Storage::put($htmlFilename, $request->html_code);
                $template->template_file = $htmlFilename;
            }
        }

        // Handle CSS code from editor
        if ($request->filled('css_code')) {
            if ($template->css_file) {
                Storage::put($template->css_file, $request->css_code);
            } else {
                $cssFilename = 'templates/' . str_replace(' ', '_', strtolower($validated['name'])) . '_' . time() . '.css';
                Storage::put($cssFilename, $request->css_code);
                $template->css_file = $cssFilename;
            }
        }

        // Handle HTML file replacement
        if ($request->hasFile('template_file')) {
            if ($template->template_file) {
                Storage::delete($template->template_file);
            }
            $template->template_file = $request->file('template_file')->store('templates', 'local');
        }

        // Handle CSS file replacement
        if ($request->hasFile('css_file')) {
            if ($template->css_file) {
                Storage::delete($template->css_file);
            }
            $template->css_file = $request->file('css_file')->store('templates', 'local');
        }

        $template->save();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template
     */
    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        
        // Delete associated files
        if ($template->template_file) {
            Storage::delete($template->template_file);
        }
        if ($template->css_file) {
            Storage::delete($template->css_file);
        }
        if (isset($template->pdf_file) && $template->pdf_file) {
            Storage::disk('public')->delete($template->pdf_file);
        }
        if ($template->preview_image) {
            Storage::disk('public')->delete($template->preview_image);
        }
        
        $template->delete();
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    /**
     * Toggle template active status
     */
    public function toggleActive($id)
    {
        $template = Template::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        $status = $template->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Template {$status} successfully!");
    }

    /**
     * Duplicate a template
     */
    public function duplicate($id)
    {
        $original = Template::findOrFail($id);
        
        // Copy HTML file
        $newHtmlFile = null;
        if ($original->template_file && Storage::exists($original->template_file)) {
            $htmlContent = Storage::get($original->template_file);
            $newHtmlFile = 'templates/' . str_replace(' ', '_', strtolower($original->name)) . '_copy_' . time() . '.html';
            Storage::put($newHtmlFile, $htmlContent);
        }

        // Copy CSS file
        $newCssFile = null;
        if ($original->css_file && Storage::exists($original->css_file)) {
            $cssContent = Storage::get($original->css_file);
            $newCssFile = 'templates/' . str_replace(' ', '_', strtolower($original->name)) . '_copy_' . time() . '.css';
            Storage::put($newCssFile, $cssContent);
        }

        // Copy PDF file if exists
        $newPdfFile = null;
        if (isset($original->pdf_file) && $original->pdf_file && Storage::disk('public')->exists($original->pdf_file)) {
            $extension = pathinfo($original->pdf_file, PATHINFO_EXTENSION);
            $newPdfFile = 'templates/' . str_replace(' ', '_', strtolower($original->name)) . '_copy_' . time() . '.' . $extension;
            Storage::disk('public')->copy($original->pdf_file, $newPdfFile);
        }

        // Copy preview image
        $newPreviewImage = null;
        if ($original->preview_image && Storage::disk('public')->exists($original->preview_image)) {
            $extension = pathinfo($original->preview_image, PATHINFO_EXTENSION);
            $newPreviewImage = 'templates/previews/' . str_replace(' ', '_', strtolower($original->name)) . '_copy_' . time() . '.' . $extension;
            Storage::disk('public')->copy($original->preview_image, $newPreviewImage);
        }

        // Create duplicate
        $duplicate = Template::create([
            'name' => $original->name . ' (Copy)',
            'category' => $original->category,
            'description' => $original->description,
            'template_file' => $newHtmlFile,
            'css_file' => $newCssFile,
            'pdf_file' => $newPdfFile,
            'template_type' => $original->template_type ?? 'html',
            'preview_image' => $newPreviewImage,
            'is_premium' => $original->is_premium,
            'is_active' => false,
            'sort_order' => Template::max('sort_order') + 10,
            'features' => $original->features,
            'version' => $original->version ?? '1.0',
        ]);

        return redirect()->route('admin.templates.edit', $duplicate->id)
            ->with('success', 'Template duplicated successfully!');
    }

    /**
     * Replace placeholders with sample data for preview
     */
    private function replacePlaceholders($html)
    {
        $sampleData = [
            '{{name}}' => 'John Michael Anderson',
            '{{email}}' => 'john.anderson@example.com',
            '{{phone}}' => '+1 (555) 123-4567',
            '{{location}}' => 'New York, NY 10001',
            '{{job_title}}' => 'Senior Software Engineer',
            '{{title}}' => 'Senior Software Engineer',
            '{{summary}}' => 'Experienced software engineer with 8+ years of expertise in full-stack development.',
            '{{company}}' => 'Tech Innovations Inc.',
            '{{start_date}}' => 'Jan 2020',
            '{{end_date}}' => 'Present',
            '{{degree}}' => 'Bachelor of Science in Computer Science',
            '{{institution}}' => 'MIT',
            '{{graduation_year}}' => '2016',
            '{{skills}}' => 'JavaScript, React, Node.js, Python, AWS',
        ];
        
        foreach ($sampleData as $placeholder => $value) {
            $html = str_replace($placeholder, $value, $html);
        }
        
        return $html;
    }

    /**
     * ðŸŽ¨ Generate preview image from PDF first page
     */
 private function generatePreviewFromPdf($pdfPath)
{
    try {
        $fullPdfPath = storage_path('app/public/' . $pdfPath);

        if (!file_exists($fullPdfPath)) {
            Log::error("PDF file missing: {$fullPdfPath}");
            return null;
        }

        // Preview file
        $previewFilename = 'preview_' . time() . '_' . uniqid() . '.jpg';
        $previewPath = 'templates/previews/' . $previewFilename;
        $fullPreviewPath = storage_path('app/public/' . $previewPath);

        // Create directory if missing
        if (!file_exists(dirname($fullPreviewPath))) {
            mkdir(dirname($fullPreviewPath), 0755, true);
        }

        // ✅ USE IMAGICK DIRECTLY (WORKS ON NAMECHEAP)
        $imagick = new \Imagick();

        // Read first page of PDF
        $imagick->setResolution(150, 150); 
        $imagick->readImage($fullPdfPath."[0]");

        // Set output format
        $imagick->setImageFormat('jpg');
        $imagick->setImageCompressionQuality(80);

        // Save JPEG preview
        $imagick->writeImage($fullPreviewPath);
        $imagick->clear();
        $imagick->destroy();

        Log::info("✅ PDF preview created at: {$previewPath}");
        return $previewPath;

    } catch (\Exception $e) {
        Log::error("❌ Imagick PDF preview failed: " . $e->getMessage());
        return null;
    }
}


    private function old_generatePreviewFromPdf($pdfPath)
    {
        try {
            // PDF is in public disk (storage/app/public/)
            $fullPdfPath = storage_path('app/public/' . $pdfPath);
            
            // Check if file exists
            if (!file_exists($fullPdfPath)) {
                Log::error("PDF file not found: {$fullPdfPath}");
                return null;
            }
            
            // Generate unique filename for preview (store in public disk for easy access)
            $previewFilename = 'preview_' . time() . '_' . uniqid() . '.jpg';
            $previewPath = 'templates/previews/' . $previewFilename;
            $fullPreviewPath = storage_path('app/public/' . $previewPath);

            // Ensure preview directory exists
            $previewDir = dirname($fullPreviewPath);
            if (!file_exists($previewDir)) {
                mkdir($previewDir, 0755, true);
            }

            // Convert PDF first page to image
            $pdf = new Pdf($fullPdfPath);
            $pdf->setPage(1)
                ->setResolution(150) // Good quality for thumbnails
                ->saveImage($fullPreviewPath);

            Log::info("Preview image generated successfully: {$previewPath}");
            return $previewPath;

        } catch (\Exception $e) {
            // Log error and return null
            Log::error('PDF to Image conversion failed: ' . $e->getMessage());
            return null;
        }
    }
}