<x-layouts.app :title="$title ?? 'Payments'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header with Statistics -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="fw-bold mb-0">💰 Payments</h4>
          <a href="{{ route('admin.payments.export', request()->all()) }}" class="btn btn-sm btn-success">
            <i class="bx bx-download me-1"></i> Export CSV
          </a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="col-lg-2 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body p-3">
            <small class="text-muted d-block mb-1">Total Revenue</small>
            <h4 class="mb-0">${{ number_format($stats['total_revenue'], 2) }}</h4>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body p-3">
            <small class="text-muted d-block mb-1">Today</small>
            <h4 class="mb-0 text-success">${{ number_format($stats['revenue_today'], 2) }}</h4>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body p-3">
            <small class="text-muted d-block mb-1">This Month</small>
            <h4 class="mb-0 text-info">${{ number_format($stats['revenue_month'], 2) }}</h4>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body p-3">
            <small class="text-muted d-block mb-1">Stripe</small>
            <h4 class="mb-0 text-primary">${{ number_format($stats['stripe_revenue'], 2) }}</h4>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body p-3">
            <small class="text-muted d-block mb-1">PayPal</small>
            <h4 class="mb-0 text-info">${{ number_format($stats['paypal_revenue'], 2) }}</h4>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-sm-6 mb-3">
        <div class="card">
          <div class="card-body p-3">
            <small class="text-muted d-block mb-1">Total Payments</small>
            <h4 class="mb-0">{{ $stats['total_payments'] }}</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.payments.index') }}">
          <div class="row">
            <div class="col-md-3 mb-3 mb-md-0">
              <input type="text" 
                     class="form-control" 
                     name="search" 
                     placeholder="Transaction ID or user..." 
                     value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2 mb-3 mb-md-0">
              <select class="form-select" name="status">
                <option value="">All Statuses</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('failed') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
              </select>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
              <select class="form-select" name="gateway">
                <option value="">All Gateways</option>
                <option value="stripe" {{ request('gateway') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                <option value="paypal" {{ request('gateway') == 'paypal' ? 'selected' : '' }}>PayPal</option>
              </select>
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
              <input type="date" 
                     class="form-control" 
                     name="date_from" 
                     value="{{ request('date_from') }}"
                     placeholder="From">
            </div>

            <div class="col-md-2 mb-3 mb-md-0">
              <input type="date" 
                     class="form-control" 
                     name="date_to" 
                     value="{{ request('date_to') }}"
                     placeholder="To">
            </div>

            <div class="col-md-1">
              <button type="submit" class="btn btn-primary w-100">
                <i class="bx bx-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Transaction ID</th>
              <th>User</th>
              <th>Amount</th>
              <th>Gateway</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $payment)
              <tr>
                <td>
                  <code>{{ substr($payment->transaction_id, 0, 20) }}...</code>
                </td>
                <td>
                  <strong>{{ $payment->user->name }}</strong><br>
                  <small class="text-muted">{{ $payment->user->email }}</small>
                </td>
                <td>
                  <strong class="text-success">${{ number_format($payment->amount, 2) }}</strong>
                  <small class="text-muted d-block">{{ $payment->currency }}</small>
                </td>
                <td>
                  <span class="badge bg-{{ $payment->getGatewayColor() }}">
                    {{ strtoupper($payment->payment_gateway) }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-{{ $payment->getStatusColor() }}">
                    {{ ucfirst($payment->status) }}
                  </span>
                </td>
                <td>
                  {{ $payment->created_at->format('M d, Y') }}<br>
                  <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                </td>
                <td>
                  <a href="{{ route('admin.payments.show', $payment) }}" 
                     class="btn btn-sm btn-icon btn-outline-primary">
                    <i class="bx bx-show"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="bx bx-info-circle me-2"></i> No payments found
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($payments->hasPages())
        <div class="card-footer">
          {{ $payments->links() }}
        </div>
      @endif
    </div>

  </div>
</x-layouts.app>