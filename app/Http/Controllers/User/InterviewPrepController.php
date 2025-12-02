<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use App\Models\UserResume;
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

        $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');

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

        $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');

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

        $user = Auth::user();

        // Get resume text if provided
        $resumeText = null;
        if ($request->resume_id) {
            $resume = UserResume::where('id', $request->resume_id)
                ->where('user_id', $user->id)
                ->first();

            if ($resume && $resume->extracted_text) {
                $resumeText = $resume->extracted_text;
            }
        }

        // Create session in database
        $sessionId = 'session_' . uniqid() . '_' . time();

        $session = InterviewSession::create([
            'session_id' => $sessionId,
            'user_id' => $user->id,
            'job_title' => $request->job_title,
            'company' => $request->company,
            'interview_type' => $request->interview_type,
            'status' => 'in_progress',
        ]);

        // Generate first question using OpenAI
        $questionData = $this->openAIService->generateInterviewQuestion(
            $request->job_title,
            $request->company,
            $request->interview_type,
            $resumeText,
            [] // No previous Q&A for first question
        );

        // Store question in database
        $question = InterviewQuestion::create([
            'session_id' => $sessionId,
            'question_number' => 1,
            'question_text' => $questionData['question'],
            'question_type' => $questionData['type'] ?? 'general',
            'focus_area' => $questionData['focus_area'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'first_question' => [
                'id' => $question->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'] ?? 'general',
                'number' => 1
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

        $user = Auth::user();

        // Get session
        $session = InterviewSession::where('session_id', $request->session_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Get current question
        $currentQuestion = InterviewQuestion::where('id', $request->question_id)
            ->where('session_id', $request->session_id)
            ->firstOrFail();

        // Evaluate answer using OpenAI
        $evaluation = $this->openAIService->evaluateInterviewAnswer(
            $currentQuestion->question_text,
            $request->answer,
            $session->job_title,
            $session->company
        );

        // Update question with answer and feedback
        $currentQuestion->update([
            'answer_text' => $request->answer,
            'score' => $evaluation['score'],
            'feedback' => [
                'feedback' => $evaluation['feedback'],
                'strengths' => $evaluation['strengths'],
                'improvements' => $evaluation['improvements']
            ],
            'answered_at' => now(),
        ]);

        // Get all previous Q&A for context
        $previousQA = $session->questions()
            ->whereNotNull('answer_text')
            ->get()
            ->map(function ($q) {
                return [
                    'question' => $q->question_text,
                    'answer' => $q->answer_text,
                    'score' => $q->score
                ];
            })
            ->toArray();

        // Determine if we should generate another question (limit to 5 questions)
        $questionCount = $session->questions()->count();
        $nextQuestion = null;

        if ($questionCount < 5) {
            // Get resume text if available
            $resumeText = null;
            $resume = UserResume::where('user_id', $user->id)->latest()->first();
            if ($resume && $resume->extracted_text) {
                $resumeText = $resume->extracted_text;
            }

            // Generate next question
            $questionData = $this->openAIService->generateInterviewQuestion(
                $session->job_title,
                $session->company,
                $session->interview_type,
                $resumeText,
                $previousQA
            );

            // Store next question
            $newQuestion = InterviewQuestion::create([
                'session_id' => $request->session_id,
                'question_number' => $questionCount + 1,
                'question_text' => $questionData['question'],
                'question_type' => $questionData['type'] ?? 'general',
                'focus_area' => $questionData['focus_area'] ?? null,
            ]);

            $nextQuestion = [
                'id' => $newQuestion->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'] ?? 'general',
                'number' => $questionCount + 1
            ];
        } else {
            // Mark session as complete
            $session->complete();
        }

        return response()->json([
            'success' => true,
            'feedback' => $evaluation['feedback'],
            'score' => $evaluation['score'],
            'strengths' => $evaluation['strengths'],
            'improvements' => $evaluation['improvements'],
            'next_question' => $nextQuestion,
            'is_complete' => is_null($nextQuestion)
        ]);
    }

    /**
     * Show AI interview results
     */
    public function aiResults($sessionId)
    {
        $user = Auth::user();

        // Fetch session from database
        $session = InterviewSession::where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->with('questions')
            ->firstOrFail();

        // Generate final report if not already generated
        if (!$session->final_report && $session->status === 'completed') {
            $questions = $session->questions()
                ->whereNotNull('answer_text')
                ->get()
                ->map(function ($q) {
                    return [
                        'question' => $q->question_text,
                        'answer' => $q->answer_text,
                        'score' => $q->score,
                        'feedback' => $q->feedback['feedback'] ?? ''
                    ];
                })
                ->toArray();

            $sessionData = [
                'job_title' => $session->job_title,
                'company' => $session->company,
                'total_questions' => $session->total_questions,
                'overall_score' => $session->overall_score,
                'questions' => $questions
            ];

            $finalReport = $this->openAIService->generateFinalInterviewReport($sessionData);

            // Store the final report
            $session->update([
                'final_report' => $finalReport,
                'final_summary' => $finalReport['summary']
            ]);
        }

        // Format session data for view
        $sessionData = [
            'id' => $session->session_id,
            'job_title' => $session->job_title,
            'company' => $session->company,
            'interview_type' => ucfirst($session->interview_type),
            'overall_score' => $session->overall_score,
            'status' => $session->status,
            'completed_at' => $session->completed_at,
            'strengths' => $session->final_report['strengths'] ?? [],
            'improvements' => $session->final_report['improvements'] ?? [],
            'recommendations' => $session->final_report['recommendations'] ?? [],
            'summary' => $session->final_report['summary'] ?? '',
            'verdict' => $session->final_report['verdict'] ?? 'Moderate Candidate',
            'detailed_feedback' => $session->questions->map(function ($q) {
                return [
                    'number' => $q->question_number,
                    'question' => $q->question_text,
                    'answer' => $q->answer_text,
                    'score' => $q->score,
                    'feedback' => $q->feedback['feedback'] ?? '',
                    'strengths' => $q->feedback['strengths'] ?? [],
                    'improvements' => $q->feedback['improvements'] ?? []
                ];
            })->toArray()
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
