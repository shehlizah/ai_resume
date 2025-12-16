# Jobsease Pricing Page - Setup & User Flow Guide

## Quick Answers to Your Questions

### 1️⃣ **If user is NOT logged in, what happens?**

When a guest user visits `/user/pricing`:

- ✅ **They CAN see all pricing plans and features**
- ✅ **Free plan button** → Takes them to `/register` page
- ✅ **Pro/Pro+ buttons** → Takes them to `/register` page to create account
- ✅ **Interview booking buttons** → Takes them to `/register` page
- ℹ️ **Top banner message** → Shows "Create an account to get started"

**Flow:**
```
Guest visits /pricing → Sees all plans → Clicks "Upgrade to Pro" → Redirected to /register → Creates account → Can then subscribe
```

---

### 2️⃣ **If user IS logged in, will it get their details?**

Yes! The pricing page shows:

✅ **Current subscription status** in the hero banner:
- If subscribed: "You're currently on the **Pro Plan**"
- If free: "You're currently on the **Free Plan**"
- If plan was deleted: Shows appropriate message

✅ **Button behavior changes**:
- If user already has that plan → Button shows "✓ Current Plan" (disabled)
- If user has different plan → Button shows "Upgrade to [Plan Name]"
- Free plan → Always shows "Get Started Free"

✅ **User data stored** in database:
- Active subscription details
- Subscription history
- Payment records
- All accessible from `/subscription/dashboard`

---

### 3️⃣ **"Plan not found" Error - How to Fix**

**This happens because NO PLANS exist in the database yet.**

#### Step 1: Run the Seeder (Initialize Plans)

```bash
php artisan db:seed --class=JobseaseIndonesiaPricingSeeder
```

**What this does:**
- ✅ Creates "Free" plan (IDR 0)
- ✅ Creates "Pro" plan (IDR 49K/month, IDR 399K/year)
- ✅ Creates "Career Pro+" plan (IDR 99K/month, IDR 699K/year)
- ✅ Creates interview add-ons (IDR 200K, IDR 400K)

#### Step 2: Verify Plans Were Created

```bash
php artisan tinker
```

Then run:
```php
App\Models\SubscriptionPlan::all()->pluck('name', 'id');
```

You should see:
```
{
  1: "Free",
  2: "Pro",
  3: "Career Pro+"
}
```

#### Step 3: Visit Pricing Page

- **URL**: `http://localhost/user/pricing`
- **Expected**: All 3 plans displayed with full details

---

## Complete User Flows

### Flow A: Guest User (Not Logged In)

```
1. Guest visits /user/pricing
   ↓
2. Sees all plans (Free, Pro, Pro+)
   ↓
3. Clicks "Choose Pro" or "Upgrade to Pro+"
   ↓
4. Redirected to /register
   ↓
5. Creates account (email, password)
   ↓
6. Auto-logged in
   ↓
7. Redirected to /user/pricing (now logged in)
   ↓
8. Clicks plan button again
   ↓
9. Stripe checkout session created
   ↓
10. Stripe payment page
    ↓
11. Completes payment
    ↓
12. Subscription activated
    ↓
13. Redirected to /resumes with success message
```

---

### Flow B: Logged-In User (Free Plan)

```
1. Free user visits /user/pricing
   ↓
2. Hero banner: "You're currently on the Free Plan"
   ↓
3. Free plan card shows: "✓ Current Plan" (button disabled)
   ↓
4. Pro plan shows: "Upgrade to Pro"
   ↓
5. Clicks "Upgrade to Pro"
   ↓
6. Stripe checkout session created
   ↓
7. Stripe payment page
   ↓
8. Selects monthly (IDR 49,000) or yearly (IDR 399,000)
   ↓
9. Completes payment
   ↓
10. UserSubscription record created
    ↓
11. Payment record created
    ↓
12. Subscription activated
    ↓
13. Redirected to /resumes with success message
```

---

### Flow C: Logged-In User (Already Subscribed)

```
1. Pro user visits /user/pricing
   ↓
2. Hero banner: "You're currently on the Pro Plan"
   ↓
3. Pro plan card shows: "✓ Current Plan" (button disabled)
   ↓
4. Career Pro+ shows: "Upgrade to Career Pro+"
   ↓
5. Can click to upgrade to higher tier
   ↓
6. New subscription replaces old one
```

---

## Database Schema Understanding

### SubscriptionPlans Table
```
id    | name           | slug      | monthly_price | yearly_price | features (JSON)
------|----------------|-----------|---------------|--------------|----------------
1     | Free           | free      | 0.00          | 0.00         | [array of features]
2     | Pro            | pro       | 49000.00      | 399000.00    | [array of features]
3     | Career Pro+    | pro-plus  | 99000.00      | 699000.00    | [array of features]
```

### UserSubscriptions Table (Created after payment)
```
id | user_id | subscription_plan_id | billing_period | status   | start_date | end_date   | payment_gateway
---|---------|----------------------|----------------|----------|------------|------------|----------------
1  | 5       | 2                    | monthly        | active   | 2025-01-01 | 2025-02-01 | stripe
2  | 7       | 3                    | yearly         | active   | 2025-01-01 | 2026-01-01 | stripe
```

### Payments Table (Transaction history)
```
id | user_id | user_subscription_id | transaction_id | amount  | currency | status    | paid_at
---|---------|----------------------|----------------|---------|----------|-----------|----------
1  | 5       | 1                    | cs_test_123... | 49000   | IDR      | completed | 2025-01-01
```

---

## Important Files

| File | Purpose |
|------|---------|
| `resources/views/user/pricing-new.blade.php` | The pricing page UI with Stripe integration |
| `app/Http/Controllers/User/SubscriptionController.php` | Handles pricing page & dashboard display |
| `app/Http/Controllers/User/PaymentController.php` | Handles Stripe checkout & payments |
| `database/seeders/JobseaseIndonesiaPricingSeeder.php` | Creates pricing plans in database |
| `app/Models/SubscriptionPlan.php` | Plan model |
| `app/Models/UserSubscription.php` | User subscription model |

---

## Troubleshooting

### Problem: "Plan not found" on pricing page

**Solution:**
```bash
php artisan db:seed --class=JobseaseIndonesiaPricingSeeder
```

### Problem: Plans show but clicking button does nothing

**Solution:**
1. Check browser console (F12) for errors
2. Verify user is logged in: Check `auth()->check()`
3. Verify Stripe API keys in `.env`:
   ```env
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   ```

### Problem: Stripe session not created

**Solution:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify Stripe keys are correct
3. Make sure `billing_period` is `monthly` or `yearly`

### Problem: User not redirected after payment

**Solution:**
1. Check Stripe dashboard for payment status
2. Check `user_subscriptions` table for record
3. Verify `PaymentController@stripeSuccess` is being called
4. Check logs for any errors

---

## Testing Checklist

- [ ] Run seeder: `php artisan db:seed --class=JobseaseIndonesiaPricingSeeder`
- [ ] Visit `/user/pricing` (guest)
- [ ] See all 3 plans displayed
- [ ] Click "Choose Pro" → Redirected to register ✓
- [ ] Register new account
- [ ] Logged in, back at pricing page
- [ ] See "You're currently on the Free Plan" ✓
- [ ] Click "Upgrade to Pro"
- [ ] Redirected to Stripe checkout ✓
- [ ] Enter test card: `4242 4242 4242 4242`
- [ ] Complete payment
- [ ] Redirected to `/resumes` with success message ✓
- [ ] Check `/subscription/dashboard` shows active subscription ✓
- [ ] Pricing page shows "✓ Current Plan" for Pro ✓

---

## Design & Customization

### Current Design Features:
- ✅ Hero section with blue gradient
- ✅ Billing toggle (Monthly/Yearly)
- ✅ 3 pricing cards with hover effects
- ✅ "Recommended" badge on Pro plan
- ✅ Dynamic price updates based on billing period
- ✅ Feature lists from database
- ✅ Interview sessions add-on section
- ✅ Responsive (mobile, tablet, desktop)

### To Customize Colors:
Edit the CSS variables in `pricing-new.blade.php`:
```css
:root {
    --primary: #007BFF;      /* Change main color */
    --success: #10B981;      /* Change success color */
    --dark: #1E293B;         /* Change dark text */
    --light: #F8FAFC;        /* Change light background */
}
```

---

## Next Steps

1. **Run seeder**: `php artisan db:seed --class=JobseaseIndonesiaPricingSeeder`
2. **Test pricing page**: Visit `/user/pricing`
3. **Test checkout**: Register, click plan, complete Stripe payment
4. **Verify subscription**: Check `/subscription/dashboard`
5. **Customize design**: Update colors/text in pricing-new.blade.php
6. **Set up webhooks**: Configure Stripe webhooks for subscription events (optional)

---

Questions? Check the logs or reach out! 🚀
