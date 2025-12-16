<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function show(Job $job)
    {
        return view('jobs.apply', compact('job'));
    }

    public function store(Request $request, Job $job)
    {
        $data = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'nullable|string|max:50',
            'resume_url' => 'nullable|url|max:500',
            'cover_letter' => 'nullable|string',
        ]);

        JobApplication::create([
            'job_id' => $job->id,
            'user_id' => Auth::id(),
            'applicant_name' => $data['applicant_name'],
            'applicant_email' => $data['applicant_email'],
            'applicant_phone' => $data['applicant_phone'] ?? null,
            'resume_url' => $data['resume_url'] ?? null,
            'cover_letter' => $data['cover_letter'] ?? null,
        ]);

        return redirect()->route('jobs.apply.show', $job)->with('success', 'Application submitted successfully.');
    }
}
