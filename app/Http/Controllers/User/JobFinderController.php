<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use App\Models\SystemSetting;
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

        $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');
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
        try {
            \Log::info('generateRecommended called', [
                'resume_id' => $request->resume_id,
                'uploaded_file' => $request->uploaded_file
            ]);

            $request->validate([
                'resume_id' => 'nullable|integer|exists:user_resumes,id',
                'uploaded_file' => 'nullable|string'
            ]);

            $user = Auth::user();
            $subscription = UserSubscription::where('user_id', $user->id)
                ->whereIn('status', ['active', 'pending'])
                ->latest()
                ->first();

            $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');

            \Log::info('Checking premium access', [
                'user_id' => $user->id,
                'has_lifetime_access' => $user->has_lifetime_access,
                'subscription_status' => $subscription ? $subscription->status : 'no_subscription',
                'hasPremiumAccess' => $hasPremiumAccess
            ]);

            // Free tier: 5 jobs view per session
            $jobsViewed = session('jobs_viewed', 0);

            if (!$hasPremiumAccess && $jobsViewed >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Free plan limit reached. View 5 jobs per session. Upgrade to Pro for unlimited access.',
                    'redirect' => route('user.pricing')
                ], 403);
            }

            if ($hasPremiumAccess) {
                \Log::info('Premium user - no session limits applied');
            }

            $resumeProfile = $this->resolveResumeProfile($request, $user);

            // Check if we have an uploaded file to send to AI
            $uploadedFilePath = null;
            if ($request->uploaded_file) {
                // The uploaded_file comes as a relative path like "uploads/temp/2/resume_..."
                // It's stored in storage/app/private/ so we need to prepend that
                $uploadedFilePath = storage_path('app/private/' . ltrim($request->uploaded_file, '/'));

                \Log::info('File path resolution', [
                    'input' => $request->uploaded_file,
                    'resolved' => $uploadedFilePath,
                    'exists' => file_exists($uploadedFilePath),
                    'is_readable' => is_readable($uploadedFilePath),
                    'file_size' => file_exists($uploadedFilePath) ? filesize($uploadedFilePath) : 'N/A'
                ]);

                if (!file_exists($uploadedFilePath)) {
                    \Log::warning('File does not exist at resolved path', [
                        'path' => $uploadedFilePath
                    ]);

                    // Try fallback without /private (in case it was stored elsewhere)
                    $fallbackPath = storage_path('app/' . ltrim($request->uploaded_file, '/'));
                    if (file_exists($fallbackPath)) {
                        \Log::info('File found at fallback path', ['path' => $fallbackPath]);
                        $uploadedFilePath = $fallbackPath;
                    } else {
                        \Log::warning('File not found at either path', [
                            'primary' => $uploadedFilePath,
                            'fallback' => $fallbackPath
                        ]);
                        $uploadedFilePath = null;
                    }
                }
            }

            $limit = $hasPremiumAccess ? SystemSetting::get('job_limit_premium', 8) : SystemSetting::get('job_limit_free', 5);

            // If we have an uploaded file, send directly to AI
            if ($uploadedFilePath) {
                \Log::info('Using uploaded file for AI', ['path' => $uploadedFilePath]);
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

            \Log::info('Resume profile check', [
                'has_profile' => !empty($resumeProfile),
                'has_text' => !empty($resumeProfile['raw_text'] ?? null),
                'text_length' => strlen($resumeProfile['raw_text'] ?? ''),
                'profile_keys' => !empty($resumeProfile) ? array_keys($resumeProfile) : []
            ]);

            // If saved resume selected, try AI with extracted text
            // More lenient check - accept if we have any profile data
            if (!empty($resumeProfile)) {
                $resumeText = $resumeProfile['raw_text'] ?? '';

                // If no raw text but we have structured data, create a summary
                if (empty($resumeText) || strlen($resumeText) < 20) {
                    \Log::info('Building resume text from structured profile data');
                    $parts = [];
                    if (!empty($resumeProfile['preferred_title'])) {
                        $parts[] = 'Title: ' . $resumeProfile['preferred_title'];
                    }
                    if (!empty($resumeProfile['skills'])) {
                        $parts[] = 'Skills: ' . implode(', ', (array)$resumeProfile['skills']);
                    }
                    $resumeText = implode("\n", $parts);
                }

                if (!empty($resumeText)) {
                    \Log::info('Using resume profile for AI', [
                        'text_length' => strlen($resumeText),
                        'has_title' => !empty($resumeProfile['preferred_title']),
                        'skills_count' => count($resumeProfile['skills'] ?? [])
                    ]);

                    $jobs = $this->openAIService->generateJobsFromResume(
                        $resumeText,
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
            }

            // No resume provided or insufficient data
            \Log::warning('No resume provided or insufficient data for job generation', [
                'has_profile' => !empty($resumeProfile),
                'profile_data' => $resumeProfile
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Could not extract sufficient information from the resume. Please try uploading a different resume or ensure your saved resume contains work experience and skills.'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in generateRecommended', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for jobs. Please try again.'
            ], 500);
        }
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

        $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');

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
        try {
            \Log::info('generateByLocation called', [
                'location' => $request->location,
                'job_title' => $request->job_title,
                'resume_id' => $request->resume_id,
                'uploaded_file' => $request->uploaded_file
            ]);

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

            $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');

            \Log::info('Checking premium access (by-location)', [
                'user_id' => $user->id,
                'has_lifetime_access' => $user->has_lifetime_access,
                'subscription_status' => $subscription ? $subscription->status : 'no_subscription',
                'hasPremiumAccess' => $hasPremiumAccess
            ]);

            $jobsViewed = session('jobs_viewed', 0);

            // Free tier: 5 jobs view per session
            if (!$hasPremiumAccess && $jobsViewed >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Free plan limit reached. Upgrade to Pro for unlimited job searches.'
                ], 403);
            }

            if ($hasPremiumAccess) {
                \Log::info('Premium user (by-location) - no session limits applied');
            }

        $resumeProfile = $this->resolveResumeProfile($request, $user);

        // Check if we have an uploaded file to send to AI
        $uploadedFilePath = null;
        if ($request->uploaded_file) {
            // The uploaded_file comes as a relative path like "uploads/temp/2/resume_..."
            // It's stored in storage/app/private/ so we need to prepend that
            $uploadedFilePath = storage_path('app/private/' . ltrim($request->uploaded_file, '/'));

            \Log::info('File path resolution (by-location)', [
                'input' => $request->uploaded_file,
                'resolved' => $uploadedFilePath,
                'exists' => file_exists($uploadedFilePath),
                'is_readable' => is_readable($uploadedFilePath),
                'file_size' => file_exists($uploadedFilePath) ? filesize($uploadedFilePath) : 'N/A'
            ]);

            if (!file_exists($uploadedFilePath)) {
                \Log::warning('File does not exist at resolved path (by-location)', [
                    'path' => $uploadedFilePath
                ]);

                // Try fallback without /private (in case it was stored elsewhere)
                $fallbackPath = storage_path('app/' . ltrim($request->uploaded_file, '/'));
                if (file_exists($fallbackPath)) {
                    \Log::info('File found at fallback path (by-location)', ['path' => $fallbackPath]);
                    $uploadedFilePath = $fallbackPath;
                } else {
                    \Log::warning('File not found at either path (by-location)', [
                        'primary' => $uploadedFilePath,
                        'fallback' => $fallbackPath
                    ]);
                    $uploadedFilePath = null;
                }
            }
        }

        $limit = $hasPremiumAccess ? SystemSetting::get('job_limit_premium', 8) : SystemSetting::get('job_limit_free', 5);

        // If we have an uploaded file, send directly to AI
        if ($uploadedFilePath) {
            \Log::info('Using uploaded file for AI (by-location)', ['path' => $uploadedFilePath]);
            $jobs = $this->openAIService->generateJobsFromResumeFile(
                $uploadedFilePath,
                $request->location,
                $limit
            );

            $newViewTotal = $jobsViewed + count($jobs);
            session(['jobs_viewed' => $newViewTotal]);

            return response()->json([
                'success' => true,
                'jobs' => $jobs
            ]);
        }

        \Log::info('Resume profile check (by-location)', [
            'has_profile' => !empty($resumeProfile),
            'has_text' => !empty($resumeProfile['raw_text'] ?? null),
            'text_length' => strlen($resumeProfile['raw_text'] ?? '')
        ]);

        // If saved resume selected, try AI with extracted text
        if (!empty($resumeProfile) && !empty($resumeProfile['raw_text']) && strlen($resumeProfile['raw_text']) > 50) {
            \Log::info('Using saved resume for AI (by-location)');
            $jobs = $this->openAIService->generateJobsFromResume(
                $resumeProfile['raw_text'],
                $request->location,
                $limit
            );

            $newViewTotal = $jobsViewed + count($jobs);
            session(['jobs_viewed' => $newViewTotal]);

            return response()->json([
                'success' => true,
                'jobs' => $jobs
            ]);
        }

        // No resume provided - search without resume matching
        \Log::info('Searching without resume (by-location) - using job title and location only');
        $jobs = $this->openAIService->generateJobsByLocation(
            $request->job_title,
            $request->location,
            $limit
        );

        $newViewTotal = $jobsViewed + count($jobs);
        session(['jobs_viewed' => $newViewTotal]);

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
            'without_resume' => true
        ]);
        } catch (\Exception $e) {
            \Log::error('Error in generateByLocation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for jobs. Please try again.'
            ], 500);
        }
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

        $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');
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
                    \Log::info('Analyzing resume data', [
                        'resume_id' => $resume->id,
                        'has_data' => !empty($resume->data),
                        'data_keys' => is_array($resume->data) ? array_keys($resume->data) : 'not_array'
                    ]);

                    $profile = $this->jobMatchService->analyzeStructuredResume($resume->data);

                    \Log::info('Resume profile analyzed', [
                        'profile_empty' => empty($profile),
                        'profile_keys' => !empty($profile) ? array_keys($profile) : [],
                        'raw_text_length' => strlen($profile['raw_text'] ?? '')
                    ]);

                    if (!empty($profile)) {
                        return $profile;
                    }
                }
            }

            if ($request->uploaded_file) {
                \Log::info('Analyzing uploaded file', ['file_path' => $request->uploaded_file]);
                $profile = $this->jobMatchService->analyzeUploadedResume($request->uploaded_file);

                \Log::info('Uploaded file profile analyzed', [
                    'profile_empty' => empty($profile),
                    'profile_keys' => !empty($profile) ? array_keys($profile) : [],
                    'raw_text_length' => strlen($profile['raw_text'] ?? '')
                ]);

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
