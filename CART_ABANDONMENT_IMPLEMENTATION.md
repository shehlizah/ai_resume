# Cart Abandonment System Implementation

## Overview
This document explains the cart abandonment tracking system implemented for your AI Resume application. It tracks three main types of abandonment:

1. **Incomplete Signup** - User registers but doesn't set password
2. **Incomplete Resume** - User starts filling resume form but doesn't complete
3. **PDF Preview Without Upgrade** - User generates resume preview but doesn't upgrade to download

---

## Architecture

### Database Structure
**Table: `abandoned_carts`**
- `id` - Primary key
- `user_id` - Associated user (nullable for incomplete signups)
- `type` - Type of abandonment: 'signup', 'resume', 'pdf_preview'
- `status` - 'abandoned', 'recovered', 'completed'
- `session_data` - JSON data storing form/session info
- `resume_id` - For resume-related abandonment (nullable)
- `recovery_email_sent_count` - Tracks number of reminder emails sent
- `first_recovery_email_at` - When first recovery email was sent
- `completed_at` - When abandonment was recovered/completed
- `created_at`, `updated_at` - Timestamps

### Key Models & Services

**AbandonedCart Model** (`app/Models/AbandonedCart.php`)
- Tracks abandoned sessions
- Methods:
  - `isAbandonedFor($hours)` - Check if abandoned for X hours
  - `shouldSendRecoveryEmail()` - Logic for when to send emails
  - `markRecoveryEmailSent()` - Track email sending
  - `markCompleted()` - Mark as recovered
  - `getPendingRecovery()` - Get carts needing recovery emails

**AbandonedCartService** (`app/Services/AbandonedCartService.php`)
- Core service for managing abandoned carts
- Methods:
  - `trackIncompleteSignup(User)` - Track signup abandonment
  - `trackIncompleteResume(User, resumeId, data)` - Track resume abandonment
  - `trackPdfPreviewAbandon(User, resumeId, name, score)` - Track PDF preview
  - `markAsCompleted(userId, type, specificId)` - Mark as completed
  - `getStats()` - Get abandonment statistics

---

## Integration Points

### 1. Incomplete Signup Tracking

**Location:** `resources/views/livewire/auth/register.blade.php`

**How it works:**
- When user completes signup and sets password, `AbandonedCartService::markAsCompleted()` is called
- This removes any abandoned signup records for that user
- Before this, you can add AJAX to track when signup form is started

**Example API call to track signup start:**
```javascript
// Call when user starts filling signup form
fetch('/api/abandonment/track-signup', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
    },
    body: JSON.stringify({
        email: userEmail,
        name: userName
    })
});
```

### 2. Incomplete Resume Tracking

**Location:** `app/Http/Controllers/UserResumeController.php`

**Integration:**
- Line ~201: When resume is generated/completed, `markAsCompleted()` is called
- This removes abandoned resume records for that user
- You can add AJAX to track form progress

**Example API call to track resume start:**
```javascript
// Call when user starts resume form
fetch('/api/abandonment/track-resume', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({
        template_id: templateId,
        form_data: {
            name: userName,
            email: userEmail,
            // ... other form fields
        }
    })
});
```

### 3. PDF Preview Without Upgrade

**Location:** `app/Http/Controllers/UserResumeController.php` - `printPreview()` method

**Integration:** Lines ~938-952
- When user views PDF preview, system checks if they have active subscription
- If NO subscription: `trackPdfPreviewAbandon()` is called (tracked for recovery emails)
- If YES subscription: `markAsCompleted()` is called (no longer abandoned)

**Logic:**
```php
if (!$hasActivePackage) {
    // Track PDF preview abandonment
    AbandonedCartService::trackPdfPreviewAbandon($user, $resume->id, $resumeName, $score);
} else {
    // Mark as completed - they can download
    AbandonedCartService::markAsCompleted($user->id, 'pdf_preview', $resume->id);
}
```

---

## Recovery Email System

### Notifications

Three notification classes send targeted emails:

**1. IncompleteSignupReminder** (`app/Notifications/IncompleteSignupReminder.php`)
- Subject: "Complete Your Account Setup"
- Content: Explains benefits of completing signup
- Action: Link to password reset page

**2. IncompleteResumeReminder** (`app/Notifications/IncompleteResumeReminder.php`)
- Subject: "Your Resume Draft is Waiting"
- Content: Shows what they've completed, encourages finishing
- Action: Link to resume editor
- Data: Displays resume name, field, etc.

**3. PdfPreviewUpgradeReminder** (`app/Notifications/PdfPreviewUpgradeReminder.php`)
- Subject: "Your Beautiful Resume is Ready"
- Content: Explains premium features, offers discount code
- Action: Link to pricing page
- Data: Shows resume score and available features

### Recovery Schedule

Emails are sent on this timeline:

| Email # | Timing | Condition |
|---------|--------|-----------|
| 1st | After 1 hour | Abandoned for 1+ hour |
| 2nd | After 24 hours | Abandoned for 24+ hours |
| None | After 2 emails | Max 2 emails per abandonment |

**Scheduling:**
- Command: `php artisan abandonment:send-reminders`
- Runs: Every hour via Laravel Scheduler
- Queue: Uses async queue for reliability
- Register in console kernel (already done)

---

## How to Use

### 1. Run Migration
```bash
php artisan migrate
```

This creates the `abandoned_carts` table.

### 2. Schedule the Command
Add to your scheduler (already in `app/Console/Kernel.php`):
```php
$schedule->command('abandonment:send-reminders')->hourly()->onOneServer();
```

Or run manually:
```bash
php artisan abandonment:send-reminders
```

### 3. Monitor Abandoned Carts
Get statistics:
```bash
curl http://yourapp.com/api/abandonment/stats \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

Returns:
```json
{
  "total_abandoned": 45,
  "total_recovered": 12,
  "by_type": {
    "signup": 15,
    "resume": 20,
    "pdf_preview": 10
  },
  "pending_recovery": 8
}
```

### 4. Add Tracking to Frontend Forms

For signup form:
```html
<form id="signupForm">
    <!-- form fields -->
</form>

<script>
document.getElementById('signupForm').addEventListener('input', debounce(function() {
    const email = document.querySelector('input[name="email"]').value;
    const name = document.querySelector('input[name="name"]').value;
    
    if (email && name) {
        fetch('/api/abandonment/track-signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email, name })
        });
    }
}, 3000)); // Track after 3 seconds of inactivity
</script>
```

For resume form:
```html
<form id="resumeForm">
    <!-- form fields -->
</form>

<script>
document.getElementById('resumeForm').addEventListener('input', debounce(function() {
    const formData = new FormData(document.getElementById('resumeForm'));
    
    fetch('/api/abandonment/track-resume', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            template_id: formData.get('template_id'),
            form_data: Object.fromEntries(formData)
        })
    });
}, 5000)); // Track every 5 seconds of inactivity

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}
</script>
```

---

## API Endpoints

### Track Signup Start
```
POST /api/abandonment/track-signup
Content-Type: application/json

{
    "email": "user@example.com",
    "name": "John Doe"
}

Response: { "success": true, "tracking_id": 123 }
```

### Track Resume Start
```
POST /api/abandonment/track-resume
Content-Type: application/json
Authorization: Bearer {user_token}

{
    "template_id": 5,
    "form_data": {
        "name": "John Doe",
        "email": "john@example.com",
        "title": "Software Engineer"
    }
}

Response: { "success": true, "tracking_id": 456 }
```

### Get Statistics (Admin Only)
```
GET /api/abandonment/stats
Authorization: Bearer {admin_token}

Response: {
    "total_abandoned": 45,
    "total_recovered": 12,
    "by_type": { "signup": 15, "resume": 20, "pdf_preview": 10 },
    "pending_recovery": 8
}
```

---

## Database Queries

### Find Abandoned Carts
```php
use App\Models\AbandonedCart;

// Get all abandoned
$abandoned = AbandonedCart::where('status', 'abandoned')->get();

// Get pending recovery (need emails)
$pending = AbandonedCart::getPendingRecovery();

// Get user's abandoned carts
$userAbandoned = AbandonedCart::where('user_id', $userId)
    ->where('status', 'abandoned')
    ->get();

// By type
$resumeAbandoned = AbandonedCart::where('type', 'resume')
    ->where('status', 'abandoned')
    ->count();
```

### Mark as Completed
```php
use App\Services\AbandonedCartService;

// Mark signup as completed
AbandonedCartService::markAsCompleted($userId, 'signup');

// Mark specific resume as completed
AbandonedCartService::markAsCompleted($userId, 'resume', $resumeId);

// Mark PDF preview as completed
AbandonedCartService::markAsCompleted($userId, 'pdf_preview', $resumeId);
```

---

## Email Templates

Email templates are sent via Laravel Notifications. Customize in:
- `resources/views/vendor/notifications/` (Laravel default)

Or create custom templates:
- `resources/views/emails/abandonment/signup-reminder.blade.php`
- `resources/views/emails/abandonment/resume-reminder.blade.php`
- `resources/views/emails/abandonment/pdf-reminder.blade.php`

---

## Troubleshooting

### Emails Not Sending
1. Check queue is running: `php artisan queue:work`
2. Check mail configuration in `.env`
3. Check logs: `storage/logs/laravel.log`
4. Run command manually: `php artisan abandonment:send-reminders`

### Not Tracking Abandonment
1. Ensure API routes are registered
2. Check CSRF token is valid
3. Verify user_id is set (for authenticated endpoints)
4. Check database migrations ran: `php artisan migrate:status`

### Duplicate Tracking Records
- System automatically prevents duplicates within 5 minutes
- If needed, cleanup old records: 
  ```php
  AbandonedCart::where('created_at', '<', now()->subDays(30))
      ->where('status', 'abandoned')
      ->delete();
  ```

---

## Future Enhancements

1. **SMS Reminders** - Add text message option
2. **Offer Codes** - Generate discount codes for recovery emails
3. **Analytics Dashboard** - Admin panel showing abandonment metrics
4. **A/B Testing** - Test different email copy/timing
5. **Win-back Campaigns** - Special offers for long-abandoned users
6. **Progressive Reminders** - Different messaging after each email
7. **Exit Surveys** - Ask why users abandoned (optional popup)

---

## Performance Considerations

- Indexes added on `(user_id, type)` and `(status, created_at)` for fast queries
- Email sending is queued asynchronously
- Recovery checks only run hourly to avoid database load
- Max 2 emails per abandonment to avoid spam

---

## Compliance

- Users can unsubscribe from reminder emails via notification preferences
- Email count is tracked to prevent harassment
- GDPR compliant: Personal data deleted after 90 days if account deleted
- Fair marketing practices: Max 2 emails, helpful content (not pushy sales)

