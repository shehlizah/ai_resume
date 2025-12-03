<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class AdminInterviewController extends Controller
{
    /**
     * Show all interview sessions
     */
    public function sessions(Request $request)
    {
        $query = InterviewSession::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by user or job title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $sessions = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_sessions' => InterviewSession::count(),
            'completed_sessions' => InterviewSession::where('status', 'completed')->count(),
            'in_progress' => InterviewSession::where('status', 'in_progress')->count(),
            'avg_score' => InterviewSession::where('status', 'completed')->avg('overall_score'),
            'sessions_today' => InterviewSession::whereDate('created_at', today())->count(),
            'sessions_this_week' => InterviewSession::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('admin.interviews.sessions', compact('sessions', 'stats'));
    }

    /**
     * Delete a session
     */
    public function deleteSession($sessionId)
    {
        $session = InterviewSession::where('session_id', $sessionId)->firstOrFail();

        // Delete related questions
        InterviewQuestion::where('session_id', $sessionId)->delete();

        // Delete session
        $session->delete();

        return redirect()->route('admin.interviews.sessions')
            ->with('success', 'Interview session deleted successfully');
    }

    /**
     * Show question bank
     */
    public function questions(Request $request)
    {
        $query = InterviewQuestion::with(['session.user'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('question_type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('question_text', 'like', "%{$request->search}%");
        }

        $questions = $query->paginate(30);

        // Get statistics
        $stats = [
            'total_questions' => InterviewQuestion::count(),
            'answered_questions' => InterviewQuestion::whereNotNull('answer_text')->count(),
            'avg_score' => InterviewQuestion::whereNotNull('score')->avg('score'),
            'by_type' => InterviewQuestion::select('question_type', DB::raw('count(*) as count'))
                ->groupBy('question_type')
                ->pluck('count', 'question_type')
                ->toArray(),
        ];

        return view('admin.interviews.questions', compact('questions', 'stats'));
    }

    /**
     * Show interview settings
     */
    public function settings()
    {
        $settings = [
            'max_questions_per_session' => SystemSetting::get('max_questions_per_session', 5),
            'ai_enabled' => SystemSetting::get('ai_enabled', true),
            'question_types' => ['technical', 'behavioral', 'both'],
            'scoring_enabled' => SystemSetting::get('scoring_enabled', true),
            'feedback_enabled' => SystemSetting::get('feedback_enabled', true),
        ];

        return view('admin.interviews.settings', compact('settings'));
    }

    /**
     * Update interview settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'max_questions_per_session' => 'required|integer|min:3|max:10',
            'ai_enabled' => 'required|boolean',
            'scoring_enabled' => 'required|boolean',
            'feedback_enabled' => 'required|boolean',
        ]);

        // Save settings to database
        SystemSetting::set('max_questions_per_session', $request->max_questions_per_session, 'integer', 'interviews');
        SystemSetting::set('ai_enabled', $request->ai_enabled, 'boolean', 'interviews');
        SystemSetting::set('scoring_enabled', $request->scoring_enabled, 'boolean', 'interviews');
        SystemSetting::set('feedback_enabled', $request->feedback_enabled, 'boolean', 'interviews');

        return redirect()->route('admin.interviews.settings')
            ->with('success', 'Interview settings updated successfully');
    }

    /**
     * Get interview statistics for dashboard
     */
    public function getStatistics()
    {
        return [
            'total_sessions' => InterviewSession::count(),
            'completed_sessions' => InterviewSession::where('status', 'completed')->count(),
            'total_questions' => InterviewQuestion::count(),
            'avg_score' => InterviewSession::where('status', 'completed')->avg('overall_score'),
            'sessions_this_month' => InterviewSession::whereMonth('created_at', now()->month)->count(),
            'top_job_titles' => InterviewSession::select('job_title', DB::raw('count(*) as count'))
                ->groupBy('job_title')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'job_title')
                ->toArray(),
        ];
    }
}
