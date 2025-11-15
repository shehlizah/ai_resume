<x-layouts.app :title="$title ?? 'Subscription Plans'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">💳 Subscription Plans</h4>
      <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
        <i class="bx bx-plus me-1"></i> Add New Plan
      </a>
    </div>

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

    <!-- Plans Cards -->
    <div class="row">
      @forelse($plans as $plan)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
          <div class="card {{ !$plan->is_active ? 'border-secondary' : '' }}">
            <div class="card-body position-relative">
              
              <!-- Status Badge -->
              <div class="position-absolute top-0 end-0 p-3">
                <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                  {{ $plan->is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>

              <!-- Plan Name -->
              <h4 class="card-title mb-1">{{ $plan->name }}</h4>
              <p class="text-muted mb-4">{{ $plan->description }}</p>

              <!-- Pricing -->
              <div class="mb-4">
                <div class="d-flex align-items-baseline">
                  <h2 class="mb-0 text-primary">${{ number_format($plan->monthly_price, 2) }}</h2>
                  <span class="text-muted ms-2">/month</span>
                </div>
                @if($plan->yearly_price > 0)
                  <div class="d-flex align-items-baseline mt-2">
                    <h3 class="mb-0 text-success">${{ number_format($plan->yearly_price, 2) }}</h3>
                    <span class="text-muted ms-2">/year</span>
                    @if($plan->getSavingsPercentage() > 0)
                      <span class="badge bg-label-success ms-2">Save {{ $plan->getSavingsPercentage() }}%</span>
                    @endif
                  </div>
                @endif
              </div>

              <!-- Features -->
              <h6 class="mb-2">Features:</h6>
              <ul class="list-unstyled mb-4">
                @if($plan->template_limit)
                  <li class="mb-2">
                    <i class="bx bx-check text-success me-2"></i>
                    <strong>{{ $plan->template_limit }}</strong> resume{{ $plan->template_limit > 1 ? 's' : '' }} limit
                  </li>
                @else
                  <li class="mb-2">
                    <i class="bx bx-check text-success me-2"></i>
                    <strong>Unlimited</strong> resumes
                  </li>
                @endif
                
                <li class="mb-2">
                  <i class="bx bx-{{ $plan->access_premium_templates ? 'check text-success' : 'x text-danger' }} me-2"></i>
                  Premium templates
                </li>
                
                <li class="mb-2">
                  <i class="bx bx-{{ $plan->priority_support ? 'check text-success' : 'x text-danger' }} me-2"></i>
                  Priority support
                </li>
                
                <li class="mb-2">
                  <i class="bx bx-{{ $plan->custom_branding ? 'check text-success' : 'x text-danger' }} me-2"></i>
                  Custom branding
                </li>

                @if($plan->features && is_array($plan->features))
                  @foreach($plan->features as $feature)
                    <li class="mb-2">
                      <i class="bx bx-check text-success me-2"></i>
                      {{ $feature }}
                    </li>
                  @endforeach
                @endif
              </ul>

              <!-- Statistics -->
              <div class="border-top pt-3 mb-3">
                <small class="text-muted">
                  <i class="bx bx-user me-1"></i>
                  {{ $plan->activeSubscriptionsCount() }} active subscriber{{ $plan->activeSubscriptionsCount() != 1 ? 's' : '' }}
                </small>
              </div>

              <!-- Actions -->
              <div class="d-flex gap-2">
                <a href="{{ route('admin.subscription-plans.edit', $plan) }}" 
                   class="btn btn-sm btn-outline-primary flex-fill">
                  <i class="bx bx-edit"></i> Edit
                </a>
                
                <form action="{{ route('admin.subscription-plans.toggle-status', $plan) }}" 
                      method="POST" class="flex-fill">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-{{ $plan->is_active ? 'warning' : 'success' }} w-100">
                    <i class="bx bx-{{ $plan->is_active ? 'hide' : 'show' }}"></i>
                    {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                </form>

                @if($plan->activeSubscriptionsCount() == 0)
                  <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" 
                        method="POST" 
                        onsubmit="return confirm('Are you sure you want to delete this plan?')"
                        class="flex-fill">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                      <i class="bx bx-trash"></i> Delete
                    </button>
                  </form>
                @endif
              </div>

            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            No subscription plans found. Create your first plan to get started!
          </div>
        </div>
      @endforelse
    </div>

  </div>
</x-layouts.app>