# Testing the AI Matching Feature

## 1. Test Queue/Scheduler is Running

On your server, run:
```bash
# Check if cron job exists
crontab -l

# Check Laravel scheduler
php artisan schedule:list

# Test scheduler manually (should show "Running scheduled command: queue:work --stop-when-empty")
php artisan schedule:run

# Check queue status
php artisan queue:failed
```

## 2. Test the Matching Feature End-to-End

### Step 1: Give yourself the AI Matching add-on (via database)
```bash
# SSH into your server
cd ~/jobsease.com

# Run this to add the ai-matching add-on to the employer_add_ons table
php artisan tinker
```

Then paste this in tinker:
```php
// Find your employer user
$employer = \App\Models\User::where('role', 'employer')->first();

// Create AI Matching add-on if it doesn't exist
$aiAddon = \App\Models\AddOn::firstOrCreate(
    ['slug' => 'ai-matching'],
    [
        'name' => 'AI Candidate Matching',
        'type' => 'ai_matching',
        'description' => 'Automatically match qualified candidates',
        'price' => 2500000,
        'is_active' => true
    ]
);

// Give the employer access
\App\Models\EmployerAddOn::create([
    'employer_id' => $employer->id,
    'add_on_id' => $aiAddon->id,
    'amount_paid' => 2500000,
    'status' => 'active',
    'purchased_at' => now(),
    'expires_at' => now()->addMonth()
]);

echo "AI Matching add-on granted to {$employer->email}\n";
exit;
```

### Step 2: Create some test user resumes (candidates)
```bash
php artisan tinker
```

```php
// Create 3 test candidates with resumes
for($i = 1; $i <= 3; $i++) {
    $user = \App\Models\User::create([
        'name' => "Test Candidate $i",
        'email' => "candidate$i@test.com",
        'password' => bcrypt('password'),
        'role' => 'user',
        'is_active' => true
    ]);
    
    $template = \App\Models\Template::first();
    
    \App\Models\UserResume::create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'status' => 'completed',
        'data' => [
            'name' => "Test Candidate $i",
            'email' => "candidate$i@test.com",
            'title' => $i == 1 ? 'Senior PHP Developer' : ($i == 2 ? 'Laravel Developer' : 'Full Stack Developer'),
            'skills' => $i == 1 ? 'PHP, Laravel, MySQL, Vue.js, Docker, AWS' : ($i == 2 ? 'PHP, Laravel, JavaScript, React' : 'Python, Django, PostgreSQL'),
            'job_title' => [$i == 1 ? 'Senior Developer' : 'Software Developer'],
            'company' => ['Tech Corp'],
            'responsibilities' => ['Built web applications'],
        ]
    ]);
}
echo "3 test candidates created\n";
exit;
```

### Step 3: Post a job as employer
1. Login as employer
2. Go to "Post Job"
3. Fill in:
   - **Title**: Senior PHP Developer
   - **Company**: Your Company
   - **Location**: Remote
   - **Description**: Looking for experienced PHP/Laravel developer
   - **Tags**: php, laravel, mysql, vue

### Step 4: Verify matching was queued
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Should see: "Starting candidate matching for job: X"
```

### Step 5: Run the queue immediately (don't wait 30 minutes)
```bash
# Process the queue right now for testing
php artisan queue:work --stop-when-empty
```

### Step 6: Check results
1. Refresh your company dashboard
2. You should see the AI-Matched Candidates widget
3. Should show test candidates with match scores

## 3. Quick Test Without 30-Minute Delay

If you want instant matching for testing, temporarily edit [app/Models/Job.php](app/Models/Job.php) line 50:

Change:
```php
MatchCandidatesJob::dispatch($job)->delay(now()->addMinutes(30));
```

To:
```php
MatchCandidatesJob::dispatchSync($job); // Instant matching
```

Remember to change it back after testing!

## Troubleshooting

**No matches appearing?**
- Check `php artisan queue:failed` for errors
- Verify employer has the add-on in `employer_add_ons` table
- Check `job_candidate_matches` table: `SELECT * FROM job_candidate_matches;`

**Want to see logs in real-time?**
```bash
tail -f storage/logs/laravel.log
```
