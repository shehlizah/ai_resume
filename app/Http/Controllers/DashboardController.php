<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Template; // Adjust based on your template model name
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
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

        // New Users This Week
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        // Downloads This Month
        $downloadsThisMonth = DB::table('template_downloads')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

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

        // Download Growth Percentage
        $thisMonthDownloads = DB::table('template_downloads')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();
        $lastMonthDownloads = DB::table('template_downloads')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])->count();
        $downloadGrowth = $lastMonthDownloads > 0 
            ? round((($thisMonthDownloads - $lastMonthDownloads) / $lastMonthDownloads) * 100) 
            : 100;

        // Average Downloads Per User
        $avgDownloadsPerUser = $activeUsers > 0 
            ? round($downloadsThisMonth / $activeUsers, 2) 
            : 0;

        // Recent Users (Last 5)
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Popular Templates (Top 5 by downloads)
        $popularTemplates = Template::select('user_resumes.*')
            ->selectRaw('COUNT(template_downloads.id) as downloads')
            ->leftJoin('template_downloads', 'resume_templates.id', '=', 'template_downloads.template_id')
            ->groupBy('resume_templates.id')
            ->orderByRaw('COUNT(template_downloads.id) DESC')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'adminCount' => $adminCount,
            'totalTemplates' => $totalTemplates,
            'premiumTemplates' => $premiumTemplates,
            'newUsersThisWeek' => $newUsersThisWeek,
            'downloadsThisMonth' => $downloadsThisMonth,
            'userGrowth' => $userGrowth,
            'downloadGrowth' => $downloadGrowth,
            'avgDownloadsPerUser' => $avgDownloadsPerUser,
            'recentUsers' => $recentUsers,
            'popularTemplates' => $popularTemplates,
        ]);
    }
}