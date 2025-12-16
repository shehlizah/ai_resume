<x-layouts.app :title="__('Company Payments')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Company Payments</h1>
            <p class="text-muted mb-0">Manage employer package and add-on purchases</p>
        </div>
    </div>

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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Payments</p>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-receipt bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Pending</p>
                            <h4 class="mb-0 text-warning">{{ $stats['pending'] }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-time bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Approved</p>
                            <h4 class="mb-0 text-success">{{ $stats['approved'] }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Revenue</p>
                            <h4 class="mb-0 text-primary">IDR {{ number_format($stats['total_revenue'], 0) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-wallet bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.company-payments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="manual" {{ request('payment_method') === 'manual' ? 'selected' : '' }}>Manual/Bank Transfer</option>
                        <option value="stripe" {{ request('payment_method') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Item Type</label>
                    <select name="item_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="package" {{ request('item_type') === 'package' ? 'selected' : '' }}>Package</option>
                        <option value="addon" {{ request('item_type') === 'addon' ? 'selected' : '' }}>Add-on</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.company-payments.index') }}" class="btn btn-label-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Payments</h5>
        </div>
        <div class="card-body p-0">
            @if($payments->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                    <p class="text-muted">No payments found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company/User</th>
                                <th>Item</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td><span class="fw-medium">#{{ $payment->id }}</span></td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $payment->user->name }}</div>
                                            <small class="text-muted">{{ $payment->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge bg-label-{{ $payment->item_type === 'package' ? 'primary' : 'info' }}">
                                                {{ ucfirst($payment->item_type) }}
                                            </span>
                                            <div class="mt-1">{{ $payment->item_name }}</div>
                                        </div>
                                    </td>
                                    <td><span class="fw-medium">IDR {{ number_format($payment->amount, 0) }}</span></td>
                                    <td>
                                        @if($payment->payment_method === 'stripe')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-credit-card"></i> Stripe
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-bank"></i> Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($payment->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $payment->created_at->format('M d, Y') }}</small>
                                        <div><small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small></div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.company-payments.show', $payment) }}" class="btn btn-sm btn-label-primary">
                                            <i class="bx bx-show"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
