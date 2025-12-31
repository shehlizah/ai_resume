<?php

namespace App\Console\Commands;

use App\Jobs\SendAbandonedCartReminders;
use Illuminate\Console\Command;

class SendAbandonmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abandonment:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for abandoned carts and incomplete processes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting abandoned cart reminder emails...');

        // Dispatch the job to the queue
        SendAbandonedCartReminders::dispatch();

        $this->info('Abandonment reminder job queued successfully!');
    }
}
