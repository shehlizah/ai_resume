<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class AdminJobController extends Controller
{
    /**
     * Show user activity for job searches
     */
    public function userActivity()
    {
        // Get users with their job search activity
        $users = User::select('users.*')
            ->withCount(['resumes'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get overall statistics
        $stats = [
            'total_users' => User::count(),
            'users_with_resumes' => User::has('resumes')->count(),
            'total_resumes' => \App\Models\UserResume::count(),
            'active_today' => User::whereDate('created_at', today())->count(),
        ];

        return view('admin.jobs.user-activity', compact('users', 'stats'));
    }

    /**
     * Show API settings for job sources
     */
    public function apiSettings()
    {
        // Get current API settings from database
        $settings = [
            'openai_enabled' => config('services.openai.enabled', true),
            'openai_key' => config('services.openai.key') ? '••••••••' . substr(config('services.openai.key'), -4) : 'Not set',
            'job_limit_free' => SystemSetting::get('job_limit_free', 5),
            'job_limit_premium' => SystemSetting::get('job_limit_premium', 8),
            'session_limit_enabled' => SystemSetting::get('session_limit_enabled', true),
        ];

        return view('admin.jobs.api-settings', compact('settings'));
    }

    /**
     * Update API settings
     */
    public function updateApiSettings(Request $request)
    {
        $request->validate([
            'openai_enabled' => 'required|boolean',
            'job_limit_free' => 'required|integer|min:1|max:20',
            'job_limit_premium' => 'required|integer|min:1|max:50',
            'session_limit_enabled' => 'required|boolean',
        ]);

        // Save settings to database
        SystemSetting::set('job_limit_free', $request->job_limit_free, 'integer', 'jobs');
        SystemSetting::set('job_limit_premium', $request->job_limit_premium, 'integer', 'jobs');
        SystemSetting::set('session_limit_enabled', $request->session_limit_enabled, 'boolean', 'jobs');

        return redirect()->route('admin.jobs.api-settings')
            ->with('success', 'API settings updated successfully');
    }

    /**
     * Get job search statistics
     */
    public function statistics()
    {
        $stats = [
            'searches_today' => 0, // Would track in a job_searches table
            'searches_this_week' => 0,
            'searches_this_month' => 0,
            'most_searched_locations' => [],
            'most_searched_titles' => [],
        ];

        return view('admin.jobs.statistics', compact('stats'));
    }
}
