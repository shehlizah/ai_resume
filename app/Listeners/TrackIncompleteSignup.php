<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\AbandonedCartService;
use Illuminate\Auth\Events\Registered;

class TrackIncompleteSignup
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Track that a user registered but may not have completed all setup
        // Note: At this point, the user HAS set their password
        // However, you can use this to track first login or profile completion

        // Actually, we don't need to track here since they completed signup
        // Instead, this serves as a marker that they completed the signup process
    }
}
