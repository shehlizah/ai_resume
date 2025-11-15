<x-layouts.app :title="$title ?? 'User Subscriptions'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header with Statistics -->
    <div class="row mb-4">
      <div class="col-12">
        <h4 class="fw-bold mb-3">👥 User Subscriptions</h4>
      </div>

      <!-- Stats Cards -->
      <div class="col-lg-3 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <span class="fw-semibold d-block mb-1">Total</span>
                <h3 class="card-title mb-0">{{ $stats['total'] }}</h3>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="bx bx-user fs-4"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <span class="fw-semibold d-block mb-1 text-success">Active</span>
                <h3 class="card-title mb-0 text-success">{{ $stats['active'] }}</h3>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="bx bx-check-circle fs-4"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <span class="fw-semibold d-block mb-1 text-warning">Expired</span>
                <h3 class="card-title mb-0 text-warning">{{ $stats['expired'] }}</h3>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="bx bx-time-five fs-4"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <span class="fw-semibold d-block mb-1">MRR</span>
                <h3 class="card-title mb-0">${{ number_format($stats['revenue_monthly'], 2) }}</h3>
              </div>
              <div class="avatar">
                <span class="avatar-initial rounded bg-label-info">
                  <i class="bx bx-dollar-circle fs-4"></i>
                </span>
              </div>
            </div>
            <small class="text-muted">Monthly Recurring</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.user-subscriptions.index') }}">
          <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
              <input type="text" 
                     class="form-control" 
                     name="search" 
                     placeholder="Search by user name or email..." 
                     value="{{ request('search') }}">
            </div>
            
            <div class="col-md-3 mb-3 mb-md-0">
              <select class="form-select" name="status">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              </select>
            </div>

            <div class="col-md-3 mb-3 mb-md-0">
              <select class="form-select" name="plan_id">
                <option value="">All Plans</option>
                @foreach($plans as $planOption)
                  <option value="{{ $planOption->id }}" {{ request('plan_id') == $planOption->id ? 'selected' : '' }}>
                    {{ $planOption->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">
                <i class="bx bx-search me-1"></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>User</th>
              <th>Plan</th>
              <th>Period</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($subscriptions as $subscription)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ strtoupper(substr($subscription->user->name, 0, 1)) }}
                      </span>
                    </div>
                    <div>
                      <strong>{{ $subscription->user->name }}</strong><br>
                      <small class="text-muted">{{ $subscription->user->email }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-label-info">{{ $subscription->plan->name }}</span>
                </td>
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
                  @if($subscription->isExpiringSoon() && $subscription->status == 'active')
                    <br><small class="text-warning">⚠️ Expiring soon</small>
                  @endif
                </td>
                <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                <td>
                  {{ $subscription->end_date->format('M d, Y') }}
                  @if($subscription->isActive())
                    <br><small class="text-muted">{{ $subscription->daysRemaining() }} days left</small>
                  @endif
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('admin.user-subscriptions.show', $subscription) }}">
                        <i class="bx bx-show me-1"></i> View Details
                      </a>
                      
                      @if($subscription->status == 'active')
                        <form action="{{ route('admin.user-subscriptions.cancel', $subscription) }}" method="POST">
                          @csrf
                          <button type="submit" class="dropdown-item text-warning" 
                                  onclick="return confirm('Cancel this subscription?')">
                            <i class="bx bx-x-circle me-1"></i> Cancel
                          </button>
                        </form>
                      @endif

                      @if($subscription->status == 'canceled')
                        <form action="{{ route('admin.user-subscriptions.reactivate', $subscription) }}" method="POST">
                          @csrf
                          <button type="submit" class="dropdown-item text-success">
                            <i class="bx bx-check-circle me-1"></i> Reactivate
                          </button>
                        </form>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4">
                  <i class="bx bx-info-circle me-2"></i> No subscriptions found
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($subscriptions->hasPages())
        <div class="card-footer">
          {{ $subscriptions->links() }}
        </div>
      @endif
    </div>

  </div>
</x-layouts.app>