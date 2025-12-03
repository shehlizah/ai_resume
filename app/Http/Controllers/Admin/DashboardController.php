<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Template;
use App\Models\UserResume;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
        try {
            // Total Users
            $totalUsers = User::count();

            // Active Users (is_active = true)
            $activeUsers = User::where('is_active', true)->count();

            // Inactive Users
            $inactiveUsers = User::where('is_active', false)->count();

            // Admin Count
            $adminCount = User::where('role', 'admin')->count();

            // Total Templates
            $totalTemplates = Template::count();

            // Premium Templates
            $premiumTemplates = Template::where('is_premium', true)->count();

            // Active Templates
            $activeTemplates = Template::where('is_active', true)->count();

            // New Users This Week
            $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->startOfWeek())
                ->count();

            // Resumes Generated This Month (using UserResume model)
            $downloadsThisMonth = UserResume::where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count();

            // Total Resumes Ever Generated
            $totalResumesGenerated = UserResume::where('status', 'completed')->count();

            // User Growth Percentage (this month vs last month)
            $thisMonthUsers = User::where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count();
            $lastMonthUsers = User::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])->count();
            $userGrowth = $lastMonthUsers > 0
                ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100)
                : 100;

            // Resume Generation Growth Percentage
            $thisMonthResumes = UserResume::where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count();
            $lastMonthResumes = UserResume::where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->count();
            $downloadGrowth = $lastMonthResumes > 0
                ? round((($thisMonthResumes - $lastMonthResumes) / $lastMonthResumes) * 100)
                : 100;

            // Average Resumes Per Active User
            $avgDownloadsPerUser = $activeUsers > 0
                ? round($downloadsThisMonth / $activeUsers, 2)
                : 0;

            // Recent Users (Last 5) - with subscription info
            $recentUsers = User::with('activeSubscription.plan')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Popular Templates (Top 5 by usage count)
            $popularTemplates = Template::select('templates.id', 'templates.name', 'templates.is_premium', 'templates.is_active')
                ->selectRaw('COUNT(user_resumes.id) as downloads')
                ->leftJoin('user_resumes', 'templates.id', '=', 'user_resumes.template_id')
                ->where('user_resumes.status', 'completed')
                ->groupBy('templates.id', 'templates.name', 'templates.is_premium', 'templates.is_active')
                ->orderByRaw('COUNT(user_resumes.id) DESC')
                ->take(5)
                ->get();

            // ============================================
            // NEW: SUBSCRIPTION & PAYMENT STATISTICS
            // ============================================

            // Active Subscriptions
            $activeSubscriptions = UserSubscription::where('status', 'active')->count();

            // Trial Subscriptions (active subscriptions with trial_end_date in future)
            $trialSubscriptions = UserSubscription::where('status', 'active')
                ->whereNotNull('trial_end_date')
                ->where('trial_end_date', '>', Carbon::now())
                ->count();

            // Revenue This Month
            $revenueThisMonth = Payment::where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->sum('amount');

            // Revenue Last Month
            $revenueLastMonth = Payment::where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->sum('amount');

            // Revenue Growth Percentage
            $revenueGrowth = $revenueLastMonth > 0
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                : ($revenueThisMonth > 0 ? 100 : 0);

            // Total Revenue (All Time)
            $totalRevenue = Payment::where('status', 'completed')->sum('amount');

            // Pending Payments
            $pendingPayments = Payment::where('status', 'pending')->count();
            $pendingPaymentsAmount = Payment::where('status', 'pending')->sum('amount');

            // Conversion Rate (Free to Paid Users)
            $paidUsers = UserSubscription::where('status', 'active')
                ->where('amount', '>', 0)
                ->distinct('user_id')
                ->count('user_id');
            $conversionRate = $totalUsers > 0 ? round(($paidUsers / $totalUsers) * 100, 1) : 0;

            // Recent Payments (Last 5)
            $recentPayments = Payment::with('user')
                ->latest()
                ->take(5)
                ->get();

            // Recent Subscriptions (Last 5)
            $recentSubscriptions = UserSubscription::with(['user', 'plan'])
                ->latest()
                ->take(5)
                ->get();

            // Subscription Plans Performance
            $subscriptionPlans = SubscriptionPlan::where('is_active', true)
                ->withCount([
                    'subscriptions as subscribers_count' => function ($query) {
                        $query->where('status', 'active');
                    }
                ])
                ->get()
                ->map(function ($plan) {
                    // Calculate total revenue for each plan
                    $plan->total_revenue = UserSubscription::where('subscription_plan_id', $plan->id)
                        ->where('status', 'active')
                        ->sum('amount');
                    return $plan;
                });

            // ============================================
            // NEW: JOB & INTERVIEW STATISTICS
            // ============================================

            // Job Search Statistics (Session-based tracking)
            $jobSearchesCount = $totalUsers; // Placeholder - actual tracking would need job_searches table
            $activeJobLocations = 25; // Placeholder - would track unique locations from searches

            // Interview Session Statistics
            $interviewSessionsCount = InterviewSession::count();
            $completedInterviewSessions = InterviewSession::where('status', 'completed')->count();
            $interviewSessionsThisMonth = InterviewSession::whereMonth('created_at', Carbon::now()->month)->count();

            // Interview Questions Statistics
            $interviewQuestionsCount = InterviewQuestion::count();
            $answeredQuestionsCount = InterviewQuestion::whereNotNull('answer_text')->count();
            $avgInterviewScore = InterviewSession::where('status', 'completed')->avg('overall_score') ?? 0;


            return view('admin.dashboard.index', [
                // User Statistics
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'inactiveUsers' => $inactiveUsers,
                'adminCount' => $adminCount,
                'userGrowth' => $userGrowth,
                'newUsersThisWeek' => $newUsersThisWeek,

                // Template Statistics
                'totalTemplates' => $totalTemplates,
                'premiumTemplates' => $premiumTemplates,
                'activeTemplates' => $activeTemplates,

                // Resume Statistics
                'downloadsThisMonth' => $downloadsThisMonth,
                'totalResumesGenerated' => $totalResumesGenerated,
                'downloadGrowth' => $downloadGrowth,
                'avgDownloadsPerUser' => $avgDownloadsPerUser,

                // Subscription Statistics
                'activeSubscriptions' => $activeSubscriptions,
                'trialSubscriptions' => $trialSubscriptions,

                // Revenue Statistics
                'revenueThisMonth' => $revenueThisMonth,
                'revenueGrowth' => $revenueGrowth,
                'totalRevenue' => $totalRevenue,
                'pendingPayments' => $pendingPayments,
                'pendingPaymentsAmount' => $pendingPaymentsAmount,
                'conversionRate' => $conversionRate,

                // Recent Data
                'recentUsers' => $recentUsers,
                'popularTemplates' => $popularTemplates,
                'recentPayments' => $recentPayments,
                'recentSubscriptions' => $recentSubscriptions,
                'subscriptionPlans' => $subscriptionPlans,

                // Job & Interview Statistics
                'jobSearchesCount' => $jobSearchesCount,
                'activeJobLocations' => $activeJobLocations,
                'interviewSessionsCount' => $interviewSessionsCount,
                'interviewQuestionsCount' => $interviewQuestionsCount,

            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());

            return view('admin.dashboard.index', [
                // User Statistics
                'totalUsers' => 0,
                'activeUsers' => 0,
                'inactiveUsers' => 0,
                'adminCount' => 0,
                'userGrowth' => 0,
                'newUsersThisWeek' => 0,

                // Template Statistics
                'totalTemplates' => 0,
                'premiumTemplates' => 0,
                'activeTemplates' => 0,

                // Resume Statistics
                'downloadsThisMonth' => 0,
                'totalResumesGenerated' => 0,
                'downloadGrowth' => 0,
                'avgDownloadsPerUser' => 0,

                // Subscription Statistics
                'activeSubscriptions' => 0,
                'trialSubscriptions' => 0,

                // Revenue Statistics
                'revenueThisMonth' => 0,
                'revenueGrowth' => 0,
                'totalRevenue' => 0,
                'pendingPayments' => 0,
                'pendingPaymentsAmount' => 0,
                'conversionRate' => 0,

                // Recent Data
                'recentUsers' => collect([]),
                'popularTemplates' => collect([]),
                'recentPayments' => collect([]),
                'recentSubscriptions' => collect([]),
                'subscriptionPlans' => collect([]),

                // Job & Interview Statistics
                'jobSearchesCount' => 0,
                'activeJobLocations' => 0,
                'interviewSessionsCount' => 0,
                'interviewQuestionsCount' => 0,

            ]);
        }
    }
}
