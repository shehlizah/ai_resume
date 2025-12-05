<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use App\Models\UserResume;
use App\Models\SystemSetting;
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

        $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');
        $resumes = $user->resumes()->get();

        return view('user.interview.prep', compact('hasPremiumAccess', 'resumes'));
    }

    /**
     * Generate interview prep from resume
     */
    public function generatePrep(Request $request)
    {
        try {
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

            // Extract resume text using the same approach as JobFinderController
            $resumeProfile = [];

            if ($validated['resume_id']) {
                $resume = $user->resumes()->findOrFail($validated['resume_id']);
                $resumeProfile = $this->jobMatchService->analyzeStructuredResume($resume->data);
            } elseif ($validated['uploaded_file']) {
                $resumeProfile = $this->jobMatchService->analyzeUploadedResume($validated['uploaded_file']);
            }

            // Extract text from profile
            $resumeText = $resumeProfile['raw_text'] ?? '';

            if (empty($resumeText) || strlen($resumeText) < 50) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not extract text from resume. Please ensure your resume contains sufficient information.'
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

            if ($resume) {
                $resumeProfile = $this->jobMatchService->analyzeStructuredResume($resume->data);
                $resumeText = $resumeProfile['raw_text'] ?? null;
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
        try {
            $request->validate([
                'session_id' => 'required|string',
                'question_id' => 'required|integer',
                'answer' => 'required|string'
            ]);

            $user = Auth::user();

            \Log::info('Submit Answer Request', [
                'user_id' => $user->id,
                'session_id' => $request->session_id,
                'question_id' => $request->question_id,
                'answer_length' => strlen($request->answer)
            ]);

            // Get session
            $session = InterviewSession::where('session_id', $request->session_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$session) {
                \Log::error('Session not found', [
                    'session_id' => $request->session_id,
                    'user_id' => $user->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            \Log::info('Session found', ['session' => $session->toArray()]);

            // Get ALL questions for this session to debug
            $allQuestions = InterviewQuestion::where('session_id', $request->session_id)->get();
            \Log::info('All questions for session', [
                'count' => $allQuestions->count(),
                'questions' => $allQuestions->pluck('id', 'question_number')->toArray()
            ]);

            // Get current question
            $currentQuestion = InterviewQuestion::where('id', $request->question_id)
                ->where('session_id', $request->session_id)
                ->first();

            if (!$currentQuestion) {
                \Log::error('Question not found', [
                    'question_id' => $request->question_id,
                    'session_id' => $request->session_id,
                    'available_questions' => $allQuestions->pluck('id')->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found. Available question IDs: ' . $allQuestions->pluck('id')->implode(', ')
                ], 404);
            }

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

            // Determine if we should generate another question (check max from settings)
            $questionCount = $session->questions()->count();
            $maxQuestions = SystemSetting::get('max_questions_per_session', 5);
            $nextQuestion = null;

            if ($questionCount < $maxQuestions) {
                // Get resume text if available
                $resumeText = null;
                $resume = UserResume::where('user_id', $user->id)->latest()->first();
                if ($resume) {
                    $resumeProfile = $this->jobMatchService->analyzeStructuredResume($resume->data);
                    $resumeText = $resumeProfile['raw_text'] ?? null;
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Submit Answer Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your answer. Please try again.'
            ], 500);
        }
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

        return view('user.interview.expert', compact('user'));
    }
}
