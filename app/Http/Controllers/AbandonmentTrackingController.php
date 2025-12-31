<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\User;
use App\Services\AbandonedCartService;
use Illuminate\Http\Request;

class AbandonmentTrackingController extends Controller
{
    /**
     * Track when user starts signup form
     * Called via AJAX when user begins filling registration form
     */
    public function trackSignupStart(Request $request)
    {
        $email = $request->input('email');
        $name = $request->input('name');

        // Only track if we have an email
        if (!$email) {
            return response()->json(['success' => false], 400);
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // User already exists
            return response()->json(['success' => false, 'message' => 'User already exists'], 400);
        }

        // Check if we already have a tracking record for this email in the last 5 minutes
        $recentCart = AbandonedCart::whereRaw('JSON_EXTRACT(session_data, "$.email") = ?', [$email])
            ->where('type', 'signup')
            ->where('status', 'abandoned')
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentCart) {
            // Already tracking, just return success
            return response()->json(['success' => true, 'tracking_id' => $recentCart->id]);
        }

        // Create new tracking record
        $cart = AbandonedCart::create([
            'user_id' => null,
            'type' => 'signup',
            'status' => 'abandoned',
            'session_data' => [
                'email' => $email,
                'name' => $name,
                'started_at' => now()->toIso8601String(),
            ],
        ]);

        return response()->json(['success' => true, 'tracking_id' => $cart->id]);
    }

    /**
     * Track when user starts resume generation form
     */
    public function trackResumeStart(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $templateId = $request->input('template_id');
        $formData = $request->input('form_data', []);

        if (!$templateId) {
            return response()->json(['success' => false], 400);
        }

        // Create or update tracking record
        $cart = AbandonedCart::create([
            'user_id' => auth()->id(),
            'type' => 'resume',
            'status' => 'abandoned',
            'session_data' => array_merge(
                [
                    'template_id' => $templateId,
                    'started_at' => now()->toIso8601String(),
                ],
                $formData
            ),
        ]);

        return response()->json(['success' => true, 'tracking_id' => $cart->id]);
    }

    /**
     * Get abandonment statistics for admin
     */
    public function getStats()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(\App\Services\AbandonedCartService::getStats());
    }
}
