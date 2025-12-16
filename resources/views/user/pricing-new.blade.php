@extends('frontend.layouts.app')

@section('title', 'Pricing Plans - Jobsease')

@section('content')
<style>
    :root {
        --primary: #007BFF;
        --success: #10B981;
        --warning: #F59E0B;
        --dark: #1E293B;
        --light: #F8FAFC;
    }

    .pricing-hero {
        background: linear-gradient(135deg, #007BFF 0%, #0056b3 100%);
        color: white;
        padding: 4rem 2rem 3rem;
        text-align: center;
    }

    .pricing-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .pricing-hero p {
        font-size: 1.25rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
    }

    .pricing-section {
        padding: 4rem 2rem;
        max-width: 1280px;
        margin: 0 auto;
    }

    .billing-toggle {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .toggle-btn {
        padding: 0.75rem 2rem;
        border-radius: 50px;
        border: 2px solid #E5E7EB;
        background: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .toggle-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .savings-badge {
        background: #10B981;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .pricing-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        margin-bottom: 4rem;
    }

    .pricing-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        border: 2px solid transparent;
    }

    .pricing-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .pricing-card.recommended {
        border-color: var(--primary);
        transform: scale(1.05);
    }

    .recommended-badge {
        position: absolute;
        top: -15px;
        right: 30px;
        background: var(--primary);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .plan-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .plan-description {
        color: #64748B;
        margin-bottom: 1.5rem;
        min-height: 48px;
    }

    .plan-price {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .price-currency {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
    }

    .price-amount {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary);
    }

    .price-period {
        font-size: 1rem;
        color: #64748B;
    }

    .price-note {
        font-size: 0.875rem;
        color: #10B981;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    .plan-cta {
        width: 100%;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 2rem;
        transition: all 0.3s;
    }

    .plan-cta-primary {
        background: var(--primary);
        color: white;
    }

    .plan-cta-primary:hover {
        background: #0056b3;
        transform: scale(1.05);
    }

    .plan-cta-outline {
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .plan-cta-outline:hover {
        background: var(--light);
    }

    .plan-features {
        list-style: none;
        padding: 0;
    }

    .plan-features li {
        padding: 0.75rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid #F1F5F9;
    }

    .plan-features li:last-child {
        border-bottom: none;
    }

    .feature-icon {
        color: #10B981;
        font-weight: bold;
        font-size: 1.25rem;
    }

    /* Add-ons Section */
    .addons-section {
        background: var(--light);
        padding: 4rem 2rem;
        margin-top: 3rem;
    }

    .addons-container {
        max-width: 1280px;
        margin: 0 auto;
    }

    .addons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .addon-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .addon-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .addon-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
    }

    .addon-price {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--primary);
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    .section-subtitle {
        text-align: center;
        font-size: 1.125rem;
        color: #64748B;
        max-width: 600px;
        margin: 0 auto 3rem;
    }

    @media (max-width: 768px) {
        .pricing-hero h1 {
            font-size: 2rem;
        }

        .pricing-cards {
            grid-template-columns: 1fr;
        }

        .pricing-card.recommended {
            transform: scale(1);
        }

        .addons-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="pricing-hero">
    <h1>Simple, Transparent Pricing</h1>
    <p>Choose the perfect plan to accelerate your career journey</p>
    @auth
        <p style="margin-top: 1rem; font-size: 0.95rem; opacity: 0.9;">
            @if($currentSubscription)
                You're currently on the <strong>{{ $currentSubscription->plan ? $currentSubscription->plan->name : 'Unknown' }} Plan</strong>
            @else
                You're currently on the <strong>Free Plan</strong>
            @endif
        </p>
    @else
        <p style="margin-top: 1rem; font-size: 0.95rem; opacity: 0.9;">
            <a href="{{ route('register') }}" class="btn btn-light btn-sm">Create an account</a> to get started
        </p>
    @endauth
</div>

<div class="pricing-section">
    <!-- Billing Toggle -->
    <div class="billing-toggle">
        <button class="toggle-btn active" id="monthlyBtn" onclick="toggleBilling('monthly')">Monthly</button>
        <button class="toggle-btn" id="yearlyBtn" onclick="toggleBilling('yearly')">
            Yearly <span class="savings-badge">Save up to 33%</span>
        </button>
    </div>

    @if(empty($plans))
        <!-- No Plans Available -->
        <div class="alert alert-warning" role="alert">
            <i class="bx bx-exclamation-circle me-2"></i>
            <strong>Pricing plans are being set up.</strong> Please check back soon or contact our team for details.
        </div>
    @else
        <!-- Pricing Cards -->
        <div class="pricing-cards">
        @forelse($plans as $plan)
            <div class="pricing-card @if($plan->slug === 'pro') recommended @endif">
                @if($plan->slug === 'pro')
                    <div class="recommended-badge">⭐ RECOMMENDED</div>
                @endif
                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-description">{{ $plan->description }}</div>

                <div class="plan-price">
                    <span class="price-currency">IDR</span>
                    <span class="price-amount" data-plan="{{ $plan->slug }}-monthly">{{ number_format($plan->monthly_price, 0) }}</span>
                    <span class="price-period" data-plan="{{ $plan->slug }}-period">/month</span>
                </div>
                <div class="price-note" data-plan="{{ $plan->slug }}-note">
                    @if($plan->monthly_price == 0)
                        Forever free
                    @else
                        IDR {{ number_format($plan->yearly_price, 0) }} billed yearly
                    @endif
                </div>

                @if($plan->monthly_price == 0)
                    <button class="plan-cta plan-cta-outline" onclick="location.href='{{ route('register') }}'">
                        Get Started Free
                    </button>
                @else
                    <button class="plan-cta @if($plan->slug === 'pro') plan-cta-primary @else plan-cta-outline @endif" onclick="subscribePlan('{{ $plan->slug }}')">
                        @auth
                            @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                                ✓ Current Plan
                            @else
                                Upgrade to {{ $plan->name }}
                            @endif
                        @else
                            Choose {{ $plan->name }}
                        @endauth
                    </button>
                @endif

                <ul class="plan-features">
                    @if($plan->features && is_array($plan->features))
                        @foreach($plan->features as $feature)
                            <li><span class="feature-icon">✓</span> {{ $feature }}</li>
                        @endforeach
                    @else
                        <li><span class="feature-icon">✓</span> Access to platform features</li>
                    @endif
                </ul>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">No plans available</p>
            </div>
        @endforelse
        </div>
</div>
    @endif
</div>
<div class="addons-section">
    <div class="addons-container">
        <h2 class="section-title">Interview with Expert</h2>
        <p class="section-subtitle">
            One-time professional interview coaching sessions with experienced industry experts
        </p>

        <div class="addons-grid">
            <!-- 30-Min Session -->
            <div class="addon-card">
                <div class="addon-header">
                    <div class="addon-title">30-Minute Session</div>
                    <div class="addon-price">IDR 200K</div>
                </div>
                <p style="color: #64748B; margin-bottom: 1.5rem;">
                    Quick focused interview practice with personalized feedback
                </p>
                <ul class="plan-features">
                    <li><span class="feature-icon">✓</span> 30 minutes with expert</li>
                    <li><span class="feature-icon">✓</span> Personalized feedback</li>
                    <li><span class="feature-icon">✓</span> Recording available</li>
                    <li><span class="feature-icon">✓</span> Written report</li>
                </ul>
                <button class="plan-cta plan-cta-outline" onclick="bookInterview('30-min')" style="margin-top: 1.5rem;">
                    Book 30-Min Session
                </button>
            </div>

            <!-- 60-Min Session -->
            <div class="addon-card">
                <div class="addon-header">
                    <div class="addon-title">60-Minute Session</div>
                    <div class="addon-price">IDR 400K</div>
                </div>
                <p style="color: #64748B; margin-bottom: 1.5rem;">
                    In-depth interview coaching with comprehensive feedback
                </p>
                <ul class="plan-features">
                    <li><span class="feature-icon">✓</span> 60 minutes with expert</li>
                    <li><span class="feature-icon">✓</span> In-depth personalized feedback</li>
                    <li><span class="feature-icon">✓</span> Recording available</li>
                    <li><span class="feature-icon">✓</span> Detailed written report</li>
                    <li><span class="feature-icon">✓</span> Follow-up email support</li>
                </ul>
                <button class="plan-cta plan-cta-primary" onclick="bookInterview('60-min')" style="margin-top: 1.5rem;">
                    Book 60-Min Session
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentBilling = 'monthly';

    // Plan ID mapping (will be populated from backend)
    const planIds = {
        'pro': null,
        'pro-plus': null
    };

    function toggleBilling(period) {
        currentBilling = period;

        // Update button states
        document.getElementById('monthlyBtn').classList.toggle('active', period === 'monthly');
        document.getElementById('yearlyBtn').classList.toggle('active', period === 'yearly');

        // Update all plan prices dynamically
        @if(isset($plans))
            @foreach($plans as $plan)
                const {{ str_replace('-', '_', $plan->slug) }}Monthly = {{ $plan->monthly_price }};
                const {{ str_replace('-', '_', $plan->slug) }}Yearly = {{ $plan->yearly_price }};
                
                const {{ str_replace('-', '_', $plan->slug) }}PriceEl = document.querySelector('[data-plan="{{ $plan->slug }}-monthly"]');
                const {{ str_replace('-', '_', $plan->slug) }}PeriodEl = document.querySelector('[data-plan="{{ $plan->slug }}-period"]');
                const {{ str_replace('-', '_', $plan->slug) }}NoteEl = document.querySelector('[data-plan="{{ $plan->slug }}-note"]');
                
                if ({{ str_replace('-', '_', $plan->slug) }}PriceEl) {
                    if (period === 'monthly') {
                        {{ str_replace('-', '_', $plan->slug) }}PriceEl.textContent = {{ $plan->monthly_price }} === 0 ? '0' : '{{ number_format($plan->monthly_price, 0) }}';
                        {{ str_replace('-', '_', $plan->slug) }}PeriodEl.textContent = '/month';
                        {{ str_replace('-', '_', $plan->slug) }}NoteEl.textContent = {{ $plan->monthly_price }} === 0 ? 'Forever free' : 'Billed monthly';
                    } else {
                        {{ str_replace('-', '_', $plan->slug) }}PriceEl.textContent = {{ $plan->yearly_price }} === 0 ? '0' : '{{ number_format($plan->yearly_price, 0) }}';
                        {{ str_replace('-', '_', $plan->slug) }}PeriodEl.textContent = '/year';
                        const monthlyTotal = {{ $plan->monthly_price }} * 12;
                        const savings = monthlyTotal - {{ $plan->yearly_price }};
                        {{ str_replace('-', '_', $plan->slug) }}NoteEl.textContent = savings > 0 ? 'Save IDR ' + new Intl.NumberFormat('id-ID').format(Math.round(savings)) + ' per year' : 'Billed yearly';
                    }
                }
            @endforeach
        @endif
    }

    function subscribePlan(planSlug) {
        @auth
            // User is logged in, process Stripe checkout
            const planId = planIds[planSlug];

            if (!planId) {
                alert('Plan not found. Please refresh the page and try again.');
                return;
            }

            // Create a form and submit to Stripe checkout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('user.payment.stripe.checkout') }}';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add plan_id
            const planInput = document.createElement('input');
            planInput.type = 'hidden';
            planInput.name = 'plan_id';
            planInput.value = planId;
            form.appendChild(planInput);

            // Add billing_period
            const periodInput = document.createElement('input');
            periodInput.type = 'hidden';
            periodInput.name = 'billing_period';
            periodInput.value = currentBilling;
            form.appendChild(periodInput);

            document.body.appendChild(form);
            form.submit();
        @else
            // User not logged in, redirect to register
            window.location.href = '{{ route('register') }}';
        @endauth
    }

    function bookInterview(duration) {
        @auth
            // User is logged in, redirect to interview booking
            alert('Interview booking feature coming soon! We will redirect you to the booking page.');
            // TODO: Implement interview booking flow
            // window.location.href = `/user/interview/book/${duration}`;
        @else
            // User not logged in, redirect to register
            window.location.href = '{{ route('register') }}';
        @endauth
    }

    // Load plan IDs from server-side data
    @if(isset($plans))
        @foreach($plans as $plan)
            @if($plan->slug === 'pro' || $plan->slug === 'pro-plus')
                planIds['{{ $plan->slug }}'] = {{ $plan->id }};
            @endif
        @endforeach
    @endif
</script>

@endsection
