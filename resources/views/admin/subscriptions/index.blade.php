<x-layouts.app :title="'Manage Subscriptions'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-credit-card me-2"></i> Manage Subscriptions
            </h4>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.subscriptions.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="User name or email" 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="all">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plan</label>
                            <select name="plan" class="form-select">
                                <option value="all">All Plans</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </a>
                            </div>
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
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Period</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $subscription->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $subscription->user->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $subscription->plan->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <strong>${{ number_format($subscription->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $subscription->getStatusColor() }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                    @if($subscription->isInTrial())
                                        <br>
                                        <span class="badge bg-info mt-1">
                                            <i class="bx bx-time-five"></i> Trial ({{ $subscription->trialDaysRemaining() }}d)
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($subscription->billing_period) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $subscription->start_date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <small>{{ $subscription->end_date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.subscriptions.show', $subscription) }}">
                                                    <i class="bx bx-show me-1"></i> View Details
                                                </a>
                                            </li>
                                            @if($subscription->status === 'active')
                                                <li>
                                                    <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Cancel this subscription?')">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-x-circle me-1"></i> Cancel
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            @if($subscription->status === 'canceled')
                                                <li>
                                                    <form action="{{ route('admin.subscriptions.activate', $subscription) }}" 
                                                          method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="bx bx-check-circle me-1"></i> Activate
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0">No subscriptions found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($subscriptions->hasPages())
                <div class="card-footer">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>