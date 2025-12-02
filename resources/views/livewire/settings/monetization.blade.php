<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function getSubscription()
    {
        $user = Auth::user();
        return $user->subscriptions()
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();
    }
}; ?>

@section('title', 'Monetization')

<section>
    @include('partials.settings-heading')

    <x-settings.layout :subheading="__('Manage your subscription and unlock premium features')">

        @php
            $subscription = $this->getSubscription();
            $user = auth()->user();
            $hasPremiumAccess = $user->has_lifetime_access || ($subscription && $subscription->status === 'active');
        @endphp

        <!-- Current Plan -->
        <div class="mb-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">Current Plan</h6>
                </div>
                <div class="card-body">
                    @if($hasPremiumAccess)
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">Plan Name</p>
                                <h5 class="mb-3">{{ $subscription->subscriptionPlan->name ?? 'Pro Plan' }}</h5>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">Status</p>
                                <span class="badge bg-success mb-3">Active</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">Price</p>
                                <h6 class="mb-0">${{ number_format($subscription->amount, 2) }}/{{ $subscription->billing_period }}</h6>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">Renewal Date</p>
                                <h6 class="mb-0">{{ $subscription->next_billing_date ? \Carbon\Carbon::parse($subscription->next_billing_date)->format('M d, Y') : 'N/A' }}</h6>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('user.subscription.dashboard') }}" class="btn btn-outline-primary">
                            <i class="bx bx-cog me-2"></i> Manage Subscription
                        </a>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-package mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <h6 class="mb-2">You're on the Free Plan</h6>
                            <p class="text-muted small mb-4">
                                Get access to all premium features including unlimited AI, job searches, and interview coaching.
                            </p>
                            <a href="{{ route('user.pricing') }}" class="btn btn-primary">
                                <i class="bx bx-crown me-2"></i> Upgrade to Pro
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Features Unlocked -->
        <div class="mb-6">
            <h6 class="mb-3">Premium Features</h6>
            <div class="row g-3">
                <!-- Feature 1 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Unlimited AI Features</h6>
                                    <p class="text-muted small mb-0">Unlimited resume suggestions, cover letter generation, and AI assistance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Premium Templates</h6>
                                    <p class="text-muted small mb-0">Access to premium resume and cover letter templates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Unlimited Job Applications</h6>
                                    <p class="text-muted small mb-0">Apply to unlimited jobs and explore all opportunities</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">AI Interview Practice</h6>
                                    <p class="text-muted small mb-0">Practice with AI interviewer and get real-time feedback</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Expert Interview Sessions</h6>
                                    <p class="text-muted small mb-0">Book 1-on-1 coaching with industry experts</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Advanced Resume Scoring</h6>
                                    <p class="text-muted small mb-0">Detailed analytics and improvement recommendations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 7 -->
                <div class="col-md-6">
                    <div class="card border-0 {{ $hasPremiumAccess ? 'border-success' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($hasPremiumAccess)
                                        <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    @else
                                        <i class="bx bx-lock text-warning" style="font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Ad-Free Experience</h6>
                                    <p class="text-muted small mb-0">Enjoy the platform without any ads</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        @if(!$hasPremiumAccess)
        <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white text-center py-5">
                <h5 class="mb-3">Ready to Unlock Premium?</h5>
                <p class="mb-4">Get access to all features starting at just $19.99/month</p>
                <a href="{{ route('user.pricing') }}" class="btn btn-light btn-lg">
                    <i class="bx bx-crown me-2"></i> Upgrade to Pro
                </a>
            </div>
        </div>
        @endif

    </x-settings.layout>
</section>

<style>
    .card.border-success {
        border: 1px solid #198754 !important;
        background-color: rgba(25, 135, 84, 0.05);
    }

    .opacity-50 {
        opacity: 0.5;
    }
</style>
