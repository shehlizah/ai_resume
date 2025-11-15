<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CoverLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoverLetterController extends Controller
{
    /**
     * Display a listing of cover letters.
     */
    public function index()
    {
        $user = Auth::user();
        $coverLetters = $user->coverLetters()
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.cover-letters.index', compact('coverLetters'));
    }

    /**
     * Show the form for creating a new cover letter.
     */
    public function create()
    {
        return view('user.cover-letters.create');
    }

    /**
     * Store a newly created cover letter in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_deleted'] = false;

        CoverLetter::create($validated);

        return redirect()
            ->route('user.cover-letters.index')
            ->with('success', 'Cover letter created successfully!');
    }

    /**
     * Display the specified cover letter.
     */
    public function view(CoverLetter $coverLetter)
    {
        // Debug: Log user and cover letter info
        \Log::info('Cover Letter Access Attempt', [
            'authenticated_user_id' => auth()->id(),
            'cover_letter_id' => $coverLetter->id,
            'cover_letter_user_id' => $coverLetter->user_id,
            'user_email' => auth()->user()?->email,
        ]);

        // Check if cover letter belongs to authenticated user
        if ($coverLetter->user_id !== auth()->id()) {
            \Log::error('Unauthorized access attempt', [
                'authenticated_user_id' => auth()->id(),
                'cover_letter_user_id' => $coverLetter->user_id,
            ]);
            abort(403, 'Unauthorized action.');
        }

        return view('user.cover-letters.view', compact('coverLetter'));
    }

    /**
     * Show the form for editing the specified cover letter.
     */
    public function edit(CoverLetter $coverLetter)
    {
        // Check if cover letter belongs to authenticated user
        if ($coverLetter->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.cover-letters.edit', compact('coverLetter'));
    }

    /**
     * Update the specified cover letter in storage.
     */
    public function update(Request $request, CoverLetter $coverLetter)
    {
        // Check if cover letter belongs to authenticated user
        if ($coverLetter->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $coverLetter->update($validated);

        return redirect()
            ->route('user.cover-letters.view', $coverLetter->id)
            ->with('success', 'Cover letter updated successfully!');
    }

    /**
     * Soft delete the specified cover letter.
     */
    public function destroy(CoverLetter $coverLetter)
    {
        // Check if cover letter belongs to authenticated user
        if ($coverLetter->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $coverLetter->update(['is_deleted' => true]);

        return redirect()
            ->route('user.cover-letters')
            ->with('success', 'Cover letter deleted successfully!');
    }
}
