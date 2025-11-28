<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;

class JobFinderController extends Controller
{
    /**
     * Show recommended jobs
     */
    public function recommended()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';
        $jobsViewed = session('jobs_viewed', 0);
        $jobsApplied = session('jobs_applied', 0);

        return view('user.jobs.recommended', compact(
            'user',
            'subscription',
            'hasPremiumAccess',
            'jobsViewed',
            'jobsApplied'
        ));
    }

    /**
     * Generate recommended jobs using AI
     */
    public function generateRecommended(Request $request)
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';

        // Free tier: 5 jobs view per session
        $jobsViewed = session('jobs_viewed', 0);

        if (!$hasPremiumAccess && $jobsViewed >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Free plan limit reached. View 5 jobs per session. Upgrade to Pro for unlimited access.',
                'redirect' => route('user.pricing')
            ], 403);
        }

        // TODO: Integrate OpenAI service to generate job recommendations
        // For now, return mock data
        $jobs = [
            [
                'id' => 1,
                'title' => 'Senior Laravel Developer',
                'company' => 'Tech Solutions Inc.',
                'location' => 'New York, NY',
                'salary' => '$120,000 - $160,000',
                'description' => 'Looking for an experienced Laravel developer to lead our backend team.',
                'match_score' => 95,
                'apply_url' => '#'
            ],
            [
                'id' => 2,
                'title' => 'PHP Web Developer',
                'company' => 'Digital Agency Co.',
                'location' => 'Remote',
                'salary' => '$80,000 - $120,000',
                'description' => 'Join our creative team building web applications.',
                'match_score' => 88,
                'apply_url' => '#'
            ],
            [
                'id' => 3,
                'title' => 'Full Stack Developer',
                'company' => 'StartUp XYZ',
                'location' => 'San Francisco, CA',
                'salary' => '$100,000 - $150,000',
                'description' => 'Help us build the next big thing.',
                'match_score' => 82,
                'apply_url' => '#'
            ]
        ];

        // Increment view counter
        session(['jobs_viewed' => $jobsViewed + count($jobs)]);

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
            'remaining_views' => $hasPremiumAccess ? 'unlimited' : (5 - $jobsViewed - count($jobs))
        ]);
    }

    /**
     * Show jobs by location
     */
    public function byLocation()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';

        return view('user.jobs.by-location', compact(
            'user',
            'subscription',
            'hasPremiumAccess'
        ));
    }

    /**
     * Generate jobs by location using AI
     */
    public function generateByLocation(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'job_title' => 'required|string'
        ]);

        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';
        $jobsViewed = session('jobs_viewed', 0);

        // Free tier: 5 jobs view per session
        if (!$hasPremiumAccess && $jobsViewed >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Free plan limit reached. Upgrade to Pro for unlimited job searches.'
            ], 403);
        }

        // TODO: Integrate job API and OpenAI service
        $jobs = [
            [
                'id' => 'loc1',
                'title' => $request->job_title . ' in ' . $request->location,
                'company' => 'Company A',
                'location' => $request->location,
                'salary' => 'Competitive',
                'description' => 'Great opportunity in ' . $request->location,
                'apply_url' => '#'
            ]
        ];

        session(['jobs_viewed' => $jobsViewed + 1]);

        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }

    /**
     * Apply to a job
     */
    public function applyJob(Request $request, $jobId)
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';
        $jobsApplied = session('jobs_applied', 0);

        // Free tier: 1 job apply per session
        if (!$hasPremiumAccess && $jobsApplied >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Free plan limit reached. You can apply to 1 job per session. Upgrade to Pro for unlimited applications.',
                'redirect' => route('user.pricing')
            ], 403);
        }

        // TODO: Store application in database
        session(['jobs_applied' => $jobsApplied + 1]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully!'
        ]);
    }
}
