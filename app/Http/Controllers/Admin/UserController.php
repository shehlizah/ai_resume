<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserResume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $title = 'User Management';
        
        $query = User::withCount('resumes');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
        
        // Filter by role (if you have roles)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $users = $query->paginate(20);
        
        // Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'total_resumes' => UserResume::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::where('created_at', '>=', now()->subWeek())->count(),
        ];
        
        return view('admin.users.index', compact('title', 'users', 'stats'));
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        $title = 'User Details';
        $user = User::withCount('resumes')->findOrFail($id);
        
        // Get user's resumes with template info
        $resumes = UserResume::where('user_id', $id)
            ->with('template')
            ->latest()
            ->paginate(10);
        
        return view('admin.users.show', compact('title', 'user', 'resumes'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $title = 'Create New User';
        return view('admin.users.create', compact('title'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'nullable|string|in:user,admin',
            'is_active' => 'boolean'
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(), // Auto-verify admin created users
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $title = 'Edit User';
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('title', 'user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'role' => 'nullable|string|in:user,admin',
            'is_active' => 'boolean'
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        
        if (isset($validated['role'])) {
            $user->role = $validated['role'];
        }
        
        $user->is_active = $validated['is_active'] ?? $user->is_active;
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account!');
        }
        
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()->with('success', "User {$status} successfully!");
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }
        
        // Delete user's resumes and PDF files
        $resumes = UserResume::where('user_id', $id)->get();
        foreach ($resumes as $resume) {
            if ($resume->generated_pdf_path) {
                \Storage::disk('public')->delete($resume->generated_pdf_path);
            }
            $resume->delete();
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User and all their data deleted successfully!');
    }

    /**
     * Delete user's specific resume
     */
    public function deleteResume($userId, $resumeId)
    {
        $resume = UserResume::where('id', $resumeId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        // Delete PDF file
        if ($resume->generated_pdf_path) {
            \Storage::disk('public')->delete($resume->generated_pdf_path);
        }
        
        $resume->delete();
        
        return redirect()->back()->with('success', 'Resume deleted successfully!');
    }

    /**
     * Download user's resume
     */
    public function downloadResume($userId, $resumeId)
    {
        $resume = UserResume::where('id', $resumeId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        if (!$resume->generated_pdf_path) {
            return redirect()->back()->with('error', 'PDF not found');
        }
        
        $filePath = storage_path('app/public/' . $resume->generated_pdf_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'PDF file not found on server');
        }
        
        $fileName = $resume->data['name'] ?? 'Resume';
        $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fileName);
        $fileName .= '_Resume.pdf';
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);
        
        $userIds = $validated['user_ids'];
        
        // Remove current admin from bulk actions
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->id();
        });
        
        switch ($validated['action']) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = count($userIds) . ' users activated successfully!';
                break;
                
            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = count($userIds) . ' users deactivated successfully!';
                break;
                
            case 'delete':
                // Delete resumes and PDFs first
                $resumes = UserResume::whereIn('user_id', $userIds)->get();
                foreach ($resumes as $resume) {
                    if ($resume->generated_pdf_path) {
                        \Storage::disk('public')->delete($resume->generated_pdf_path);
                    }
                }
                UserResume::whereIn('user_id', $userIds)->delete();
                User::whereIn('id', $userIds)->delete();
                $message = count($userIds) . ' users deleted successfully!';
                break;
        }
        
        return redirect()->route('admin.users.index')->with('success', $message);
    }
}