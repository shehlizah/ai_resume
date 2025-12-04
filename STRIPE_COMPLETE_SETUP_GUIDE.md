# 🔥 Complete Stripe Setup Guide for AI Resume Builder

## 📋 Table of Contents
1. [Create Stripe Account](#1-create-stripe-account)
2. [Get API Keys](#2-get-api-keys)
3. [Configure Laravel Environment](#3-configure-laravel-environment)
4. [Install Stripe PHP Library](#4-install-stripe-php-library)
5. [Create Stripe Products & Prices](#5-create-stripe-products--prices)
6. [Configure Webhooks](#6-configure-webhooks)
7. [Test Payment Flow](#7-test-payment-flow)
8. [Go Live Checklist](#8-go-live-checklist)

---

## 1. Create Stripe Account

### Step 1.1: Sign Up
1. Go to https://stripe.com
2. Click **"Start now"** or **"Sign up"**
3. Fill in:
   - Email address
   - Full name
   - Country (e.g., United States, Pakistan, India)
   - Password
4. Verify your email address

### Step 1.2: Activate Your Account
1. Log into Stripe Dashboard
2. Click **"Activate your account"**
3. Provide:
   - Business type (Individual or Company)
   - Business details
   - Bank account information (for payouts)
   - Tax information

**Note:** You can start with **Test Mode** before completing activation.

---

## 2. Get API Keys

### Step 2.1: Access API Keys
1. Log into https://dashboard.stripe.com
2. Click **"Developers"** in the left sidebar
3. Click **"API keys"**

### Step 2.2: Copy Keys
You'll see 4 keys:

**Test Mode Keys:**
- **Publishable key (Test):** `pk_test_xxxxxxxxxxxxxxxxxxxxxx`
- **Secret key (Test):** `sk_test_xxxxxxxxxxxxxxxxxxxxxx`

**Live Mode Keys (after activation):**
- **Publishable key (Live):** `pk_live_xxxxxxxxxxxxxxxxxxxxxx`
- **Secret key (Live):** `sk_live_xxxxxxxxxxxxxxxxxxxxxx`

**⚠️ Important:**
- **Never** commit secret keys to Git
- Use **Test keys** for development
- Switch to **Live keys** only in production

---

## 3. Configure Laravel Environment

### Step 3.1: Update `.env` File
```bash
# Open your .env file
notepad .env
```

### Step 3.2: Add Stripe Configuration
```env
# Stripe Configuration
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxx

# Stripe Currency (USD, EUR, GBP, etc.)
STRIPE_CURRENCY=usd

# Your Application URL (for webhooks)
APP_URL=http://localhost:8000
```

### Step 3.3: Update `config/services.php`
```php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],
],
```

### Step 3.4: Clear Configuration Cache
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 4. Install Stripe PHP Library

### Step 4.1: Install via Composer
```bash
composer require stripe/stripe-php
```

### Step 4.2: Verify Installation
```bash
composer show stripe/stripe-php
```

You should see something like:
```
stripe/stripe-php v10.x.x
```

---

## 5. Create Stripe Products & Prices

### Method 1: Via Stripe Dashboard (Recommended)

#### Step 5.1: Create Products
1. Go to https://dashboard.stripe.com/test/products
2. Click **"Add product"**

**Create Free Plan:**
- Name: `Free Plan`
- Description: `Basic features with limited access`
- Click **"Add pricing"**
  - Model: `Standard pricing`
  - Price: `$0.00`
  - Billing period: `Monthly`
  - Currency: `USD`
- Click **"Save product"**
- Copy the **Price ID**: `price_xxxxxxxxxxxxxxxxxx`

**Create Basic Plan:**
- Name: `Basic Plan`
- Description: `Essential features for job seekers`
- Click **"Add pricing"**
  - Model: `Standard pricing`
  - Price: `$4.99`
  - Billing period: `Monthly`
  - Currency: `USD`
- Add another price:
  - Price: `$49.99`
  - Billing period: `Yearly`
  - Currency: `USD`
- Click **"Save product"**
- Copy both **Price IDs**

**Create Premium Plan:**
- Name: `Premium Plan`
- Description: `All features + AI-powered tools`
- Click **"Add pricing"**
  - Model: `Standard pricing`
  - Price: `$9.99`
  - Billing period: `Monthly`
  - Currency: `USD`
- Add another price:
  - Price: `$99.99`
  - Billing period: `Yearly`
  - Currency: `USD`
- Click **"Save product"**
- Copy both **Price IDs**

#### Step 5.2: Update Database with Price IDs

Run this SQL in your database or via Laravel Tinker:

```sql
-- Update subscription_plans table with Stripe Price IDs
UPDATE subscription_plans 
SET stripe_price_id = 'price_1234567890abcdef' 
WHERE name = 'Free' AND billing_period = 'monthly';

UPDATE subscription_plans 
SET stripe_price_id = 'price_2234567890abcdef' 
WHERE name = 'Basic' AND billing_period = 'monthly';

UPDATE subscription_plans 
SET stripe_price_id = 'price_3234567890abcdef' 
WHERE name = 'Basic' AND billing_period = 'yearly';

UPDATE subscription_plans 
SET stripe_price_id = 'price_4234567890abcdef' 
WHERE name = 'Premium' AND billing_period = 'monthly';

UPDATE subscription_plans 
SET stripe_price_id = 'price_5234567890abcdef' 
WHERE name = 'Premium' AND billing_period = 'yearly';
```

**Via Laravel Tinker:**
```bash
php artisan tinker
```

```php
DB::table('subscription_plans')->where('name', 'Basic')->where('billing_period', 'monthly')->update(['stripe_price_id' => 'price_1234567890abcdef']);
DB::table('subscription_plans')->where('name', 'Basic')->where('billing_period', 'yearly')->update(['stripe_price_id' => 'price_2234567890abcdef']);
DB::table('subscription_plans')->where('name', 'Premium')->where('billing_period', 'monthly')->update(['stripe_price_id' => 'price_3234567890abcdef']);
DB::table('subscription_plans')->where('name', 'Premium')->where('billing_period', 'yearly')->update(['stripe_price_id' => 'price_4234567890abcdef']);
```

### Method 2: Via Stripe API (Programmatic)

```php
// In tinker or a seeder
\Stripe\Stripe::setApiKey(config('services.stripe.secret'));

// Create Basic Monthly Plan
$basicMonthly = \Stripe\Price::create([
    'unit_amount' => 499, // $4.99 in cents
    'currency' => 'usd',
    'recurring' => ['interval' => 'month'],
    'product_data' => [
        'name' => 'Basic Plan',
        'description' => 'Essential features for job seekers',
    ],
]);

echo "Basic Monthly Price ID: " . $basicMonthly->id;
```

---

## 6. Configure Webhooks

### Step 6.1: Install ngrok (for local testing)

**Windows:**
```bash
# Download from https://ngrok.com/download
# Or use Chocolatey:
choco install ngrok

# Start ngrok tunnel
ngrok http 8000
```

**Mac/Linux:**
```bash
brew install ngrok
ngrok http 8000
```

You'll see output like:
```
Forwarding  https://abc123.ngrok.io -> http://localhost:8000
```

### Step 6.2: Create Webhook in Stripe

1. Go to https://dashboard.stripe.com/test/webhooks
2. Click **"Add endpoint"**
3. Enter endpoint URL:
   - **Local:** `https://abc123.ngrok.io/stripe/webhook`
   - **Production:** `https://yourdomain.com/stripe/webhook`
4. Select events to listen to:
   - `checkout.session.completed`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `charge.succeeded`
   - `charge.failed`
5. Click **"Add endpoint"**

### Step 6.3: Copy Webhook Secret

1. Click on your newly created webhook
2. Click **"Signing secret"** → **"Reveal"**
3. Copy the secret: `whsec_xxxxxxxxxxxxxxxxxxxxxx`
4. Add to `.env`:
```env
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxx
```

### Step 6.4: Clear Config Cache
```bash
php artisan config:clear
```

---

## 7. Test Payment Flow

### Step 7.1: Use Test Cards

Stripe provides test cards for different scenarios:

**Successful Payment:**
- Card: `4242 4242 4242 4242`
- Expiry: Any future date (e.g., `12/34`)
- CVC: Any 3 digits (e.g., `123`)
- ZIP: Any 5 digits (e.g., `12345`)

**Declined Payment:**
- Card: `4000 0000 0000 0002`

**Requires Authentication (3D Secure):**
- Card: `4000 0025 0000 3155`

**Insufficient Funds:**
- Card: `4000 0000 0000 9995`

### Step 7.2: Test Subscription Flow

1. Start your Laravel app:
```bash
php artisan serve
```

2. Start ngrok (if testing locally):
```bash
ngrok http 8000
```

3. Go to pricing page:
```
http://localhost:8000/pricing
```

4. Click **"Subscribe"** on any plan

5. Use test card: `4242 4242 4242 4242`

6. Complete payment

7. Check:
   - User subscription status in database
   - Stripe Dashboard → Customers
   - Webhook logs in Stripe Dashboard

### Step 7.3: Verify Database

```bash
php artisan tinker
```

```php
// Check user subscription
$user = User::find(1);
$user->activeSubscription;

// Check payments
Payment::latest()->first();

// Check webhook logs
DB::table('webhook_logs')->latest()->get();
```

---

## 8. Go Live Checklist

### ✅ Before Going Live

- [ ] Complete Stripe account activation
- [ ] Add business information
- [ ] Verify bank account for payouts
- [ ] Submit tax information
- [ ] Update `.env` with **Live API keys**
- [ ] Create Live products and prices
- [ ] Update webhook URL to production domain
- [ ] Test subscription flow on staging
- [ ] Set up email notifications for failed payments
- [ ] Configure payment retry logic
- [ ] Add terms of service and privacy policy
- [ ] Set up customer support email
- [ ] Enable receipt emails in Stripe Dashboard
- [ ] Configure invoice settings
- [ ] Set up fraud prevention rules (Radar)

### Step 8.1: Switch to Live Mode

**Update `.env`:**
```env
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_live_xxxxxxxxxxxxxxxxxxxxxx
```

**Clear caches:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 8.2: Update Webhook URL

1. Go to https://dashboard.stripe.com/webhooks
2. Switch to **"Live mode"** (toggle in top right)
3. Click **"Add endpoint"**
4. Enter: `https://yourdomain.com/stripe/webhook`
5. Select same events as test mode
6. Copy webhook secret → update `.env`

### Step 8.3: Test Live Payment

1. Use a **real card** (or your own test card)
2. Complete subscription
3. Verify payment in Stripe Dashboard
4. Check webhook delivery
5. Confirm user access granted

---

## 🔧 Troubleshooting

### Issue 1: "No such price"
**Solution:** Verify `stripe_price_id` in `subscription_plans` table matches Stripe Dashboard.

```sql
SELECT id, name, billing_period, stripe_price_id FROM subscription_plans;
```

### Issue 2: Webhook not receiving events
**Solutions:**
- Check ngrok is running: `ngrok http 8000`
- Verify webhook URL in Stripe Dashboard
- Check webhook secret in `.env`
- Look at Stripe Dashboard → Webhooks → Recent deliveries

### Issue 3: "Invalid API Key"
**Solutions:**
- Run `php artisan config:clear`
- Verify `.env` has correct keys
- Check if using test keys with live endpoint (or vice versa)

### Issue 4: Payment succeeds but subscription not created
**Solutions:**
- Check `app/Http/Controllers/User/SubscriptionController.php`
- Verify `Payment` and `UserSubscription` models are saving correctly
- Check Laravel logs: `storage/logs/laravel.log`
- Check Stripe webhook logs in Dashboard

### Issue 5: "Customer not found"
**Solution:** Ensure `stripe_customer_id` is saved to users table after first payment.

```php
// In SubscriptionController@subscribe
$user->stripe_customer_id = $session->customer;
$user->save();
```

---

## 📞 Support Resources

- **Stripe Documentation:** https://stripe.com/docs
- **Stripe API Reference:** https://stripe.com/docs/api
- **Test Cards:** https://stripe.com/docs/testing
- **Webhook Testing:** https://stripe.com/docs/webhooks/test
- **Stripe Support:** https://support.stripe.com

---

## 🎯 Quick Start Commands

```bash
# 1. Install Stripe
composer require stripe/stripe-php

# 2. Clear caches
php artisan config:clear && php artisan cache:clear

# 3. Start app
php artisan serve

# 4. Start ngrok (new terminal)
ngrok http 8000

# 5. Update webhook URL in Stripe Dashboard with ngrok URL

# 6. Test payment with card: 4242 4242 4242 4242
```

---

## ✅ Success Indicators

Your Stripe integration is working if:

1. ✅ User can click "Subscribe" on pricing page
2. ✅ Stripe Checkout opens successfully
3. ✅ Payment completes without errors
4. ✅ User is redirected back to app
5. ✅ Subscription shows as "active" in database
6. ✅ User has access to premium features
7. ✅ Webhook events appear in Stripe Dashboard
8. ✅ Payment record exists in `payments` table
9. ✅ Subscription record exists in `user_subscriptions` table
10. ✅ Customer appears in Stripe Dashboard → Customers

---

## 🚀 Next Steps

After successful setup:

1. **Test cancellation flow**
2. **Test plan upgrades/downgrades**
3. **Set up email notifications**
4. **Configure payment retry logic**
5. **Add subscription management UI**
6. **Implement proration for plan changes**
7. **Set up analytics tracking**

---

**Need Help?** Check the Stripe Dashboard logs and Laravel logs for detailed error messages.
