@section('title', __('Admin Dashboard'))
<x-layouts.app :title="__('Admin Dashboard')">
    <div class="row g-4">

        <!-- Welcome Section -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">
                                👋 Welcome, Admin!
                            </h4>
                            <p class="text-white mb-0 opacity-90">
                                Here's what's happening with your platform today.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
                                    <i class="bx bx-user-plus me-1"></i> Add User
                                </a>
                                <a href="{{ route('admin.templates.create') }}" class="btn btn-outline-light btn-sm">
                                    <i class="bx bx-plus me-1"></i> Add Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Primary Statistics - Row 1 -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded">
                                <i class="bx bxs-user-account text-primary" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Users</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($totalUsers) }}</h3>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success bg-opacity-10 text-success me-2">
                            <i class="bx bx-up-arrow-alt"></i> {{ $userGrowth }}%
                        </span>
                        <small class="text-muted">This month</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-success bg-opacity-10 rounded">
                                <i class="bx bxs-badge-check text-success" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Active Subscriptions</h6>
                            <h3 class="mb-0 text-success">{{ number_format($activeSubscriptions) }}</h3>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info bg-opacity-10 text-info me-2">
                            {{ $trialSubscriptions }} on trial
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded">
                                <i class="bx bxs-dollar-circle text-warning" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Revenue (Month)</h6>
                            <h3 class="mb-0 text-warning">${{ number_format($revenueThisMonth, 2) }}</h3>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success bg-opacity-10 text-success me-2">
                            <i class="bx bx-up-arrow-alt"></i> {{ $revenueGrowth }}%
                        </span>
                        <small class="text-muted">vs last month</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-info bg-opacity-10 rounded">
                                <i class="bx bxs-wallet text-info" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Revenue</h6>
                            <h3 class="mb-0 text-info">${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">All-time earnings</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Statistics - Row 2 -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-purple bg-opacity-10 rounded">
                                <i class="bx bxs-file-blank text-purple" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Templates</h6>
                            <h3 class="mb-0 text-purple">{{ number_format($totalTemplates) }}</h3>
                        </div>
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <span class="badge bg-warning badge-sm">{{ $premiumTemplates }} Premium</span>
                        <span class="badge bg-success badge-sm">{{ $activeTemplates }} Active</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-secondary bg-opacity-10 rounded">
                                <i class="bx bxs-download text-secondary" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Resumes Created</h6>
                            <h3 class="mb-0 text-secondary">{{ number_format($downloadsThisMonth) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">This month</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-danger bg-opacity-10 rounded">
                                <i class="bx bxs-time-five text-danger" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Pending Payments</h6>
                            <h3 class="mb-0 text-danger">{{ number_format($pendingPayments) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">${{ number_format($pendingPaymentsAmount, 2) }} total</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-teal bg-opacity-10 rounded">
                                <i class="bx bxs-pie-chart-alt-2 text-teal" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Conversion Rate</h6>
                            <h3 class="mb-0 text-teal">{{ $conversionRate }}%</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Free to paid</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job & Interview Stats -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded">
                                <i class="bx bxs-briefcase text-primary" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Job Searches</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($jobSearchesCount ?? 0) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">This month</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-success bg-opacity-10 rounded">
                                <i class="bx bxs-chat text-success" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Interview Sessions</h6>
                            <h3 class="mb-0 text-success">{{ number_format($interviewSessionsCount ?? 0) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Total AI practice</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded">
                                <i class="bx bxs-book-content text-warning" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Interview Questions</h6>
                            <h3 class="mb-0 text-warning">{{ number_format($interviewQuestionsCount ?? 0) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">In question bank</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-info bg-opacity-10 rounded">
                                <i class="bx bxs-map text-info" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Active Locations</h6>
                            <h3 class="mb-0 text-info">{{ number_format($activeJobLocations ?? 0) }}</h3>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Job search locations</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-zap me-1"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.users.create') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-user-plus text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Add User</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.templates.create') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-file-blank text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Add Template</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-group text-secondary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Users</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.templates.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-layer text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Templates</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.subscriptions.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-credit-card text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Subscriptions</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin.payments.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-dollar-circle text-danger mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Payments</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ url('admin/jobs') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-briefcase text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Job Sources</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ url('admin/interview/questions') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border hover-shadow transition">
                                    <i class="bx bx-chat text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Interview Bank</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Plans Overview -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-crown me-1"></i> Subscription Plans Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($subscriptionPlans as $plan)
                            <div class="col-lg-3 col-md-6">
                                <div class="p-4 rounded border h-100" style="background: rgba(99, 102, 241, 0.03);">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">{{ $plan->name }}</h6>
                                            <span class="badge bg-primary">{{ $plan->subscribers_count ?? 0 }} subscribers</span>
                                        </div>
                                        <i class="bx bx-crown text-warning" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="mb-2">
                                        <h4 class="mb-0 text-primary">${{ number_format($plan->monthly_price, 2) }}</h4>
                                        <small class="text-muted">per month</small>
                                    </div>
                                    <div class="border-top pt-2 mt-2">
                                        <small class="text-muted d-block">Total Revenue</small>
                                        <strong>${{ number_format($plan->total_revenue ?? 0, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">
                                <i class="bx bx-package mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p>No subscription plans yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data Tables -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-dollar-circle me-1"></i> Recent Payments
                    </h6>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">User</th>
                                <th class="px-4 py-3 border-0">Amount</th>
                                <th class="px-4 py-3 border-0">Status</th>
                                <th class="px-4 py-3 border-0">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div>
                                            <strong class="d-block">{{ Str::limit($payment->user->name, 20) }}</strong>
                                            <small class="text-muted">{{ Str::limit($payment->user->email, 25) }}</small>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong class="d-block">${{ number_format($payment->amount, 2) }}</strong>
                                        <small class="text-muted">
                                            <i class="bx bxl-{{ $payment->payment_gateway }}"></i>
                                            {{ ucfirst($payment->payment_gateway) }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $payment->created_at->format('M d, Y') }}<br>
                                            {{ $payment->created_at->format('h:i A') }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-5 text-center text-muted">
                                        <i class="bx bx-dollar-circle mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No payments yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-user-check me-1"></i> Recent Subscriptions
                    </h6>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">User</th>
                                <th class="px-4 py-3 border-0">Plan</th>
                                <th class="px-4 py-3 border-0">Status</th>
                                <th class="px-4 py-3 border-0">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSubscriptions as $subscription)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div>
                                            <strong class="d-block">{{ Str::limit($subscription->user->name, 20) }}</strong>
                                            <small class="text-muted">{{ Str::limit($subscription->user->email, 25) }}</small>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong class="d-block">{{ $subscription->plan->name ?? 'N/A' }}</strong>
                                        <small class="text-muted">
                                            ${{ number_format($subscription->amount, 2) }}/{{ $subscription->billing_period }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                        @if($subscription->isInTrial())
                                            <span class="badge bg-info d-block mt-1">
                                                <i class="bx bx-time-five"></i> Trial
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $subscription->start_date->format('M d, Y') }}
                                            @if($subscription->isInTrial())
                                                <br>
                                                <span class="text-info">{{ $subscription->trialDaysRemaining() }}d left</span>
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-5 text-center text-muted">
                                        <i class="bx bx-credit-card mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No subscriptions yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Users & Popular Templates -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-user me-1"></i> Recent Users
                    </h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">User</th>
                                <th class="px-4 py-3 border-0">Status</th>
                                <th class="px-4 py-3 border-0 text-end">Plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded me-2">
                                                <span class="text-primary fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <strong class="d-block">{{ Str::limit($user->name, 20) }}</strong>
                                                <small class="text-muted">{{ Str::limit($user->email, 25) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        @if($user->activeSubscription)
                                            <span class="badge bg-primary">
                                                {{ $user->activeSubscription->plan->name ?? 'Premium' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Free</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-5 text-center text-muted">
                                        <i class="bx bx-user mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No users yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-file me-1"></i> Popular Templates
                    </h6>
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">Template</th>
                                <th class="px-4 py-3 border-0 text-end">Usage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popularTemplates as $template)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-info bg-opacity-10 rounded me-2">
                                                <i class="bx bx-file text-info"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block">{{ Str::limit($template->name, 25) }}</strong>
                                                <div class="mt-1">
                                                    @if($template->is_premium)
                                                        <span class="badge bg-warning badge-sm">Premium</span>
                                                    @else
                                                        <span class="badge bg-info badge-sm">Free</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <strong class="text-primary">{{ number_format($template->downloads ?? 0) }}</strong>
                                        <small class="text-muted d-block">uses</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-5 text-center text-muted">
                                        <i class="bx bx-file mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No templates yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <style>
        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .avatar-md {
            width: 48px;
            height: 48px;
        }

        .avatar-lg {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }

        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }

        .transition {
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05) !important;
        }

        .badge-sm {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }

        .card {
            transition: all 0.3s ease;
        }

        .text-purple {
            color: #8b5cf6;
        }

        .bg-purple {
            background-color: #8b5cf6;
        }

        .text-teal {
            color: #14b8a6;
        }

        .bg-teal {
            background-color: #14b8a6;
        }

        .bg-label-primary {
            background-color: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .bg-label-success {
            background-color: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .bg-label-warning {
            background-color: rgba(251, 146, 60, 0.1);
            color: #fb923c;
        }

        .bg-label-info {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
    </style>
</x-layouts.app>
