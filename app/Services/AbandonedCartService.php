<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\User;

class AbandonedCartService
{
    /**
     * Track incomplete signup
     * Called when user creates account but hasn't set password yet
     */
    public static function trackIncompleteSignup(User $user)
    {
        // Check if already tracking this user's signup
        $existing = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'signup')
            ->where('status', 'abandoned')
            ->latest()
            ->first();

        if ($existing && $existing->created_at->diffInMinutes(now()) < 5) {
            // Don't create duplicate records within 5 minutes
            return $existing;
        }

        return AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => [
                'email' => $user->email,
                'name' => $user->name,
                'signup_method' => 'email',
            ],
        ]);
    }

    /**
     * Track incomplete resume
     * Called when user starts filling resume form but leaves
     */
    public static function trackIncompleteResume(User $user, $resumeId, $resumeData = [])
    {
        return AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'resume',
            'status' => 'abandoned',
            'resume_id' => $resumeId,
            'session_data' => array_merge([
                'name' => $user->name,
                'email' => $user->email,
                'resume_id' => $resumeId,
            ], $resumeData),
        ]);
    }

    /**
     * Track PDF preview view without upgrade
     * Called when user views generated PDF but doesn't have active subscription
     */
    public static function trackPdfPreviewAbandon(User $user, $resumeId, $resumeName = '', $resumeScore = 0)
    {
        // Check if we already tracked this resume's preview
        $existing = AbandonedCart::where('user_id', $user->id)
            ->where('type', 'pdf_preview')
            ->where('resume_id', $resumeId)
            ->where('status', 'abandoned')
            ->latest()
            ->first();

        if ($existing && $existing->created_at->diffInMinutes(now()) < 5) {
            // Don't create duplicate records within 5 minutes
            return $existing;
        }

        return AbandonedCart::create([
            'user_id' => $user->id,
            'type' => 'pdf_preview',
            'status' => 'abandoned',
            'resume_id' => $resumeId,
            'session_data' => [
                'resume_id' => $resumeId,
                'resume_name' => $resumeName,
                'score' => $resumeScore,
                'email' => $user->email,
                'name' => $user->name,
            ],
        ]);
    }

    /**
     * Mark abandoned cart as completed
     * Call this when user:
     * - Sets password (signup)
     * - Completes resume form (resume)
     * - Upgrades subscription (pdf_preview)
     */
    public static function markAsCompleted($userId, $type, $specificId = null)
    {
        $query = AbandonedCart::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', 'abandoned');

        if ($specificId) {
            $query->where('resume_id', $specificId);
        }

        $carts = $query->get();

        foreach ($carts as $cart) {
            $cart->markCompleted();
        }

        return $carts;
    }

    /**
     * Get abandonment statistics
     */
    public static function getStats()
    {
        return [
            'total_abandoned' => AbandonedCart::where('status', 'abandoned')->count(),
            'total_recovered' => AbandonedCart::where('status', 'completed')->count(),
            'by_type' => AbandonedCart::where('status', 'abandoned')
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'pending_recovery' => AbandonedCart::getPendingRecovery()->count(),
        ];
    }

    /**
     * Get user's abandoned carts
     */
    public static function getUserAbandoned($userId)
    {
        return AbandonedCart::where('user_id', $userId)
            ->where('status', 'abandoned')
            ->latest()
            ->get();
    }
}
