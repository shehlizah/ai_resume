# Cart Abandonment - Quick Start Code Snippets

Copy and paste these into your views to start tracking abandonment immediately!

---

## 1️⃣ Track Signup Abandonment

Add this to your **signup form** in `resources/views/livewire/auth/register.blade.php`:

```html
@section('page-script')
<script>
    // Track signup form interactions
    const signupForm = document.querySelector('form[wire\\:submit*="register"]');
    if (signupForm) {
        let trackingTimeout;
        
        signupForm.addEventListener('input', function() {
            clearTimeout(trackingTimeout);
            
            trackingTimeout = setTimeout(() => {
                const email = document.querySelector('input[name="email"]')?.value;
                const name = document.querySelector('input[name="name"]')?.value;
                
                if (email && name) {
                    fetch('/api/abandonment/track-signup', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]')?.value
                        },
                        body: JSON.stringify({ email, name })
                    }).catch(err => console.log('Tracking recorded'));
                }
            }, 3000); // Track after 3 seconds of inactivity
        });
    }
</script>
@endsection
```

---

## 2️⃣ Track Resume Abandonment

Add this to your **resume form** (wherever users fill resume data):

```html
<script>
    // Track resume form progress
    const resumeForm = document.querySelector('form[id*="resume"]') || document.querySelector('form');
    if (resumeForm) {
        let trackingTimeout;
        
        resumeForm.addEventListener('input', function() {
            clearTimeout(trackingTimeout);
            
            trackingTimeout = setTimeout(() => {
                const templateId = document.querySelector('input[name="template_id"]')?.value;
                
                if (templateId && @auth{{ auth()->user()->id }}@endauth) {
                    const formData = new FormData(resumeForm);
                    const data = {
                        name: formData.get('name'),
                        email: formData.get('email'),
                        title: formData.get('title'),
                        // Add other important fields
                    };
                    
                    fetch('/api/abandonment/track-resume', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                        },
                        body: JSON.stringify({
                            template_id: templateId,
                            form_data: data
                        })
                    }).catch(err => console.log('Resume tracking recorded'));
                }
            }, 5000); // Track every 5 seconds of inactivity
        });
    }
</script>
```

---

## 3️⃣ Test Recovery Emails

Run this command to manually trigger recovery emails:

```bash
php artisan abandonment:send-reminders
```

Or in code:

```php
// In any controller or artisan command
use App\Jobs\SendAbandonedCartReminders;

SendAbandonedCartReminders::dispatch();
```

---

## 4️⃣ View Abandoned Carts

Check database directly:

```php
// In tinker or controller
use App\Models\AbandonedCart;

// Get all abandoned
$abandoned = AbandonedCart::where('status', 'abandoned')->get();

// Get by type
$resumeAbandoned = AbandonedCart::where('type', 'resume')
    ->where('status', 'abandoned')
    ->count();

// Get pending recovery (need emails)
$pending = AbandonedCart::getPendingRecovery();
```

Or SQL:

```sql
SELECT type, COUNT(*) as count, status 
FROM abandoned_carts 
GROUP BY type, status;
```

---

## 5️⃣ Create Admin Dashboard Widget

Add this to your admin dashboard to show abandonment stats:

```blade
<!-- resources/views/admin/widgets/abandonment-stats.blade.php -->
<div class="card">
    <div class="card-header">
        <h5>Cart Abandonment Statistics</h5>
    </div>
    <div class="card-body">
        @php
            $stats = \App\Services\AbandonedCartService::getStats();
        @endphp
        
        <div class="row">
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-danger">{{ $stats['total_abandoned'] }}</h3>
                    <p class="text-muted">Total Abandoned</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-success">{{ $stats['total_recovered'] }}</h3>
                    <p class="text-muted">Recovered/Completed</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-warning">{{ $stats['pending_recovery'] }}</h3>
                    <p class="text-muted">Pending Emails</p>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary" onclick="sendReminderNow()">
                    Send Now
                </button>
            </div>
        </div>
        
        <hr>
        
        <h6>By Type</h6>
        <ul>
            @foreach($stats['by_type'] as $type => $count)
                <li>{{ ucfirst($type) }}: <strong>{{ $count }}</strong></li>
            @endforeach
        </ul>
    </div>
</div>

<script>
function sendReminderNow() {
    if (confirm('Send abandonment reminder emails now?')) {
        fetch('/api/abandonment/stats', {
            headers: { 'Authorization': 'Bearer YOUR_TOKEN' }
        });
    }
}
</script>
```

---

## 6️⃣ Customize Recovery Emails

Modify the notification classes to customize content:

```php
// app/Notifications/IncompleteResumeReminder.php

public function toMail(object $notifiable): MailMessage
{
    $resumeData = $this->abandonedCart->session_data ?? [];
    $resumeName = $resumeData['name'] ?? 'Your Resume';
    
    return (new MailMessage)
        ->subject('Your Resume is Ready to Complete! 🚀')
        ->greeting("Hi {$notifiable->name}!")
        ->line("You were creating \"{$resumeName}\" but didn't finish.")
        ->line('Completing your resume takes just 2-3 more minutes.')
        ->line('Benefits of a completed resume:')
        ->line('✓ Better job matches')
        ->line('✓ Higher visibility to employers')
        ->line('✓ Professional AI-reviewed feedback')
        ->action('Finish My Resume', route('user.resumes.edit', ['id' => $this->abandonedCart->resume_id]))
        ->line('If you no longer need this resume, you can delete it anytime.')
        ->markdown('notifications.mail');
}
```

---

## 7️⃣ Setup Email Scheduling

Make sure to add to your cron job (cPanel, htaccess, or CI/CD):

```bash
# Run every hour
0 * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

Or for Laravel Forge/Envoyer, it's automatic.

---

## 8️⃣ Monitor Email Sending

Check if emails are actually being sent:

```bash
# Watch the queue
php artisan queue:work --verbose

# Check mail logs
tail -f storage/logs/laravel.log | grep -i "mail\|notification"

# In database - see email attempts
SELECT * FROM abandoned_carts 
WHERE recovery_email_sent_count > 0 
ORDER BY first_recovery_email_at DESC;
```

---

## 9️⃣ Advanced: Send Custom Recovery Email

```php
// In any controller
use App\Services\AbandonedCartService;
use App\Notifications\IncompleteResumeReminder;

public function sendCustomRecoveryEmail($cartId)
{
    $cart = AbandonedCart::findOrFail($cartId);
    
    if ($cart->user) {
        // Send custom notification
        $cart->user->notify(new IncompleteResumeReminder($cart));
        
        // Mark as sent
        $cart->markRecoveryEmailSent();
        
        return response()->json(['success' => true, 'message' => 'Email sent']);
    }
}
```

---

## 🔟 Debug: Check Abandonment Data

```php
// In tinker (php artisan tinker)
use App\Models\AbandonedCart;

// Get latest abandoned
$latest = AbandonedCart::latest()->first();

// See what's in it
$latest->session_data; // Shows stored form data

// Check email history
$latest->recovery_email_sent_count; // How many emails sent
$latest->first_recovery_email_at; // When first email was sent

// Get next ones needing email
AbandonedCart::getPendingRecovery(); // Filtered list ready for emailing

// Get stats
\App\Services\AbandonedCartService::getStats(); // Full statistics
```

---

## ⚡ Performance Optimization

If you have many abandoned records, add indexes:

```php
// In a new migration
Schema::table('abandoned_carts', function (Blueprint $table) {
    $table->index(['user_id', 'status']);
    $table->index(['type', 'status']);
    $table->index('created_at');
});
```

Run: `php artisan migrate`

---

## 🎨 Email Templates

Save HTML email templates in:
`resources/views/emails/abandonment/`

Example:
```blade
<!-- resources/views/emails/abandonment/resume.blade.php -->
@component('mail::message')
# Your Resume is Waiting! 📄

Hi {{ $userName }},

You started creating "{{ $resumeName }}" but didn't finish. 

Your progress has been saved! Just a few more clicks to complete it.

@component('mail::button', ['url' => $resumeUrl])
Continue Resume
@endcomponent

Thanks,
AI Resume Team
@endcomponent
```

Then use in notification:
```php
->markdown('emails.abandonment.resume')
```

---

## 📊 Generate Reports

Create admin report of abandonment:

```php
// In a route or controller
use App\Models\AbandonedCart;

public function abandonmentReport()
{
    $report = AbandonedCart::selectRaw('
        type,
        status,
        COUNT(*) as total,
        COUNT(DISTINCT user_id) as users,
        ROUND(AVG(recovery_email_sent_count), 2) as avg_emails_sent
    ')
    ->groupBy('type', 'status')
    ->get();
    
    return response()->json($report);
}
```

---

**That's it! Your cart abandonment system is now live.** 🎉

For more details, see: `CART_ABANDONMENT_IMPLEMENTATION.md`
