@section('title', __('Dashboard'))
<x-layouts.app :title="__('Dashboard')">
    <div class="row g-4">
        
        <!-- Welcome Banner with CTA -->
        <div class="col-lg-12">
            <div class="overflow-hidden rounded border">
                <div class="p-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(99, 102, 241, 0.02) 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2">👋 Welcome back, {{ $user->first_name ?? $user->name ?? 'User' }}!</h4>
                            <p class="text-muted mb-0">Ready to create your next professional resume? Let's get started!</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('user.resumes') }}" class="btn btn-primary">
                                <i class="bx bx-plus-circle me-1"></i> Create Your Resume Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Alert -->
        @if($subscription && $subscription->status == 'pending')
            <div class="col-lg-12">
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="bx bx-time-five me-1"></i>
                    <strong>Payment Pending:</strong> Your subscription payment is being processed. 
                    <a href="{{ route('user.subscription') }}" class="alert-link">View Details</a>
                </div>
            </div>
        @elseif($subscription && $subscription->status == 'active' && $daysUntilBilling !== null && $daysUntilBilling <= 7)
            <div class="col-lg-12">
                <div class="alert alert-info mb-0" role="alert">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Upcoming Billing:</strong> Your next billing date is {{ $stats['next_billing_date'] }}. 
                    <a href="{{ route('user.subscription') }}" class="alert-link">Manage Subscription</a>
                </div>
            </div>
        @elseif(!$subscription || $subscription->status == 'expired')
            <div class="col-lg-12">
                <div class="alert alert-info mb-0" role="alert">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Start Your Subscription!</strong> Get access to all premium templates with our plans starting at $19.99/month.
                    <a href="{{ route('packages') }}" class="alert-link">View Plans</a>
                </div>
            </div>
        @endif
        
        <!-- Statistics Cards - First Row -->
        <div class="col-lg-3">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Total Resumes</h6>
                        <h3 class="mb-1 text-primary">{{ $stats['total_resumes'] }}</h3>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-file-doc"></i>
                        Created by you
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(34, 197, 94, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Cover Letters</h6>
                        <h3 class="mb-1 text-success">{{ $stats['total_cover_letters'] }}</h3>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-envelope"></i>
                        Ready to send
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Available Templates</h6>
                        <h3 class="mb-1 text-info">{{ $availableTemplatesCount }}</h3>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-palette"></i>
                        Professional designs
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(251, 146, 60, 0.05) 0%, rgba(251, 146, 60, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Subscription</h6>
                        @if($subscription)
                            <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'pending' ? 'warning' : 'secondary') }} px-3 py-2">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Free</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-crown"></i>
                        @if($subscription && $stats['subscription_plan'])
                            {{ $stats['subscription_plan'] }} - {{ ucfirst($stats['billing_period'] ?? 'monthly') }}
                        @else
                            Basic plan
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-12">
            <div class="overflow-hidden rounded border">
                <div class="p-4">
                    <h6 class="text-muted fw-normal small mb-3">Quick Actions</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('user.resumes') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus-circle me-1"></i> New Resume
                        </a>
                        <a href="#" class="btn btn-sm btn-success">
                            <i class="bx bx-envelope me-1"></i> New Cover Letter
                        </a>
                        <a href="{{ route('user.resumes.choose') }}" class="btn btn-sm btn-info">
                            <i class="bx bx-palette me-1"></i> Browse Templates
                        </a>
                        <a href="{{ route('user.resumes') }}" class="btn btn-sm btn-warning">
                            <i class="bx bx-folder me-1"></i> My Documents
                        </a>
                        @if(!$hasPremiumAccess)
                            <a href="{{ route('packages') }}" class="btn btn-sm btn-danger">
                                <i class="bx bx-crown me-1"></i> Upgrade to Premium
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Resumes & Subscription Info -->
        <div class="col-lg-8">
            <div class="overflow-hidden rounded border">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="px-4 py-3">Recent Resumes</th>
                                <th class="px-4 py-3">Template</th>
                                <th class="px-4 py-3">Created</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentResumes as $resume)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                <i class="bx bxs-file-pdf text-primary" style="font-size: 1.25rem;"></i>
                                            </div>
                                            <strong>{{ $resume->title }}</strong>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $resume->template->name ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small class="text-muted">
                                            {{ $resume->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('user.resumes', $resume->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('user.resumes.download', $resume->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-5 text-center">
                                        <div class="text-muted">
                                            <i class="bx bxs-file-doc" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h6 class="mt-3 mb-2">No resumes yet</h6>
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

        <div class="col-lg-4">
            <!-- Subscription Details -->
            <div class="overflow-hidden rounded border mb-4">
                <div class="p-4">
                    <h6 class="text-muted fw-normal small mb-3">
                        <i class="bx bxs-credit-card me-1"></i> Subscription Details
                    </h6>
                    
                    @if($subscription)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Plan</small>
                            <strong>{{ $stats['subscription_plan'] ?? 'Premium' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Billing Period</small>
                            <strong class="text-capitalize">{{ $stats['billing_period'] ?? 'monthly' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Status</small>
                            <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                        @if($subscription->status == 'active' && $stats['next_billing_date'])
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Next Billing</small>
                                <strong>{{ $stats['next_billing_date'] }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Amount</small>
                                <strong>${{ number_format($subscription->amount, 2) }}</strong>
                            </div>
                        @endif
                        @if($subscription->status == 'active')
                            <a href="{{ route('user.pricing') }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bx bx-cog me-1"></i> Manage Subscription
                            </a>
                        @elseif($subscription->status == 'expired' || $subscription->status == 'canceled')
                            <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100">
                                <i class="bx bx-refresh me-1"></i> Renew Subscription
                            </a>
                        @endif
                    @else
                        <p class="text-muted mb-3">You're currently on the free plan with limited access.</p>
                        <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100">
                            <i class="bx bx-crown me-1"></i> Upgrade Now
                        </a>
                    @endif
                </div>
            </div>

            <!-- Premium Upgrade Card (if not subscribed) -->
            @if(!$hasPremiumAccess)
                <div class="overflow-hidden rounded border" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);">
                    <div class="p-4 text-center">
                        <i class="bx bxs-crown" style="font-size: 3rem; color: #8b5cf6;"></i>
                        <h6 class="mt-3 mb-2">Unlock Premium</h6>
                        <p class="small text-muted mb-3">Get access to all professional templates</p>
                        <ul class="text-start small mb-3 ps-3">
                            <li class="mb-2">✓ Unlimited Resumes</li>
                            <li class="mb-2">✓ All Premium Templates</li>
                            <li class="mb-2">✓ Cover Letter Builder</li>
                            <li class="mb-2">✓ Priority Support</li>
                        </ul>
                        <a href="{{ route('packages') }}" class="btn btn-primary btn-sm w-100">
                            Get Started - $19.99/month
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05) !important;
        }
        
        .table thead {
            background-color: #f8f9fa;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        .btn-sm {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .alert {
            border-radius: 0.375rem;
            border: 1px solid transparent;
        }

        .alert-warning {
            background-color: rgba(251, 146, 60, 0.1);
            border-color: rgba(251, 146, 60, 0.2);
            color: #ea580c;
        }

        .alert-info {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.2);
            color: #2563eb;
        }
    </style>
</x-layouts.app>