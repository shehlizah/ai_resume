<?php

namespace App\Jobs;

use App\Models\PostedJob;
use App\Services\CandidateMatchService;
use App\Notifications\CandidatesMatchedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MatchCandidatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $job;
    public $tries = 3;
    public $timeout = 300; // 5 minutes

    public function __construct(PostedJob $job)
    {
        $this->job = $job;
    }

    public function handle(CandidateMatchService $matchService)
    {
        // Temporarily disabled to prevent queue blocking
        return;

        try {
            Log::info("Starting candidate matching for job: {$this->job->id}");

            $matchCount = $matchService->matchCandidatesForJob($this->job, 50);

            Log::info("Matched {$matchCount} candidates for job: {$this->job->id}");

            // Send notification to employer if matches found
            if ($matchCount > 0 && $this->job->user) {
                $this->job->user->notify(new CandidatesMatchedNotification($this->job, $matchCount));
            }
        } catch (\Exception $e) {
            Log::error("Candidate matching failed for job {$this->job->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("MatchCandidatesJob permanently failed for job {$this->job->id}: {$exception->getMessage()}");
    }
}
