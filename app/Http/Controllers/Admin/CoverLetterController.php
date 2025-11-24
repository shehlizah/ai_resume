<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoverLetter;
use App\Models\CoverLetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoverLetterController extends Controller
{
    // ========================================
    // DASHBOARD
    // ========================================
    
    /**
     * Display dashboard/overview
     */
    public function index()
    {
        $stats = [
            'total_cover_letters' => CoverLetter::count(),
            'active_cover_letters' => CoverLetter::where('is_deleted', false)->count(),
            'deleted_cover_letters' => CoverLetter::where('is_deleted', true)->count(),
            'total_users' => CoverLetter::distinct('user_id')->count('user_id'),
            'total_templates' => CoverLetterTemplate::count(),
            'active_templates' => CoverLetterTemplate::where('is_active', true)->count(),
        ];

        $recentCoverLetters = CoverLetter::with('user')
            ->where('is_deleted', false)
            ->latest()
            ->take(10)
            ->get();

        $topUsers = CoverLetter::select('user_id', DB::raw('count(*) as cover_letter_count'))
            ->where('is_deleted', false)
            ->groupBy('user_id')
            ->orderByDesc('cover_letter_count')
            ->take(5)
            ->with('user')
            ->get();

        return view('admin.cover-letters.index', compact('stats', 'recentCoverLetters', 'topUsers'));
    }

    // ========================================
    // USER COVER LETTERS MANAGEMENT
    // ========================================
    
    /**
     * Display all user cover letters
     */
    public function userCoverLetters(Request $request)
    {
        $query = CoverLetter::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_deleted', false);
            } elseif ($request->status === 'deleted') {
                $query->where('is_deleted', true);
            }
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $coverLetters = $query->latest()->paginate(20);

        return view('admin.cover-letters.user-cover-letters', compact('coverLetters'));
    }

    /**
     * View specific cover letter
     */
    public function viewCoverLetter(CoverLetter $coverLetter)
    {
        $coverLetter->load('user');

        return view('admin.cover-letters.view-cover-letter', compact('coverLetter'));
    }

    /**
     * Delete user cover letter (soft delete)
     */
    public function deleteCoverLetter(CoverLetter $coverLetter)
    {
        $coverLetter->update(['is_deleted' => true]);

        return redirect()->route('admin.cover-letters.user-cover-letters')
            ->with('success', 'Cover letter deleted successfully!');
    }

    /**
     * Permanently delete cover letter
     */
    public function permanentDelete(CoverLetter $coverLetter)
    {
        $coverLetter->delete();

        return redirect()->route('admin.cover-letters.user-cover-letters')
            ->with('success', 'Cover letter permanently deleted!');
    }

    /**
     * Restore deleted cover letter
     */
    public function restore(CoverLetter $coverLetter)
    {
        $coverLetter->update(['is_deleted' => false]);

        return back()->with('success', 'Cover letter restored successfully!');
    }

    // ========================================
    // TEMPLATES MANAGEMENT
    // ========================================
    
    /**
     * Display all templates
     */
    public function templates()
    {
        $templates = CoverLetterTemplate::orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total' => $templates->count(),
            'active' => $templates->where('is_active', true)->count(),
            'inactive' => $templates->where('is_active', false)->count(),
        ];
        
        return view('admin.cover-letters.templates.index', compact('templates', 'stats'));
    }

    /**
 * Show template details/preview
 */
public function showTemplate(CoverLetterTemplate $template)
{
    return view('admin.cover-letters.templates.show', compact('template'));
}

    /**
     * Show create template form
     */
    public function createTemplate()
    {
        return view('admin.cover-letters.templates.create');
    }

    /**
     * Store new template
     */
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Auto-set sort order if not provided
        if (!isset($validated['sort_order'])) {
            $maxOrder = CoverLetterTemplate::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        CoverLetterTemplate::create($validated);

        return redirect()->route('admin.cover-letters.templates')
            ->with('success', 'Template created successfully!');
    }

    /**
     * Show edit template form
     */
    public function editTemplate(CoverLetterTemplate $template)
    {
        return view('admin.cover-letters.templates.edit', compact('template'));
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, CoverLetterTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.cover-letters.templates')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Delete template
     */
    public function deleteTemplate(CoverLetterTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.cover-letters.templates')
            ->with('success', 'Template deleted successfully!');
    }

    /**
     * Toggle template active status
     */
    public function toggleTemplateStatus(CoverLetterTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Template {$status} successfully!");
    }

    /**
     * Duplicate template
     */
    public function duplicateTemplate(CoverLetterTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->sort_order = (CoverLetterTemplate::max('sort_order') ?? 0) + 1;
        $newTemplate->save();

        return back()->with('success', 'Template duplicated successfully!');
    }

    /**
     * Bulk actions for templates
     */
    public function bulkTemplateAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'template_ids' => 'required|array',
            'template_ids.*' => 'exists:cover_letter_templates,id',
        ]);

        $templates = CoverLetterTemplate::whereIn('id', $request->template_ids);

        switch ($request->action) {
            case 'activate':
                $templates->update(['is_active' => true]);
                $message = 'Templates activated successfully!';
                break;
            
            case 'deactivate':
                $templates->update(['is_active' => false]);
                $message = 'Templates deactivated successfully!';
                break;
            
            case 'delete':
                $templates->delete();
                $message = 'Templates deleted successfully!';
                break;
        }

        return back()->with('success', $message);
    }

    // ========================================
    // STATISTICS & REPORTS
    // ========================================
    
    /**
     * Get cover letters statistics
     */
    public function statistics()
    {
        $stats = [
            'total_cover_letters' => CoverLetter::count(),
            'active_cover_letters' => CoverLetter::where('is_deleted', false)->count(),
            'deleted_cover_letters' => CoverLetter::where('is_deleted', true)->count(),
            'total_users' => CoverLetter::distinct('user_id')->count('user_id'),
            'total_templates' => CoverLetterTemplate::count(),
            'active_templates' => CoverLetterTemplate::where('is_active', true)->count(),
        ];

        // Monthly statistics
        $monthlyStats = CoverLetter::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('is_deleted', false)
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Template usage statistics
        $templateUsage = CoverLetterTemplate::withCount(['coverLetters' => function($query) {
            $query->where('is_deleted', false);
        }])
        ->orderByDesc('cover_letters_count')
        ->get();

        return view('admin.cover-letters.statistics', compact('stats', 'monthlyStats', 'templateUsage'));
    }

    // ========================================
    // EXPORT FUNCTIONS
    // ========================================
    
    /**
     * Export cover letters to CSV
     */
    public function exportCoverLetters(Request $request)
    {
        $query = CoverLetter::with('user');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_deleted', false);
            } elseif ($request->status === 'deleted') {
                $query->where('is_deleted', true);
            }
        }

        $coverLetters = $query->get();

        $filename = 'cover-letters-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($coverLetters) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['ID', 'User', 'Email', 'Title', 'Company', 'Recipient', 'Status', 'Created At']);
            
            // Data rows
            foreach ($coverLetters as $letter) {
                fputcsv($file, [
                    $letter->id,
                    $letter->user->name,
                    $letter->user->email,
                    $letter->title,
                    $letter->company_name,
                    $letter->recipient_name,
                    $letter->is_deleted ? 'Deleted' : 'Active',
                    $letter->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export templates to CSV
     */
    public function exportTemplates()
    {
        $templates = CoverLetterTemplate::all();

        $filename = 'cover-letter-templates-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($templates) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['ID', 'Name', 'Description', 'Status', 'Sort Order', 'Created At']);
            
            // Data rows
            foreach ($templates as $template) {
                fputcsv($file, [
                    $template->id,
                    $template->name,
                    $template->description,
                    $template->is_active ? 'Active' : 'Inactive',
                    $template->sort_order,
                    $template->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}