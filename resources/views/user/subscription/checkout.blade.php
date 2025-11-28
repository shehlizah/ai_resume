<x-layouts.app :title="$title ?? 'Checkout'">
  <div class="container-xxl flex-grow-1 container-p-y">

    <div class="row justify-content-center">
      <div class="col-lg-8">

        <!-- Header -->
        <div class="mb-4">
          <a href="{{ route('user.pricing') }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bx bx-arrow-back"></i> Back to Pricing
          </a>
          <h4 class="fw-bold">Complete Your Purchase</h4>
        </div>

        <div class="row">
          <!-- Order Summary -->
          <div class="col-md-5 mb-4 mb-md-0">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <h4>{{ $plan->name }} Plan</h4>
                  <p class="text-muted mb-0">{{ $plan->description }}</p>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                  <div class="d-flex justify-content-between mb-2">
                    <span>Billing Period:</span>
                    <strong>{{ ucfirst($billingPeriod) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Amount:</span>
                    <h4 class="text-primary mb-0">${{ number_format($amount, 2) }}</h4>
                  </div>
                  @if($billingPeriod === 'yearly' && $plan->getSavingsPercentage() > 0)
                    <div class="mt-2">
                      <span class="badge bg-success">
                        Save {{ $plan->getSavingsPercentage() }}% (${{ number_format($plan->getYearlySavings(), 2) }})
                      </span>
                    </div>
                  @endif
                </div>

                <div class="mb-0">
                  <h6 class="mb-3">What's Included:</h6>
                  <ul class="list-unstyled mb-0">
                    @if($plan->template_limit)
                      <li class="mb-2">
                        <i class="bx bx-check text-success me-1"></i>
                        {{ $plan->template_limit }} resume limit
                      </li>
                    @else
                      <li class="mb-2">
                        <i class="bx bx-check text-success me-1"></i>
                        Unlimited resumes
                      </li>
                    @endif

                    @if($plan->access_premium_templates)
                      <li class="mb-2">
                        <i class="bx bx-check text-success me-1"></i>
                        Premium templates
                      </li>
                    @endif

                    @if($plan->priority_support)
                      <li class="mb-2">
                        <i class="bx bx-check text-success me-1"></i>
                        Priority support
                      </li>
                    @endif

                    @if($plan->features && is_array($plan->features))
                      @foreach(array_slice($plan->features, 0, 3) as $feature)
                        <li class="mb-2">
                          <i class="bx bx-check text-success me-1"></i>
                          {{ $feature }}
                        </li>
                      @endforeach
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="col-md-7">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Choose Payment Method</h5>
              </div>
              <div class="card-body">

                @if($currentSubscription)
                  <div class="alert alert-info mb-4">
                    <i class="bx bx-info-circle me-2"></i>
                    You currently have an active <strong>{{ $currentSubscription->plan->name }}</strong> plan.
                    This will be replaced with the new plan.
                  </div>
                @endif

                <!-- Stripe Payment -->
                <div class="payment-option mb-3">
                  <div class="border rounded p-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                      <i class="bx bxl-stripe display-6 text-primary me-3"></i>
                      <div>
                        <h5 class="mb-0">Credit/Debit Card</h5>
                        <small class="text-muted">Powered by Stripe</small>
                      </div>
                    </div>
                    <p class="text-muted mb-3">
                      Pay securely with your credit or debit card. All major cards accepted.
                    </p>
                    <form action="/api/test-submit" method="POST" id="stripeCheckoutForm">
                      @csrf
                      <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                      <input type="hidden" name="billing_period" value="{{ $billingPeriod }}">
                      <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-lock-alt me-2"></i>
                        Pay ${{ number_format($amount, 2) }} with Stripe (TEST)
                      </button>
                    </form>
                  </div>
                </div>

                <!-- PayPal Payment -->
                <!--<div class="payment-option">-->
                <!--  <div class="border rounded p-4">-->
                <!--    <div class="d-flex align-items-center mb-3">-->
                <!--      <i class="bx bxl-paypal display-6 text-info me-3"></i>-->
                <!--      <div>-->
                <!--        <h5 class="mb-0">PayPal</h5>-->
                <!--        <small class="text-muted">Fast and secure</small>-->
                <!--      </div>-->
                <!--    </div>-->
                <!--    <p class="text-muted mb-3">-->
                <!--      Pay with your PayPal account or credit card through PayPal.-->
                <!--    </p>-->
                <!--    <form action="{{ route('user.payment.paypal.checkout') }}" method="POST">-->
                <!--      @csrf-->
                <!--      <input type="hidden" name="plan_id" value="{{ $plan->id }}">-->
                <!--      <input type="hidden" name="billing_period" value="{{ $billingPeriod }}">-->
                <!--      <button type="submit" class="btn btn-info w-100">-->
                <!--        <i class="bx bxl-paypal me-2"></i>-->
                <!--        Pay ${{ number_format($amount, 2) }} with PayPal-->
                <!--      </button>-->
                <!--    </form>-->
                <!--  </div>-->
                <!--</div>-->

                <!-- Security Info -->
                <div class="mt-4 text-center">
                  <small class="text-muted">
                    <i class="bx bx-lock-alt me-1"></i>
                    Your payment information is secure and encrypted
                  </small>
                </div>

              </div>
            </div>

            <!-- Money Back Guarantee -->
            <div class="card mt-3 bg-light">
              <div class="card-body text-center">
                <i class="bx bx-shield-quarter display-4 text-success mb-2"></i>
                <h6>14-Day Money-Back Guarantee</h6>
                <p class="text-muted mb-0 small">
                  Not satisfied? Get a full refund within 14 days, no questions asked.
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</x-layouts.app>
