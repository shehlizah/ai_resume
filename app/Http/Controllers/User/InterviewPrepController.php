<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use App\Services\OpenAIService;
use App\Services\JobMatchService;

class InterviewPrepController extends Controller
{
    protected $openAIService;
    protected $jobMatchService;

    public function __construct(OpenAIService $openAIService, JobMatchService $jobMatchService)
    {
        $this->openAIService = $openAIService;
        $this->jobMatchService = $jobMatchService;
    }

    /**
     * Show interview prep page with resume upload
     */
    public function prep()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';
        $resumes = $user->resumes()->get();

        return view('user.interview.prep', compact('hasPremiumAccess', 'resumes'));
    }

    /**
     * Generate interview prep from resume
     */
    public function generatePrep(Request $request)
    {
        $validated = $request->validate([
            'resume_id' => 'nullable|exists:user_resumes,id',
            'uploaded_file' => 'nullable|string',
            'job_title' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,mid,senior,executive'
        ]);

        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';

        try {
            // Extract resume text
            $resumeText = '';

            if ($validated['resume_id']) {
                $resume = $user->resumes()->findOrFail($validated['resume_id']);
                $filePath = storage_path('app/private/' . $resume->file_path);

                if (file_exists($filePath)) {
                    $resumeText = $this->jobMatchService->extractTextFromFile($filePath);
                }
            } elseif ($validated['uploaded_file']) {
                $filePath = storage_path('app/private/' . ltrim($validated['uploaded_file'], '/'));

                if (file_exists($filePath)) {
                    $resumeText = $this->jobMatchService->extractTextFromFile($filePath);
                }
            }

            if (empty($resumeText)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not extract text from resume'
                ], 400);
            }

            // Generate interview prep based on plan
            if ($hasPremiumAccess) {
                $result = $this->openAIService->generateInterviewPrepFromResume(
                    $resumeText,
                    $validated['job_title'],
                    $validated['experience_level'],
                    'pro'
                );
            } else {
                $result = $this->openAIService->generateInterviewPrepFromResume(
                    $resumeText,
                    $validated['job_title'],
                    $validated['experience_level'],
                    'free'
                );
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('Interview Prep Generation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate interview prep'
            ], 500);
        }
    }

    /**
     * Show practice questions (Free)
     */
    public function questions()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $hasPremiumAccess = $subscription && $subscription->status === 'active';

        // Get user's resumes
        $resumes = $user->resumes()->get();

        // Mock basic interview questions (free tier)
        $questions = [
            [
                'id' => 1,
                'question' => 'Tell me about yourself.',
                'category' => 'General',
                'difficulty' => 'Easy',
                'tips' => [
                    'Keep it to 2-3 minutes',
                    'Focus on relevant experience',
                    'End with why you\'re interested in the role'
                ]
            ],
            [
                'id' => 2,
                'question' => 'What are your strengths?',
                'category' => 'Personal',
                'difficulty' => 'Easy',
                'tips' => [
                    'Choose 2-3 relevant strengths',
                    'Provide specific examples',
                    'Relate them to the job'
                ]
            ],
            [
                'id' => 3,
                'question' => 'What is your greatest weakness?',
                'category' => 'Personal',
                'difficulty' => 'Medium',
                'tips' => [
                    'Be honest but strategic',
                    'Mention how you\'re improving',
                    'Choose something that won\'t directly impact the role'
                ]
            ],
            [
                'id' => 4,
                'question' => 'Why do you want to work for our company?',
                'category' => 'Company',
                'difficulty' => 'Medium',
                'tips' => [
                    'Research the company beforehand',
                    'Mention specific initiatives or values',
                    'Connect your skills to their needs'
                ]
            ],
            [
                'id' => 5,
                'question' => 'Describe a challenging situation and how you handled it.',
                'category' => 'Experience',
                'difficulty' => 'Hard',
                'tips' => [
                    'Use the STAR method (Situation, Task, Action, Result)',
                    'Focus on positive outcomes',
                    'Show problem-solving skills'
                ]
            ]
        ];

        return view('user.interview.questions', compact(
            'user',
            'subscription',
            'hasPremiumAccess',
            'questions',
            'resumes'
        ));
    }

    /**
     * Show AI practice interview page (PRO)
     */
    public function aiPractice()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        // Get user's resumes
        $resumes = $user->resumes()->get();

        return view('user.interview.ai-practice', compact('user', 'subscription', 'resumes'));
    }

    /**
     * Start a new AI practice session
     */
    public function startAIPractice(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string',
            'company' => 'required|string',
            'interview_type' => 'required|in:technical,behavioral,both',
            'resume_id' => 'nullable|integer|exists:user_resumes,id'
        ]);

        // TODO: Create interview session in database
        // TODO: Generate questions using OpenAI

        $sessionId = 'session_' . uniqid();

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'first_question' => [
                'id' => 1,
                'question' => 'Tell me about your experience with ' . $request->job_title,
                'type' => 'open_ended'
            ]
        ]);
    }

    /**
     * Submit answer to interview question
     */
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'question_id' => 'required|integer',
            'answer' => 'required|string'
        ]);

        // TODO: Store answer
        // TODO: Get AI feedback
        // TODO: Generate next question

        return response()->json([
            'success' => true,
            'feedback' => 'Great answer! You provided good context and examples.',
            'score' => 85,
            'next_question' => [
                'id' => 2,
                'question' => 'How would you approach this technical challenge?'
            ]
        ]);
    }

    /**
     * Show AI interview results
     */
    public function aiResults($sessionId)
    {
        $user = Auth::user();

        // TODO: Fetch session from database
        $sessionData = [
            'id' => $sessionId,
            'job_title' => 'Software Engineer',
            'company' => 'Tech Company',
            'overall_score' => 82,
            'strengths' => [
                'Clear communication',
                'Good problem-solving approach',
                'Relevant experience'
            ],
            'improvements' => [
                'Add more specific examples',
                'Provide more technical depth',
                'Practice timing of responses'
            ],
            'detailed_feedback' => [
                'Q1: Tell me about yourself - Score: 85 - Good structure and relevant info',
                'Q2: Technical question - Score: 80 - Solid approach but could add more depth'
            ]
        ];

        return view('user.interview.ai-results', compact('user', 'sessionData'));
    }

    /**
     * Show book expert session page (PRO)
     */
    public function bookExpert()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        // TODO: Fetch available expert slots
        $experts = [
            [
                'id' => 1,
                'name' => 'John Smith',
                'title' => 'Senior Tech Recruiter',
                'experience' => '15+ years',
                'bio' => 'Specializes in tech interviews and career coaching',
                'rate' => '$99/hour',
                'available_slots' => [
                    '2024-12-05 10:00 AM',
                    '2024-12-05 2:00 PM',
                    '2024-12-06 9:00 AM'
                ]
            ],
            [
                'id' => 2,
                'name' => 'Sarah Johnson',
                'title' => 'Career Coach',
                'experience' => '10+ years',
                'bio' => 'Expert in interview preparation and personal branding',
                'rate' => '$79/hour',
                'available_slots' => [
                    '2024-12-05 3:00 PM',
                    '2024-12-06 11:00 AM',
                    '2024-12-07 10:00 AM'
                ]
            ]
        ];

        return view('user.interview.expert', compact('user', 'subscription', 'experts'));
    }

    /**
     * Book an expert session
     */
    public function bookSession(Request $request)
    {
        $request->validate([
            'expert_id' => 'required|integer',
            'time_slot' => 'required|string'
        ]);

        // TODO: Store booking in database
        // TODO: Send confirmation email

        return response()->json([
            'success' => true,
            'message' => 'Session booked successfully!',
            'booking_id' => 'BOOK_' . uniqid(),
            'next_page' => route('user.interview.my-sessions')
        ]);
    }

    /**
     * Show my scheduled sessions
     */
    public function mySessions()
    {
        $user = Auth::user();

        // TODO: Fetch sessions from database
        $sessions = [
            [
                'id' => 1,
                'expert_name' => 'John Smith',
                'scheduled_date' => '2024-12-05 10:00 AM',
                'duration' => '1 hour',
                'status' => 'scheduled',
                'zoom_link' => 'https://zoom.us/j/...'
            ]
        ];

        return view('user.interview.my-sessions', compact('user', 'sessions'));
    }
}
