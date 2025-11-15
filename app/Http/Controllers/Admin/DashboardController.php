<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Template;
use App\Models\UserResume;
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

            // Recent Users (Last 5)
            $recentUsers = User::orderBy('created_at', 'desc')
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

            return view('admin.dashboard.index', [
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'inactiveUsers' => $inactiveUsers,
                'adminCount' => $adminCount,
                'totalTemplates' => $totalTemplates,
                'premiumTemplates' => $premiumTemplates,
                'activeTemplates' => $activeTemplates,
                'newUsersThisWeek' => $newUsersThisWeek,
                'downloadsThisMonth' => $downloadsThisMonth,
                'totalResumesGenerated' => $totalResumesGenerated,
                'userGrowth' => $userGrowth,
                'downloadGrowth' => $downloadGrowth,
                'avgDownloadsPerUser' => $avgDownloadsPerUser,
                'recentUsers' => $recentUsers,
                'popularTemplates' => $popularTemplates,
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            return view('admin.dashboard.index', [
                'totalUsers' => 0,
                'activeUsers' => 0,
                'inactiveUsers' => 0,
                'adminCount' => 0,
                'totalTemplates' => 0,
                'premiumTemplates' => 0,
                'activeTemplates' => 0,
                'newUsersThisWeek' => 0,
                'downloadsThisMonth' => 0,
                'totalResumesGenerated' => 0,
                'userGrowth' => 0,
                'downloadGrowth' => 0,
                'avgDownloadsPerUser' => 0,
                'recentUsers' => [],
                'popularTemplates' => [],
            ]);
        }
    }
}