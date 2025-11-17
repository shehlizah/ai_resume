# Stripe Integration Setup Guide

## Step 1: Get Your Stripe Price IDs

1. Go to https://dashboard.stripe.com/products
2. Find each plan (Basic, Premium, etc.)
3. For each plan, you'll see prices listed (Monthly, Yearly)
4. Click on each price to see the **Price ID** (looks like: `price_1234567890abcdef`)

Example:
```
Basic Plan:
- Monthly Price ID: price_1STdoCDfpo67wO4dX6Kr1bDX
- Yearly Price ID: price_1STdoCDfpo67wO4dX6Kr1bDX_yearly

Premium Plan:
- Monthly Price ID: price_1STdoCDfpo67wO4dZ7Ls2cEY
- Yearly Price ID: price_1STdoCDfpo67wO4dZ7Ls2cEY_yearly
```

## Step 2: Update Your Database

**Option A: Using the Seeder (Recommended)**

1. Edit `database/seeders/UpdateStripepriceIds.php`
2. Replace the placeholder Price IDs with your actual Stripe Price IDs:

```php
SubscriptionPlan::where('slug', 'basic')->update([
    'stripe_monthly_price_id' => 'price_YOUR_BASIC_MONTHLY_ID',
    'stripe_yearly_price_id' => 'price_YOUR_BASIC_YEARLY_ID',
]);
```

3. Run the seeder:
```bash
php artisan db:seed --class=UpdateStripepriceIds
```

**Option B: Direct Database Update**

SSH into your server and run:
```bash
mysql -u username -p database_name
UPDATE subscription_plans SET stripe_monthly_price_id='price_1234567890' WHERE slug='basic';
UPDATE subscription_plans SET stripe_yearly_price_id='price_0987654321' WHERE slug='basic';
UPDATE subscription_plans SET stripe_monthly_price_id='price_abcdefghij' WHERE slug='premium';
UPDATE subscription_plans SET stripe_yearly_price_id='price_jihgfedcba' WHERE slug='premium';
```

## Step 3: Run the Migration

```bash
php artisan migrate
```

## Step 4: Verify in Database

Check that the Price IDs are saved:
```bash
php artisan tinker
>>> App\Models\SubscriptionPlan::all();
```

You should see the `stripe_monthly_price_id` and `stripe_yearly_price_id` columns populated.

## Step 5: Configure Stripe Webhook

1. Go to https://dashboard.stripe.com/webhooks
2. Add endpoint: `https://sneat.hitechmain.com/webhooks/stripe`
3. Select events:
   - customer.subscription.created
   - customer.subscription.updated
   - customer.subscription.deleted
   - charge.succeeded
   - charge.failed
4. Copy the **Signing secret** and add to `.env`:
   ```
   STRIPE_WEBHOOK_SECRET=whsec_1234567890...
   ```

## Step 6: Test the Webhook

1. In Stripe Dashboard, go to your webhook endpoint
2. Click "Send test event"
3. Select an event type
4. Check your server logs: `tail -100 storage/logs/laravel.log`

## Troubleshooting

If subscriptions still aren't being created:

1. Check logs for errors:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Verify webhook is receiving requests:
   ```bash
   grep "Webhook received" storage/logs/laravel.log
   ```

3. Verify Price IDs are in database:
   ```bash
   php artisan tinker
   >>> App\Models\SubscriptionPlan::where('slug', 'basic')->first();
   ```

4. Test webhook manually with Thunder Client using actual user email in test data
