<x-layouts.app :title="$title ?? 'My Subscription'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <h4 class="fw-bold mb-4">📊 My Subscription</h4>

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

    <div class="row">
      <!-- Current Subscription Card -->
      <div class="col-lg-8 mb-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Current Plan</h5>
            <a href="{{ route('user.pricing') }}" class="btn btn-sm btn-primary">
              <i class="bx bx-rocket me-1"></i> Upgrade Plan
            </a>
          </div>
          <div class="card-body">
            @if($currentSubscription)
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
                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block">Days Remaining</small>
       
@php
    $daysRemaining = \Carbon\Carbon::now()->diffInDays($currentSubscription->end_date);
    if (\Carbon\Carbon::now()->greaterThan($currentSubscription->end_date)) {
        $daysRemaining = 0;
    }

    // Convert to string and take first two characters
    $daysRemainingDisplay = substr((string)$daysRemaining, 0, 2);
@endphp

<strong>{{ $daysRemaining > 0 ? $daysRemainingDisplay . ' days' : 'Expired' }}</strong>


                </div>
                
                
                <!--<div class="col-md-6 mb-3">-->
                <!--  <small class="text-muted d-block">Next Billing</small>-->
                <!--  <strong>{{ $currentSubscription->next_billing_date ? $currentSubscription->next_billing_date->format('F j, Y') : 'N/A' }}</strong>-->
                <!--</div>-->

                @if($currentSubscription->amount > 0 && $currentSubscription->next_billing_date)
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

              @if($currentSubscription->isExpiringSoon())
                <div class="alert alert-warning mb-4">
                  <i class="bx bx-info-circle me-2"></i>
                  Your subscription is expiring soon on {{ $currentSubscription->end_date->format('F j, Y') }}!
                </div>
              @endif

              <!-- Plan Features -->
              <div class="mb-4">
                <h6 class="mb-3">Your Plan Includes:</h6>
                <ul class="list-unstyled">
                  @if($currentSubscription->plan->template_limit)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      <strong>{{ $currentSubscription->plan->template_limit }}</strong> resume limit
                    </li>
                  @else
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      <strong>Unlimited</strong> resume creation
                    </li>
                  @endif

                  @if($currentSubscription->plan->access_premium_templates)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      Access to premium templates
                    </li>
                  @endif

                  @if($currentSubscription->plan->priority_support)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      Priority 24/7 support
                    </li>
                  @endif

                  @if($currentSubscription->plan->custom_branding)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      Custom branding
                    </li>
                  @endif

                  @if($currentSubscription->plan->features && is_array($currentSubscription->plan->features))
                    @foreach($currentSubscription->plan->features as $feature)
                      <li class="mb-2">
                        <i class="bx bx-check text-success me-2"></i>
                        {{ $feature }}
                      </li>
                    @endforeach
                  @endif
                </ul>
              </div>

              <!-- Actions -->
              <div class="border-top pt-3">
                @if($currentSubscription->status === 'active')
                  <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bx bx-x-circle me-1"></i> Cancel Subscription
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

            @else
              <!-- No Active Subscription -->
              <div class="text-center py-5">
                <i class="bx bx-package display-1 text-muted mb-3"></i>
                <h4>No Active Subscription</h4>
                <p class="text-muted mb-4">You're currently on the free plan. Upgrade to unlock premium features!</p>
                <a href="{{ route('user.pricing') }}" class="btn btn-primary">
                  <i class="bx bx-rocket me-1"></i> View Plans
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Recent Payments -->
      <div class="col-lg-4 mb-4">
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
              <p class="text-muted text-center mb-0">No payments yet</p>
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
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Plan</th>
                <th>Period</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              @foreach($subscriptionHistory as $subscription)
                <tr>
                  <td><strong>{{ $subscription->plan->name }}</strong></td>
                  <td>
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
                  <td>
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
    @endif

  </div>

  <!-- Cancel Subscription Modal -->
  @if($currentSubscription && $currentSubscription->status === 'active')
    <div class="modal fade" id="cancelModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('user.subscription.cancel') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Cancel Subscription</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to cancel your subscription? You'll continue to have access until <strong>{{ $currentSubscription->end_date->format('F j, Y') }}</strong>.</p>
              
              <div class="mb-3">
                <label class="form-label">Reason for canceling (optional)</label>
                <textarea class="form-control" name="reason" rows="3" placeholder="Help us improve..."></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Subscription</button>
              <button type="submit" class="btn btn-warning">Cancel Subscription</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

</x-layouts.app>