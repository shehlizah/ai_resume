# Lifetime Pro Access Feature

## Overview
This feature allows admins to grant specific users full Pro access to all features without requiring an active subscription.

## Database Changes

### Migration File
`database/migrations/2025_12_02_000000_add_has_lifetime_access_to_users_table.php`

Adds `has_lifetime_access` boolean column to users table (default: false)

### Run Migration
```bash
php artisan migrate
```

## How It Works

### 1. User Model
- Added `has_lifetime_access` to `$fillable` array
- Added boolean cast for the field

### 2. Access Checks Updated
All premium access checks now use:
```php
$hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');
```

**Updated Files:**
- `app/Http/Controllers/User/DashboardController.php`
- `app/Http/Controllers/User/JobFinderController.php` (5 methods)
- `app/Http/Controllers/User/InterviewPrepController.php` (3 methods)
- `resources/views/livewire/settings/monetization.blade.php`

### 3. Admin Interface
**Admin Users Page:** `/admin/users`

New features:
- **Status Column:** Shows "Lifetime Pro" badge for users with lifetime access
- **Crown Button:** Toggle lifetime access on/off for any user
- Color-coded:
  - Blue/Primary when granted
  - Red/Danger to revoke

### 4. API Endpoint
**Route:** `POST /admin/users/{id}/toggle-lifetime-access`
**Controller:** `App\Http\Controllers\Admin\UserController@toggleLifetimeAccess`

## Usage

### Option 1: Admin Interface (Recommended)
1. Go to `/admin/users`
2. Find the user
3. Click the crown icon button
4. Confirm the action
5. User instantly gets Pro access

### Option 2: Direct Database (Manual)
```sql
-- Grant lifetime access to a specific user
UPDATE users SET has_lifetime_access = 1 WHERE email = 'user@example.com';

-- Grant lifetime access by user ID
UPDATE users SET has_lifetime_access = 1 WHERE id = 123;

-- Revoke lifetime access
UPDATE users SET has_lifetime_access = 0 WHERE email = 'user@example.com';

-- Check users with lifetime access
SELECT id, name, email, has_lifetime_access, created_at 
FROM users 
WHERE has_lifetime_access = 1;
```

## Features Unlocked with Lifetime Access

Users with lifetime access get:

### ✅ Job Finder
- Unlimited job views (no 5 per session limit)
- Unlimited job applications (no 1 per session limit)

### ✅ Interview Prep
- 20-25 advanced AI questions (vs 5-8 basic for free)
- Technical topics to study
- Salary negotiation tips
- Access to AI Mock Interview
- Book expert interview sessions

### ✅ AI Features
- All AI-powered resume analysis
- Pro-level prompts with more detail
- Extended token limits (4000 vs 2000)

### ✅ General
- No upgrade prompts
- "Unlimited" badge shown instead of counters
- Full access to all Pro features

## Notes

- Lifetime access **bypasses** subscription checks completely
- Works even if user has no subscription or expired subscription
- Admin can toggle on/off anytime without affecting their data
- No expiration date - truly lifetime
- Perfect for:
  - Beta testers
  - Staff/team members
  - Special promotions
  - Partnerships
  - Compensation/giveaways

## Security

- Only admins can grant/revoke lifetime access
- Requires authentication and admin role
- CSRF protection on toggle endpoint
- Confirmation dialog before granting/revoking
- Audit trail in git commits (consider adding database logging)
