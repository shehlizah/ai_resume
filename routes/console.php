<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically process queued jobs every minute
Schedule::command('queue:work --stop-when-empty')->everyMinute();

// Send abandoned cart recovery emails every hour
Schedule::command('abandoned-carts:send-recovery-emails')->hourly();
