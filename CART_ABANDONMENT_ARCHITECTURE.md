# Cart Abandonment System - File Structure

## 📁 Complete File Listing

### ✅ NEW FILES CREATED

```
app/
├── Console/
│   ├── Kernel.php                              ← Scheduler setup (CREATED)
│   └── Commands/
│       └── SendAbandonmentReminders.php        ← Console command (CREATED)
├── Http/
│   └── Controllers/
│       └── AbandonmentTrackingController.php   ← Tracking API (CREATED)
├── Jobs/
│   └── SendAbandonedCartReminders.php          ← Queue job (CREATED)
├── Models/
│   └── AbandonedCart.php                       ← Database model (CREATED)
├── Notifications/
│   ├── IncompleteSignupReminder.php            ← Email 1 (CREATED)
│   ├── IncompleteResumeReminder.php            ← Email 2 (CREATED)
│   └── PdfPreviewUpgradeReminder.php           ← Email 3 (CREATED)
└── Services/
    └── AbandonedCartService.php                ← Core service (CREATED)

database/
└── migrations/
    └── 2025_01_01_000001_create_abandoned_carts_table.php  ← Migration (CREATED)

📄 DOCUMENTATION FILES (CREATED):
├── CART_ABANDONMENT_IMPLEMENTATION.md  ← Technical reference
├── CART_ABANDONMENT_SUMMARY.md         ← Feature overview
├── CART_ABANDONMENT_QUICKSTART.md      ← Code snippets
└── CART_ABANDONMENT_COMPLETE.md        ← This file
```

### 🔧 MODIFIED FILES

```
routes/
└── web.php                                     ← Added 3 endpoints + import (MODIFIED)

resources/views/livewire/auth/
└── register.blade.php                         ← Added completion tracking (MODIFIED)

app/Http/Controllers/
└── UserResumeController.php                   ← Added 2 tracking calls (MODIFIED)
```

---

## 📊 Database Table Created

### abandoned_carts
```sql
CREATE TABLE abandoned_carts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NULLABLE FOREIGN KEY,
    type VARCHAR(20),              -- 'signup', 'resume', 'pdf_preview'
    status VARCHAR(20) DEFAULT 'abandoned',    -- 'abandoned', 'recovered', 'completed'
    session_data JSON NULLABLE,
    resume_id VARCHAR(255) NULLABLE,
    recovery_email_sent_count INT DEFAULT 0,
    first_recovery_email_at TIMESTAMP NULLABLE,
    completed_at TIMESTAMP NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id, type),
    INDEX (status, created_at)
);
```

---

## 🔗 System Connections

### Route Registration Flow
```
routes/web.php (Line 604-607)
    ↓
POST /api/abandonment/track-signup
    ↓
AbandonmentTrackingController::trackSignupStart()
    ↓
AbandonedCart::create() → Stored in DB
```

### Email Sending Flow
```
Hourly Scheduler (Console/Kernel.php)
    ↓
abandonment:send-reminders Command
    ↓
SendAbandonedCartReminders Job (queued)
    ↓
For each pending cart:
    ├── IncompleteSignupReminder (if type='signup')
    ├── IncompleteResumeReminder (if type='resume')
    └── PdfPreviewUpgradeReminder (if type='pdf_preview')
    ↓
User receives email
    ↓
AbandonedCart::markRecoveryEmailSent()
```

### Completion Detection Flow
```
User completes action (signup/resume/upgrade)
    ↓
Application detects completion:
    ├── Register: register.blade.php line 35
    ├── Resume: UserResumeController line 213
    └── PDF: UserResumeController line 938
    ↓
AbandonedCartService::markAsCompleted()
    ↓
abandoned_carts.status = 'completed'
    ↓
No recovery emails sent
```

---

## 📋 Sequence Diagrams

### Signup Abandonment Sequence
```
User              Form                Tracking        DB
 │                 │                    │              │
 ├──Fill form──→   │                    │              │
 │                 │──Optional: Track──→│              │
 │                 │                    ├─Save record─→│
 │                 │                    │              │
 ├─Abandon─────→   │                    │              │
 │                 │                    │              │
 │<────Wait 1h──────────────────────────────────────────
 │                 │                    │              │
 │              [Email Reminder]        │              │
 │                 │                    ├─Update count→│
 │                 │                    │              │
 ├─Complete signup─────────────────────→│              │
 │                 │                    ├─Mark complete─→│
 │                 │                    │              │
```

### Resume Abandonment Sequence
```
User              Form                Resume        DB
 │                 │                  Controller    │
 ├─Select template │                    │          │
 ├─Start form──→   │                    │          │
 │                 │──Track progress──→ │──Save──→ │
 │                 │                    │          │
 ├─Fill partial──→ │                    │          │
 │                 │──Update tracking──→ │──Update→ │
 │                 │                    │          │
 ├─Leave/Abandon──→ │                    │          │
 │                 │                    │          │
 │<────Wait 1h──────────────────────────────────────
 │                 │                    │          │
 │              [Email Reminder]        │          │
 │                 │                    │──Update→ │
 │                 │                    │          │
 ├─Complete form──→ │                    │──Save──→ │
 │                 │                    │──Mark───→ │
 │                 │                    │  Complete │
```

### PDF Preview Abandonment Sequence
```
User              PDF Preview          Controller    DB
 │                 │                    │           │
 ├─Generate PDF──→ │                    │           │
 │                 │                    │           │
 ├─View preview──→ │                    │           │
 │                 │──Check subscription│           │
 │                 │                    │           │
 │              [Has no subscription]   │           │
 │                 │───Track abandon───→├─Save────→ │
 │                 │                    │           │
 ├─Leave without──→ │                    │           │
 │  upgrading      │                    │           │
 │                 │                    │           │
 │<────Wait 1h──────────────────────────────────────
 │                 │                    │           │
 │              [Email Upgrade Offer]   │           │
 │                 │                    ├─Update──→ │
 │                 │                    │           │
 ├─Click upgrade──→ │                    │           │
 │                 │──Stripe payment──→ │           │
 │                 │                    │           │
 │<─Confirmation── │                    │──Mark───→ │
 │                 │                    │  Complete │
```

---

## 🔄 Data Flow

### Session Data Storage (JSON)

**Signup Abandonment:**
```json
{
    "email": "user@example.com",
    "name": "John Doe",
    "signup_method": "email",
    "started_at": "2025-01-01T10:30:00Z"
}
```

**Resume Abandonment:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "resume_id": 123,
    "template_id": 5,
    "template": "Professional Modern",
    "title": "Software Engineer",
    "company": "Tech Corp",
    "started_at": "2025-01-01T11:00:00Z"
}
```

**PDF Preview Abandonment:**
```json
{
    "resume_id": 123,
    "resume_name": "Senior Engineer Resume",
    "score": 78,
    "email": "john@example.com",
    "name": "John Doe"
}
```

---

## 🎯 Entry Points

### Where Tracking Happens

| Type | File | Line | Method | Trigger |
|------|------|------|--------|---------|
| Signup Completion | register.blade.php | 35 | markAsCompleted | Password set |
| Resume Completion | UserResumeController | 213 | markAsCompleted | Form submitted |
| PDF Abandonment | UserResumeController | 945 | trackPdfPreviewAbandon | Preview viewed (no subscription) |
| PDF Completion | UserResumeController | 949 | markAsCompleted | Has subscription |

### Where Emails Are Sent

| Type | File | Condition | Send Time |
|------|------|-----------|-----------|
| Signup | IncompleteSignupReminder | Abandoned 1h+ | 1st: 1h, 2nd: 24h |
| Resume | IncompleteResumeReminder | Abandoned 1h+ | 1st: 1h, 2nd: 24h |
| PDF | PdfPreviewUpgradeReminder | Abandoned 1h+ | 1st: 1h, 2nd: 24h |

---

## 🔌 Integration Hooks

### In Authentication Flow
- File: `resources/views/livewire/auth/register.blade.php`
- Hooks into: User registration completion
- Action: Marks signup as completed

### In Resume Generation Flow
- File: `app/Http/Controllers/UserResumeController.php`
- Method: `generate()` (line ~213)
- Action: Marks resume abandonment as completed

### In PDF Preview Flow
- File: `app/Http/Controllers/UserResumeController.php`
- Method: `printPreview()` (line ~938)
- Action: Detects and tracks PDF preview abandonment

---

## 🚀 Deployment Steps

1. **Pull latest code** → Contains all new files
2. **Run migration** → `php artisan migrate`
3. **Start queue** → `php artisan queue:work`
4. **Test manually** → `php artisan abandonment:send-reminders`
5. **Setup scheduler** → Cron job for `php artisan schedule:run`
6. **Monitor logs** → `storage/logs/laravel.log`

---

## 📝 Quick Reference

### Key Files to Know

| File | Purpose | Status |
|------|---------|--------|
| `AbandonedCart.php` | Database model | ✅ CREATED |
| `AbandonedCartService.php` | Business logic | ✅ CREATED |
| `SendAbandonedCartReminders.php` | Email job | ✅ CREATED |
| `AbandonmentTrackingController.php` | API endpoints | ✅ CREATED |
| `UserResumeController.php` | PDF tracking | ✅ MODIFIED |
| `register.blade.php` | Signup completion | ✅ MODIFIED |
| `web.php` | Routes | ✅ MODIFIED |
| `Kernel.php` | Scheduler | ✅ CREATED |

### Key Classes & Methods

```php
// AbandonedCart model
AbandonedCart::getPendingRecovery()      // Get carts needing email
AbandonedCart::isAbandonedFor($hours)    // Check if abandoned
AbandonedCart::markCompleted()           // Mark recovered

// Service layer
AbandonedCartService::trackIncompleteSignup($user)
AbandonedCartService::trackIncompleteResume($user, $id, $data)
AbandonedCartService::trackPdfPreviewAbandon($user, $id, $name, $score)
AbandonedCartService::markAsCompleted($userId, $type, $id)
AbandonedCartService::getStats()

// Notifications
IncompleteSignupReminder
IncompleteResumeReminder
PdfPreviewUpgradeReminder

// API Endpoints
POST /api/abandonment/track-signup
POST /api/abandonment/track-resume
GET  /api/abandonment/stats
```

---

**System architecture is complete and ready for deployment!** 🚀
