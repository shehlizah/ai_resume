@section('title', __('Dashboard'))
<x-layouts.app :title="__('Dashboard')">
    <div class="row g-4">
        
        <!-- Welcome Banner with CTA -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">
                                👋 Welcome back, {{ $user->first_name ?? $user->name ?? 'User' }}!
                            </h4>
                            <p class="text-white mb-0 opacity-90">
                                Ready to create your next professional resume? Let's get started!
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('user.resumes') }}" class="btn btn-light btn-lg shadow-sm">
                                <i class="bx bx-plus-circle me-1"></i> Create Resume
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trial/Status Alerts -->
        @if($subscription && $subscription->isInTrial())
            <div class="col-lg-12">
                <div class="alert alert-info border-0 shadow-sm mb-0 d-flex align-items-center" role="alert">
                    <div class="flex-shrink-0 me-3">
                        <i class="bx bx-time-five" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Free Trial Active!</strong> You have <strong>{{ $subscription->trialDaysRemaining() }} days</strong> remaining. 
                        @if($subscription->status === 'canceled')
                            Your trial ends on <strong>{{ $subscription->trial_end_date->format('M j, Y') }}</strong>.
                        @else
                            Next billing: <strong>${{ number_format($subscription->amount, 2) }}</strong> on <strong>{{ $subscription->trial_end_date->format('M j, Y') }}</strong>.
                        @endif
                    </div>
                    <a href="{{ route('user.subscription.dashboard') }}" class="btn btn-sm btn-outline-info">Manage</a>
                </div>
            </div>
        @elseif(!$subscription || $subscription->status == 'expired')
            <div class="col-lg-12">
                <div class="alert alert-warning border-0 shadow-sm mb-0 d-flex align-items-center" role="alert">
                    <div class="flex-shrink-0 me-3">
                        <i class="bx bx-crown" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Unlock Premium Features!</strong> Get unlimited resumes and premium templates starting at $19.99/month.
                    </div>
                    <a href="{{ route('user.pricing') }}" class="btn btn-sm btn-warning">Upgrade Now</a>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded">
                                <i class="bx bxs-file-doc text-primary" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Resumes</h6>
                            <h3 class="mb-0 text-primary">{{ $stats['total_resumes'] }}</h3>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-trending-up me-1"></i>Created by you
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-success bg-opacity-10 rounded">
                                <i class="bx bxs-envelope text-success" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Cover Letters</h6>
                            <h3 class="mb-0 text-success">{{ $stats['total_cover_letters'] }}</h3>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-check-circle me-1"></i>Ready to send
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-info bg-opacity-10 rounded">
                                <i class="bx bxs-palette text-info" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Resume Templates</h6>
                            <h3 class="mb-0 text-info">{{ $availableTemplatesCount }}</h3>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-star me-1"></i>Professional designs
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded">
                                <i class="bx bxs-crown text-warning" style="font-size: 1.75rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Subscription</h6>
                            @if($subscription)
                                <div class="d-flex gap-1 flex-wrap">
                                    <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                    @if($subscription->isInTrial())
                                        <span class="badge bg-info">Trial</span>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-secondary">Free</span>
                            @endif
                        </div>
                    </div>
                    <small class="text-muted">
                        @if($subscription && $stats['subscription_plan'])
                            {{ $stats['subscription_plan'] }}
                        @else
                            Basic plan
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-8">
            
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-zap me-1"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-6">
                            <a href="{{ route('user.resumes') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border border-primary border-opacity-25 hover-shadow transition">
                                    <i class="bx bxs-file-plus text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">New Resume</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="{{ route('user.cover-letters.create') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border border-success border-opacity-25 hover-shadow transition">
                                    <i class="bx bxs-envelope text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Cover Letter</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="{{ route('user.resumes.choose') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border border-info border-opacity-25 hover-shadow transition">
                                    <i class="bx bxs-palette text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Templates</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="{{ route('user.resumes') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border border-warning border-opacity-25 hover-shadow transition">
                                    <i class="bx bxs-folder text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">My Documents</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="{{ route('user.add-ons.index') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border border-secondary border-opacity-25 hover-shadow transition">
                                    <i class="bx bxs-package text-secondary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Add-Ons</h6>
                                </div>
                            </a>
                        </div>
                        @if(!$hasPremiumAccess)
                        <div class="col-md-4 col-6">
                            <a href="{{ route('user.pricing') }}" class="text-decoration-none">
                                <div class="p-3 text-center rounded border border-danger border-opacity-25 hover-shadow transition">
                                    <i class="bx bxs-crown text-danger mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="small mb-0 text-dark">Upgrade</h6>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Resumes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-file me-1"></i> Recent Resumes
                    </h6>
                    <a href="{{ route('user.resumes') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">Resume</th>
                                <th class="px-4 py-3 border-0">Template</th>
                                <th class="px-4 py-3 border-0">Created</th>
                                <th class="px-4 py-3 border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentResumes as $resume)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded me-2">
                                                <i class="bx bxs-file-pdf text-primary"></i>
                                            </div>
                                            <strong>{{ Str::limit($resume->title, 30) }}</strong>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark">
                                            {{ $resume->template->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $resume->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('user.resumes', $resume->id) }}" class="btn btn-outline-primary" title="View">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('user.resumes.download', $resume->id) }}" class="btn btn-primary" title="Download">
                                                <i class="bx bx-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-5 text-center">
                                        <div class="text-muted">
                                            <i class="bx bxs-file-doc mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h6 class="mb-2">No resumes yet</h6>
                                            <p class="small mb-3">Create your first professional resume now!</p>
                                            <a href="{{ route('user.resumes') }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-plus-circle me-1"></i> Create Resume
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Subscription Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bxs-credit-card me-1"></i> Subscription
                    </h6>
                </div>
                <div class="card-body">
                    @if($subscription)
                        @if($subscription->isInTrial())
                            <div class="alert alert-info p-3 mb-3 border-0">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-time-five me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <strong class="d-block mb-1">Free Trial Active</strong>
                                        <small class="text-muted d-block mb-2">{{ $subscription->trialDaysRemaining() }} days remaining</small>
                                        <small class="d-block">Trial ends: <strong>{{ $subscription->trial_end_date->format('M j, Y') }}</strong></small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Plan</small>
                                <div class="d-flex align-items-center gap-2">
                                    <strong>{{ $stats['subscription_plan'] ?? 'Premium' }}</strong>
                                    @if($subscription->isInTrial())
                                        <span class="badge bg-info badge-sm">Trial</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Status</small>
                                <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </div>
                        </div>

                        @if($subscription->isInTrial() && $subscription->status === 'active')
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">After Trial</small>
                                    <strong>${{ number_format($subscription->amount, 2) }}/{{ $stats['billing_period'] ?? 'month' }}</strong>
                                </div>
                            </div>
                        @elseif($subscription->status == 'active' && $stats['next_billing_date'])
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Next Billing</small>
                                    <strong>{{ $stats['next_billing_date'] }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('user.subscription.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-cog me-1"></i> Manage Subscription
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-package mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mb-3 small">You're on the free plan</p>
                            <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100">
                                <i class="bx bx-crown me-1"></i> Upgrade Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Add-Ons -->
            @if(auth()->user()->userAddOns()->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bx bx-package me-1"></i> My Add-Ons
                        </h6>
                        <a href="{{ route('user.add-ons.my-add-ons') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach(auth()->user()->userAddOns()->with('addOn')->where('status', 'active')->latest()->take(3)->get() as $userAddOn)
                                <div class="list-group-item border-0 px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded">
                                                <i class="bx {{ $userAddOn->addOn->icon ?? 'bx-gift' }} text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong class="d-block mb-1">{{ $userAddOn->addOn->name }}</strong>
                                            <small class="text-muted">{{ $userAddOn->purchased_at->diffForHumans() }}</small>
                                        </div>
                                        <a href="{{ route('user.add-ons.access', $userAddOn->addOn) }}" class="btn btn-sm btn-primary">
                                            Access
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);">
                    <div class="card-body text-center p-4">
                        <i class="bx bxs-gift mb-3" style="font-size: 3rem; color: #8b5cf6;"></i>
                        <h6 class="mb-2">Boost Your Career</h6>
                        <p class="text-muted small mb-3">Access job boards, interview prep, and career resources</p>
                        <a href="{{ route('user.add-ons.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="bx bx-shopping-bag me-1"></i> Browse Add-Ons
                        </a>
                    </div>
                </div>
            @endif

            <!-- Premium Upgrade Card -->
            @if(!$hasPremiumAccess)
                <div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-center text-white p-4">
                        <i class="bx bxs-crown mb-3" style="font-size: 3rem;"></i>
                        <h6 class="mb-2 text-white">Unlock Premium</h6>
                        <p class="small mb-3 opacity-90">Get unlimited access to all features</p>
                        <ul class="text-start small mb-3 ps-3">
                            <li class="mb-2">✓ Unlimited Resumes</li>
                            <li class="mb-2">✓ All Premium Templates</li>
                            <li class="mb-2">✓ Cover Letter Builder</li>
                            <li class="mb-2">✓ Priority Support</li>
                        </ul>
                        <a href="{{ route('user.pricing') }}" class="btn btn-light btn-sm w-100">
                            Get Started - $19.99/month
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <style>
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
            font-size: 0.875rem;
        }

        .avatar-lg {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
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

        .list-group-item:last-child {
            border-bottom: 0 !important;
        }
    </style>
</x-layouts.app>