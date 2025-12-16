# Jobsease Indonesia Pricing - Stripe Integration Guide

## Overview
The new pricing structure has been implemented with Indonesian Rupiah (IDR) pricing and full Stripe Checkout integration.

## Pricing Structure

### User Subscriptions

#### 1. **Free Plan**
- Price: IDR 0 (Free forever)
- Features:
  - 1 CV creation (basic template)
  - Basic CV sections
  - Basic interview questions (read only)
  - View 5 jobs
  - Apply to 1 job
  - Ads shown

#### 2. **Pro Plan** ⭐ RECOMMENDED
- Monthly: IDR 49,000/month
- Yearly: IDR 399,000/year (Save IDR 189,000)
- Features:
  - Unlimited CVs
  - Premium templates
  - AI CV improvement
  - Resume score + suggestions
  - Unlimited job viewing
  - Unlimited job apply
  - AI interview practice
  - Interview score & feedback
  - No ads

#### 3. **Career Pro+ Plan**
- Monthly: IDR 99,000/month
- Yearly: IDR 699,000/year (Save IDR 489,000)
- Features:
  - Everything in Pro
  - Priority job matching
  - Advanced interview questions (role-based)
  - Mock interview simulation
  - Discounts on human interview sessions
  - Priority support
  - Custom branding

### Human Interview Sessions (One-Time Purchase)

#### 1. **30-Minute Session**
- Price: IDR 200,000
- Features:
  - 30 minutes with expert interviewer
  - Personalized feedback
  - Recording available
  - Written report

#### 2. **60-Minute Session**
- Price: IDR 400,000
- Features:
  - 60 minutes with expert interviewer
  - In-depth personalized feedback
  - Recording available
  - Detailed written report
  - Follow-up email support

---

## How to Set Up

### 1. Run the Database Seeder

```bash
php artisan db:seed --class=JobseaseIndonesiaPricingSeeder
```

This will:
- Create/update the 3 subscription plans (Free, Pro, Pro+)
- Create the 2 human interview add-ons (30-min, 60-min)
- Clear old pricing data

### 2. Access the New Pricing Page

The pricing page will automatically use the new design:
- URL: `/user/pricing`
- File: `resources/views/user/pricing-new.blade.php`

### 3. Configure Stripe

The system automatically detects IDR vs USD based on the amount:
- Amounts >= 1000 = IDR (no decimal conversion)
- Amounts < 1000 = USD (converted to cents)

---

## How Stripe Checkout Works

### Flow for Users:

1. **User clicks "Upgrade to Pro" or "Upgrade to Pro+"**
   - If not logged in → Redirects to register page
   - If logged in → Proceeds to checkout

2. **Billing Period Selection**
   - User toggles between Monthly/Yearly
   - Price updates dynamically
   - Savings badge shown for yearly

3. **Stripe Checkout Submission**
   - JavaScript creates a form with:
     - `_token` (CSRF)
     - `plan_id` (from database)
     - `billing_period` (monthly/yearly)
   - Submits to: `POST /payment/stripe/checkout`

4. **Backend Processing** (`PaymentController@stripeCheckout`)
   - Validates plan and billing period
   - Detects currency (IDR/USD)
   - Creates Stripe Checkout Session
   - Redirects user to Stripe payment page

5. **User Completes Payment on Stripe**
   - Stripe hosted checkout page
   - Secure card payment
   - Returns to success URL

6. **Success Callback** (`PaymentController@stripeSuccess`)
   - Retrieves session from Stripe
   - Creates `UserSubscription` record
   - Creates `Payment` record
   - Activates subscription
   - Redirects to resumes page with success message

---

## Important Routes

### Frontend Routes:
- **Pricing Page**: `GET /user/pricing` → `SubscriptionController@pricing`
- **Register**: `GET /register` (for non-logged-in users)

### Payment Routes:
- **Stripe Checkout**: `POST /payment/stripe/checkout` → `PaymentController@stripeCheckout`
- **Stripe Success**: `GET /payment/stripe/success` → `PaymentController@stripeSuccess`
- **Stripe Cancel**: User returns to `/user/pricing`

### Subscription Routes:
- **Dashboard**: `GET /subscription/dashboard` → `SubscriptionController@dashboard`
- **Cancel**: `POST /subscription/cancel` → `SubscriptionController@cancel`

---

## Testing Stripe Integration

### Test Card Numbers (Stripe Test Mode):

**Successful Payment:**
- Card: `4242 4242 4242 4242`
- Expiry: Any future date (e.g., 12/34)
- CVC: Any 3 digits (e.g., 123)
- ZIP: Any 5 digits (e.g., 12345)

**Declined Payment:**
- Card: `4000 0000 0000 0002`

**Requires Authentication (3D Secure):**
- Card: `4000 0025 0000 3155`

### Test Workflow:

1. Go to `/user/pricing`
2. Toggle Monthly/Yearly billing
3. Click "Upgrade to Pro"
4. Should redirect to Stripe Checkout
5. Enter test card details
6. Complete payment
7. Should redirect back with success message
8. Check `/subscription/dashboard` to verify active subscription

---

## Files Modified/Created

### New Files:
1. `database/seeders/JobseaseIndonesiaPricingSeeder.php` - Pricing data seeder
2. `resources/views/user/pricing-new.blade.php` - New pricing page with Stripe integration
3. `PRICING_STRIPE_GUIDE.md` - This guide

### Modified Files:
1. `app/Http/Controllers/User/PaymentController.php`
   - Added IDR currency support
   - Fixed amount conversion for IDR (no decimals)
   
2. `app/Http/Controllers/User/SubscriptionController.php`
   - Updated to serve new pricing page
   - Handle both logged-in and guest users

3. `app/Models/AddOn.php` - Already supports interview sessions

---

## Next Steps

### 1. Update Stripe Dashboard
- Create products in Stripe for each plan
- Set up price IDs for monthly/yearly
- Update seeder with actual Stripe Price IDs:
  ```php
  'stripe_monthly_price_id' => 'price_XXXXX',
  'stripe_yearly_price_id' => 'price_YYYYY',
  ```

### 2. Implement Interview Booking
- Create booking flow for 30-min and 60-min sessions
- Integration with calendar system
- Payment flow similar to subscriptions

### 3. Configure Stripe Webhooks
- Set up webhook endpoint for subscription events
- Handle subscription renewals
- Handle subscription cancellations
- Handle payment failures

### 4. Add PayPal Option (Optional)
- Similar flow to Stripe
- Update pricing page with PayPal button

---

## Environment Variables

Make sure these are set in `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

For production, use live keys:
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
```

---

## Troubleshooting

### "Plan not found" Error:
- Run the seeder: `php artisan db:seed --class=JobseaseIndonesiaPricingSeeder`
- Clear cache: `php artisan cache:clear`

### Stripe Session Creation Fails:
- Check Stripe API keys in `.env`
- Verify Stripe account is active
- Check logs: `storage/logs/laravel.log`

### Payment Success but No Subscription:
- Check `UserSubscription` table
- Check `Payment` table
- Review logs for errors in success callback

### Currency Issues:
- IDR amounts should be whole numbers (49000, not 490.00)
- USD amounts are in cents (4900 = $49.00)
- Controller automatically detects currency

---

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Stripe Dashboard for payment details
3. Review database records (`subscription_plans`, `user_subscriptions`, `payments`)
