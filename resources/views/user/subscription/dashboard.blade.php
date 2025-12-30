<x-layouts.app :title="$title ?? 'My Subscription'">
  <style>
    .missing-features-list {
      background: #f8f9ff;
      border-left: 3px solid #667eea;
      padding: 1rem;
      border-radius: 0.375rem;
      margin-top: 1.5rem;
    }

    .missing-features-list li {
      padding: 0.5rem 0;
      color: #6c757d;
    }

    .missing-features-list .bx-lock {
      color: #667eea;
    }

    .urgency-note {
      background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%);
      border: 1px solid #667eea20;
      border-radius: 0.5rem;
      padding: 1rem 1.25rem;
      margin-top: 1.5rem;
    }

    .empty-payments-state {
      text-align: center;
      padding: 2rem 1rem;
    }

    .empty-payments-state .bx {
      font-size: 3rem;
      color: #667eea;
      opacity: 0.5;
    }

    .alert-benefit-focused {
      padding: 1rem 1.25rem;
      border-left: 4px solid #ffc107;
    }

    .card-compact {
      margin-bottom: 1.5rem;
    }

    .primary-cta-btn {
      background: #667eea;
      color: white;
      border: none;
      font-weight: 600;
      padding: 0.625rem 1.5rem;
    }

    .primary-cta-btn:hover {
      background: #5568d3;
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .secondary-cta-btn {
      color: #667eea;
      border-color: #667eea;
    }

    .plan-icon-size {
      font-size: 2.5rem;
    }
  </style>

  <div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header -->
    <h4 class="fw-bold mb-4">📊 My Subscription</h4>

    <!-- Current Plan Badge Card -->
    @php
      $activeSubscription = $user->activeSubscription;
      $currentPlan = $activeSubscription?->plan ?? null;
    @endphp

    @if($currentPlan && $activeSubscription)
      <div class="card bg-primary text-white mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-1">{{ $currentPlan->name }} Plan</h5>
            <p class="card-text mb-0">Active since {{ $activeSubscription->start_date->format('M d, Y') }}</p>
          </div>
          <div class="text-end">
            <div class="display-6 mb-1">${{ number_format($currentPlan->price ?? 0, 2) }}</div>
            <small>/{{ $activeSubscription->billing_period === 'yearly' ? 'year' : 'month' }}</small>
          </div>
        </div>
        <div class="card-footer bg-transparent border-top border-white border-opacity-25">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-white-50">Expires: <strong>{{ $activeSubscription->end_date->format('F j, Y') }}</strong></span>
            <a href="{{ route('user.pricing') }}" class="btn btn-sm btn-light text-primary">Upgrade Plan</a>
          </div>
        </div>
      </div>
    @elseif(!$currentPlan)
      <div class="alert alert-warning alert-benefit-focused alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
          <i class="bx bx-lock-open me-3" style="font-size: 1.5rem;"></i>
          <div>
            <strong>🔒 You're currently on the Free Plan</strong>
            <p class="mb-0 mt-1">Upgrade to unlock AI resume scoring, interview feedback, and premium templates.</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Success/Error Messages -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('info'))
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- ✅ TRIAL ALERT - NEW -->
    @if($currentSubscription && $currentSubscription->isInTrial())
      <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-info-circle me-2"></i>
        You're currently on a <strong>free trial</strong>.
        <strong>{{ $currentSubscription->trialDaysRemaining() }} days</strong> remaining.
        @if($currentSubscription->status === 'canceled')
          Your trial will end on <strong>{{ $currentSubscription->trial_end_date->format('F j, Y') }}</strong> and you won't be charged.
        @else
          You'll be charged <strong>${{ number_format($currentSubscription->amount, 2) }}</strong> on <strong>{{ $currentSubscription->trial_end_date->format('F j, Y') }}</strong>.
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    <!-- END TRIAL ALERT -->

    <div class="row">
      <!-- Current Subscription Card -->
      <div class="col-lg-8 mb-3">
        <div class="card card-compact">
          <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">Current Plan</h5>
            @if(!$currentSubscription || !$currentSubscription->plan)
            <a href="{{ route('user.pricing') }}" class="btn btn-sm primary-cta-btn">
              <i class="bx bx-rocket me-1"></i> Upgrade to Pro
            </a>
            @endif
          </div>
          <div class="card-body">
            @if($currentSubscription && $currentSubscription->plan)
              <!-- Active Subscription -->
              <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                  <h3 class="mb-1">{{ $currentSubscription->plan->name }} Plan</h3>
                  <p class="text-muted mb-2">{{ $currentSubscription->plan->description }}</p>

                  <div class="mb-2">
                    <span class="badge bg-{{ $currentSubscription->getStatusColor() }} me-2">
                      {{ ucfirst($currentSubscription->status) }}
                    </span>
                    <span class="badge bg-label-secondary">
                      {{ ucfirst($currentSubscription->billing_period) }} Billing
                    </span>
                    <!-- ✅ TRIAL BADGE - NEW -->
                    @if($currentSubscription->isInTrial())
                      <span class="badge bg-info">
                        <i class="bx bx-time-five"></i> Trial
                      </span>
                    @endif
                  </div>
                </div>
                <div class="text-end">
                  <h2 class="text-primary mb-0">${{ number_format($currentSubscription->amount, 2) }}</h2>
                  <small class="text-muted">/{{ $currentSubscription->billing_period === 'yearly' ? 'year' : 'month' }}</small>
                </div>
              </div>

              <!-- Subscription Details -->
              <div class="row mb-4">
                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block">Start Date</small>
                  <strong>{{ $currentSubscription->start_date->format('F j, Y') }}</strong>
                </div>
                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block">End Date</small>
                  <strong>{{ $currentSubscription->end_date->format('F j, Y') }}</strong>
                </div>

                <!-- ✅ TRIAL INFORMATION - NEW -->
                @if($currentSubscription->isInTrial())
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Trial Period</small>
                    <span class="badge bg-info">
                      <i class="bx bx-time-five me-1"></i>
                      {{ $currentSubscription->trialDaysRemaining() }} days left
                    </span>
                  </div>
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Trial Ends</small>
                    <strong>{{ $currentSubscription->trial_end_date->format('F j, Y') }}</strong>
                  </div>
                @else
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Days Remaining</small>
                    @php
                      $daysRemaining = \Carbon\Carbon::now()->diffInDays($currentSubscription->end_date);
                      if (\Carbon\Carbon::now()->greaterThan($currentSubscription->end_date)) {
                          $daysRemaining = 0;
                      }
                      $daysRemainingDisplay = substr((string)$daysRemaining, 0, 2);
                    @endphp
                    <strong>{{ $daysRemaining > 0 ? $daysRemainingDisplay . ' days' : 'Expired' }}</strong>
                  </div>
                @endif

                <!-- Next Billing (only show if not in trial or if trial and has next billing) -->
                @if($currentSubscription->amount > 0 && $currentSubscription->next_billing_date && !$currentSubscription->isInTrial())
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Next Billing</small>
                    <strong>{{ $currentSubscription->next_billing_date->format('F j, Y') }}</strong>
                  </div>
                @endif

                @if($currentSubscription->payment_gateway)
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Payment Method</small>
                    <span class="badge bg-{{ $currentSubscription->payment_gateway === 'stripe' ? 'primary' : 'info' }}">
                      {{ strtoupper($currentSubscription->payment_gateway) }}
                    </span>
                  </div>
                @endif
                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block">Auto Renew</small>
                  <strong>{{ $currentSubscription->auto_renew ? 'Yes' : 'No' }}</strong>
                </div>
              </div>

              @if($currentSubscription->isExpiringSoon() && !$currentSubscription->isInTrial())
                <div class="alert alert-warning mb-4">
                  <i class="bx bx-info-circle me-2"></i>
                  Your subscription is expiring soon on {{ $currentSubscription->end_date->format('F j, Y') }}!
                </div>
              @endif

              <!-- Plan Features -->
              <div class="mb-4">
                <h6 class="mb-3">Your Plan Includes:</h6>
                <ul class="list-unstyled">
                  @if($currentSubscription->plan && $currentSubscription->plan->template_limit)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      <strong>{{ $currentSubscription->plan->template_limit }}</strong> resume limit
                    </li>
                  @elseif($currentSubscription->plan)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      <strong>Unlimited</strong> resume creation
                    </li>
                  @endif

                  @if($currentSubscription->plan && $currentSubscription->plan->access_premium_templates)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      Access to premium templates
                    </li>
                  @endif

                  @if($currentSubscription->plan && $currentSubscription->plan->priority_support)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      Priority 24/7 support
                    </li>
                  @endif

                  @if($currentSubscription->plan && $currentSubscription->plan->custom_branding)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      Custom branding
                    </li>
                  @endif

                  @if($currentSubscription->plan && $currentSubscription->plan->features && is_array($currentSubscription->plan->features))
                    @foreach($currentSubscription->plan->features as $feature)
                      <li class="mb-2">
                        <i class="bx bx-check text-success me-2"></i>
                        {{ $feature }}
                      </li>
                    @endforeach
                  @endif
                </ul>
              </div>

              <!-- ✅ UPDATED ACTIONS WITH TRIAL SUPPORT -->
              <div class="border-top pt-3">
                @if($currentSubscription->status === 'active')
                  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bx bx-x-circle me-1"></i>
                    {{ $currentSubscription->isInTrial() ? 'Cancel Trial' : 'Cancel Subscription' }}
                  </button>
                @elseif($currentSubscription->status === 'canceled')
                  <form action="{{ route('user.subscription.resume') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                      <i class="bx bx-check-circle me-1"></i> Resume Subscription
                    </button>
                  </form>
                @endif
              </div>

            @elseif($currentSubscription && !$currentSubscription->plan)
              <!-- Plan Deleted -->
              <div class="alert alert-warning">
                <i class="bx bx-exclamation-circle me-2"></i>
                The plan associated with your subscription was not found. Please contact support for assistance.
              </div>

            @else
              <!-- No Active Subscription -->
              <div class="text-center py-3">
                <i class="bx bx-package text-muted mb-2" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mb-2">No Active Subscription</h5>
                <p class="text-muted mb-3">You're currently on the free plan.</p>

                <!-- What You're Missing List -->
                <div class="missing-features-list text-start">
                  <h6 class="mb-3 fw-bold text-dark">What You're Missing:</h6>
                  <ul class="list-unstyled mb-0">
                    <li><i class="bx bx-lock me-2"></i> AI Resume Score & Suggestions</li>
                    <li><i class="bx bx-lock me-2"></i> Unlimited Resume & Cover Letter Generation</li>
                    <li><i class="bx bx-lock me-2"></i> AI Mock Interviews & Feedback</li>
                    <li><i class="bx bx-lock me-2"></i> Expert 1-on-1 Sessions</li>
                    <li><i class="bx bx-lock me-2"></i> Premium Resume Templates</li>
                  </ul>
                </div>

                <div class="mt-4 d-flex gap-2 justify-content-center">
                  <a href="{{ route('user.pricing') }}" class="btn primary-cta-btn">
                    <i class="bx bx-rocket me-1"></i> Upgrade to Pro
                  </a>
                  <a href="{{ route('user.pricing') }}" class="btn btn-outline-primary secondary-cta-btn">
                    Compare Plans
                  </a>
                </div>
              </div>

              <!-- Soft Urgency -->
              <div class="urgency-note">
                <small class="d-flex align-items-center text-muted">
                  <i class="bx bx-time-five me-2 text-warning"></i>
                  Most users upgrade after creating their first resume to improve interview success.
                </small>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Recent Payments -->
      <div class="col-lg-4 mb-3">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Recent Payments</h5>
          </div>
          <div class="card-body">
            @forelse($recentPayments as $payment)
              <div class="d-flex justify-content-between align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                  <small class="text-muted d-block">{{ $payment->created_at->format('M d, Y') }}</small>
                  <strong>${{ number_format($payment->amount, 2) }}</strong>
                  <br>
                  <span class="badge bg-{{ $payment->getStatusColor() }} badge-sm mt-1">
                    {{ ucfirst($payment->status) }}
                  </span>
                </div>
                <span class="badge bg-{{ $payment->getGatewayColor() }}">
                  {{ strtoupper($payment->payment_gateway) }}
                </span>
              </div>
            @empty
              <div class="empty-payments-state">
                <i class="bx bx-lock text-muted mb-2" style="font-size: 3rem;"></i>
                <p class="text-muted mb-0">No payments yet. Upgrade to Pro to start using premium features instantly.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

      <!-- Subscription History -->
    @if($subscriptionHistory->count() > 0)
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Subscription History</h5>
        </div>
        <div class="table-responsive subscription-history-wrapper">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Plan</th>
                <th class="period-column">Period</th>
                <th>Amount</th>
                <th>Status</th>
                <th class="duration-column">Duration</th>
              </tr>
            </thead>
            <tbody>
              @foreach($subscriptionHistory as $subscription)
                <tr>
                  <td><strong>{{ $subscription->plan ? $subscription->plan->name : 'Unknown Plan' }}</strong></td>
                  <td class="period-column">
                    <span class="badge bg-label-secondary">
                      {{ ucfirst($subscription->billing_period) }}
                    </span>
                  </td>
                  <td><strong>${{ number_format($subscription->amount, 2) }}</strong></td>
                  <td>
                    <span class="badge bg-{{ $subscription->getStatusColor() }}">
                      {{ ucfirst($subscription->status) }}
                    </span>
                  </td>
                  <td class="duration-column">
                    {{ $subscription->start_date->format('M d, Y') }} -
                    {{ $subscription->end_date->format('M d, Y') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($subscriptionHistory->hasPages())
          <div class="card-footer">
            {{ $subscriptionHistory->links() }}
          </div>
        @endif
      </div>
    @endif  </div>

  <!-- ✅ UPDATED CANCEL SUBSCRIPTION MODAL WITH TRIAL SUPPORT -->
  @if($currentSubscription && $currentSubscription->status === 'active')
    <div class="modal fade" id="cancelModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('user.subscription.cancel') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">
                {{ $currentSubscription->isInTrial() ? 'Cancel Trial' : 'Cancel Subscription' }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              @if($currentSubscription->isInTrial())
                <!-- Trial Cancellation Message -->
                <div class="alert alert-info">
                  <i class="bx bx-info-circle me-2"></i>
                  <strong>Trial Period:</strong> You have {{ $currentSubscription->trialDaysRemaining() }} days remaining
                </div>
                <p>
                  Are you sure you want to cancel your trial?
                  You'll continue to have access until
                  <strong>{{ $currentSubscription->trial_end_date->format('F j, Y') }}</strong>
                  and <strong>you won't be charged</strong>.
                </p>
              @else
                <!-- Paid Subscription Cancellation Message -->
                <p>
                  Are you sure you want to cancel your subscription?
                  You'll continue to have access until
                  <strong>{{ $currentSubscription->end_date->format('F j, Y') }}</strong>.
                </p>
              @endif

              <div class="mb-3">
                <label class="form-label">Reason for canceling (optional)</label>
                <textarea class="form-control" name="reason" rows="3" placeholder="Help us improve..."></textarea>
              </div>

              <!-- Optional: Add immediate cancellation option -->
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="immediately" value="1" id="cancelImmediately">
                <label class="form-check-label text-danger" for="cancelImmediately">
                  Cancel immediately (lose access right away)
                </label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                {{ $currentSubscription->isInTrial() ? 'Keep Trial' : 'Keep Subscription' }}
              </button>
              <button type="submit" class="btn btn-warning">
                {{ $currentSubscription->isInTrial() ? 'Cancel Trial' : 'Cancel Subscription' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <style>
    /* Subscription history table - vertical scroll only on mobile */
    @media (max-width: 768px) {
      /* General mobile spacing improvements */
      .card {
        margin-bottom: 1.5rem;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
      }

      .card-header .btn {
        width: 100%;
        justify-content: center;
      }

      /* Responsive grid for subscription details */
      .row > [class^="col-"] {
        flex: 0 0 100%;
        max-width: 100%;
      }

      .row.mb-4 > .col-md-6 {
        margin-bottom: 1rem;
      }

      /* Make badge card responsive */
      .bg-primary.text-white.mb-4 .card-body {
        flex-direction: column;
        align-items: flex-start !important;
      }

      .bg-primary.text-white.mb-4 .card-footer {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
      }

      .text-end {
        text-align: left !important;
      }

      /* Subscription history table - NO horizontal scroll */
      .subscription-history-wrapper {
        overflow-x: visible !important;
        overflow-y: auto !important;
        max-height: 400px;
        -webkit-overflow-scrolling: touch;
      }

      .subscription-history-wrapper table {
        width: 100%;
      }

      .subscription-history-wrapper table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
      }

      /* Hide Period and Duration columns on mobile */
      .period-column {
        display: none !important;
      }

      .duration-column {
        display: none !important;
      }
    }

    @media (max-width: 576px) {
      .subscription-history-wrapper {
        max-height: 350px;
      }

      h3, h4, h5 {
        font-size: 1.1rem;
      }

      .display-6 {
        font-size: 2rem !important;
      }
    }
  </style>

</x-layouts.app>
