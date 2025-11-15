<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\TemplateController;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TemplateStarterController extends Controller
{
    /**
     * API: List all starter templates
     */
    public function list()
    {
        try {
            $templates = Template::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function($template) {
                    $previewUrl = $template->preview_image 
                        ? asset('storage/' . $template->preview_image)
                        : asset('images/template-placeholder.jpg');
                    
                    return [
                        'id' => $template->id,
                        'name' => $template->name,
                        'slug' => $template->slug,
                        'category' => ucfirst($template->category ?? 'general'),
                        'description' => $template->description ?? 'Professional resume template',
                        'preview_url' => $previewUrl,
                        'is_premium' => (bool) $template->is_premium,
                        'features' => is_array($template->features) ? $template->features : [],
                    ];
                });

            return response()->json(['templates' => $templates]);
            
        } catch (\Exception $e) {
            Log::error('Starter templates list error: ' . $e->getMessage());
            
            return response()->json([
                'templates' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get HTML and CSS content for preview
     */
    public function getContent($id)
    {
        try {
            $template = Template::findOrFail($id);
            
            return response()->json([
                'html' => $template->getHtmlContent(),
                'css' => $template->getCssContent(),
                'name' => $template->name,
                'description' => $template->description
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Clone a starter template
     */
    public function clone(Request $request, $id)
    {
        $starter = Template::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'customize' => 'boolean'
        ]);

        $newTemplate = $starter->replicate();
        $newTemplate->name = $request->name;
        $newTemplate->slug = Str::slug($request->name);
        $newTemplate->is_active = false;
        
        if ($starter->template_file) {
            $newHtmlPath = 'templates/html/' . $newTemplate->slug . '.html';
            Storage::copy($starter->template_file, $newHtmlPath);
            $newTemplate->template_file = $newHtmlPath;
        }

        if ($starter->css_file) {
            $newCssPath = 'templates/css/' . $newTemplate->slug . '.css';
            Storage::copy($starter->css_file, $newCssPath);
            $newTemplate->css_file = $newCssPath;
        }

        if ($starter->preview_image) {
            $extension = pathinfo($starter->preview_image, PATHINFO_EXTENSION);
            $newImagePath = 'templates/previews/' . $newTemplate->slug . '.' . $extension;
            Storage::disk('public')->copy($starter->preview_image, $newImagePath);
            $newTemplate->preview_image = $newImagePath;
        }

        $newTemplate->save();

        if ($request->customize) {
            return redirect()->route('admin.templates.edit', $newTemplate->id)
                ->with('success', 'Template cloned successfully! Customize it now.');
        }

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template "' . $newTemplate->name . '" cloned successfully!');
    }
}