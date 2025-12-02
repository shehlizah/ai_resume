<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use App\Services\JobMatchService;
use App\Services\OpenAIService;

class JobFinderController extends Controller
{
    public function __construct(
        private readonly JobMatchService $jobMatchService,
        private readonly OpenAIService $openAIService
    )
    {
    }
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

        // Get user's resumes
        $resumes = $user->resumes()->get();

        return view('user.jobs.recommended', compact(
            'user',
            'subscription',
            'hasPremiumAccess',
            'jobsViewed',
            'jobsApplied',
            'resumes'
        ));
    }

    /**
     * Generate recommended jobs using AI
     */
    public function generateRecommended(Request $request)
    {
        $request->validate([
            'resume_id' => 'nullable|integer|exists:user_resumes,id',
            'uploaded_file' => 'nullable|string'
        ]);

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

        $resumeProfile = $this->resolveResumeProfile($request, $user);

        // Check if we have an uploaded file to send to AI
        $uploadedFilePath = null;
        if ($request->uploaded_file) {
            $uploadedFilePath = storage_path('app/' . ltrim($request->uploaded_file, '/'));
            if (!file_exists($uploadedFilePath)) {
                $uploadedFilePath = null;
            }
        }

        $limit = $hasPremiumAccess ? 8 : 5;

        // If we have an uploaded file, send directly to AI
        if ($uploadedFilePath) {
            $jobs = $this->openAIService->generateJobsFromResumeFile(
                $uploadedFilePath,
                'Remote (Any)',
                $limit
            );

            $newViewTotal = $jobsViewed + count($jobs);
            session(['jobs_viewed' => $newViewTotal]);
            $remainingViews = $hasPremiumAccess ? 'unlimited' : max(0, 5 - $newViewTotal);

            return response()->json([
                'success' => true,
                'jobs' => $jobs,
                'remaining_views' => $remainingViews
            ]);
        }

        // If saved resume selected, try AI with extracted text
        if (!empty($resumeProfile) && !empty($resumeProfile['raw_text']) && strlen($resumeProfile['raw_text']) > 50) {
            $jobs = $this->openAIService->generateJobsFromResume(
                $resumeProfile['raw_text'],
                'Remote (Any)',
                $limit
            );

            $newViewTotal = $jobsViewed + count($jobs);
            session(['jobs_viewed' => $newViewTotal]);
            $remainingViews = $hasPremiumAccess ? 'unlimited' : max(0, 5 - $newViewTotal);

            return response()->json([
                'success' => true,
                'jobs' => $jobs,
                'remaining_views' => $remainingViews
            ]);
        }

        // No resume provided
        return response()->json([
            'success' => false,
            'message' => 'Please upload a resume or select a saved resume to get job recommendations.'
        ], 422);
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

        // Get user's resumes
        $resumes = $user->resumes()->get();

        return view('user.jobs.by-location', compact(
            'user',
            'subscription',
            'hasPremiumAccess',
            'resumes'
        ));
    }

    /**
     * Generate jobs by location using AI
     */
    public function generateByLocation(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'job_title' => 'required|string',
            'resume_id' => 'nullable|integer|exists:user_resumes,id',
            'uploaded_file' => 'nullable|string'
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

        $resumeData = null;

        // Get resume data either from saved resume or uploaded file
        if ($request->resume_id) {
            $resume = $user->resumes()->findOrFail($request->resume_id);
            $resumeData = $resume->data; // Assuming resume data is stored here
        } elseif ($request->uploaded_file) {
            // Get the uploaded file content from storage
            $filePath = storage_path('app/' . $request->uploaded_file);
            if (file_exists($filePath)) {
                // TODO: Extract text from PDF/DOCX file
                $resumeData = "Uploaded resume content"; // Placeholder
            }
        }

        $resumeProfile = $this->resolveResumeProfile($request, $user);

        // Check if we have an uploaded file to send to AI
        $uploadedFilePath = null;
        if ($request->uploaded_file) {
            $uploadedFilePath = storage_path('app/' . ltrim($request->uploaded_file, '/'));
            if (!file_exists($uploadedFilePath)) {
                $uploadedFilePath = null;
            }
        }

        $limit = $hasPremiumAccess ? 8 : 5;

        // If we have an uploaded file, send directly to AI
        if ($uploadedFilePath) {
            $jobs = $this->openAIService->generateJobsFromResumeFile(
                $uploadedFilePath,
                $request->location,
                $limit
            );

            session(['jobs_viewed' => $jobsViewed + count($jobs)]);
            return response()->json([
                'success' => true,
                'jobs' => $jobs
            ]);
        }

        // If saved resume selected, try AI with extracted text
        if (!empty($resumeProfile) && !empty($resumeProfile['raw_text']) && strlen($resumeProfile['raw_text']) > 50) {
            $jobs = $this->openAIService->generateJobsFromResume(
                $resumeProfile['raw_text'],
                $request->location,
                $limit
            );

            session(['jobs_viewed' => $jobsViewed + count($jobs)]);
            return response()->json([
                'success' => true,
                'jobs' => $jobs
            ]);
        }

        // No resume provided
        return response()->json([
            'success' => false,
            'message' => 'Please upload a resume or enter a job title to search for jobs.'
        ], 422);
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

        private function resolveResumeProfile(Request $request, $user): array
        {
            if ($request->resume_id) {
                $resume = $user->resumes()->find($request->resume_id);
                if ($resume) {
                    $profile = $this->jobMatchService->analyzeStructuredResume($resume->data);
                    if (!empty($profile)) {
                        return $profile;
                    }
                }
            }

            if ($request->uploaded_file) {
                $profile = $this->jobMatchService->analyzeUploadedResume($request->uploaded_file);
                if (!empty($profile)) {
                    return $profile;
                }
            }

            return [];
        }

    /**
     * Reset job search session limits
     */
    public function resetSessionLimit(Request $request)
    {
        session(['jobs_viewed' => 0]);
        session(['jobs_applied' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Session limit reset successfully'
        ]);
    }
}
