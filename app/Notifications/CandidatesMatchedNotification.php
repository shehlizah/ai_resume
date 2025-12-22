<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidatesMatchedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $job;
    protected $matchCount;

    public function __construct(Job $job, int $matchCount)
    {
        $this->job = $job;
        $this->matchCount = $matchCount;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("🎯 {$this->matchCount} Candidates Matched for Your Job")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! We've found **{$this->matchCount} qualified candidates** for your job posting:")
            ->line("**{$this->job->title}** at {$this->job->company}")
            ->action('View Matched Candidates', route('company.dashboard'))
            ->line('Our AI has analyzed their resumes and matched them based on skills, experience, and qualifications.')
            ->line('Review the candidates and reach out to the best fits directly from your dashboard.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'candidates_matched',
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'match_count' => $this->matchCount,
            'message' => "{$this->matchCount} candidates matched for {$this->job->title}",
        ];
    }
}
