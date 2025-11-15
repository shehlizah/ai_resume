<x-layouts.app :title="$title ?? 'Edit Subscription Plan'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="mb-4">
      <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-sm btn-secondary mb-2">
        <i class="bx bx-arrow-back"></i> Back to Plans
      </a>
      <h4 class="fw-bold">✏️ Edit Subscription Plan: {{ $plan->name }}</h4>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <form action="{{ route('admin.subscription-plans.update', $plan) }}" method="POST">
              @csrf
              @method('PUT')

              <!-- Plan Name -->
              <div class="mb-3">
                <label class="form-label" for="name">Plan Name *</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $plan->name) }}" 
                       required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Description -->
              <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3">{{ old('description', $plan->description) }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Pricing -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="monthly_price">Monthly Price ($) *</label>
                  <input type="number" 
                         class="form-control @error('monthly_price') is-invalid @enderror" 
                         id="monthly_price" 
                         name="monthly_price" 
                         value="{{ old('monthly_price', $plan->monthly_price) }}" 
                         step="0.01" 
                         min="0" 
                         required>
                  @error('monthly_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label" for="yearly_price">Yearly Price ($) *</label>
                  <input type="number" 
                         class="form-control @error('yearly_price') is-invalid @enderror" 
                         id="yearly_price" 
                         name="yearly_price" 
                         value="{{ old('yearly_price', $plan->yearly_price) }}" 
                         step="0.01" 
                         min="0" 
                         required>
                  @error('yearly_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <!-- Template Limit -->
              <div class="mb-3">
                <label class="form-label" for="template_limit">Resume Creation Limit</label>
                <input type="number" 
                       class="form-control @error('template_limit') is-invalid @enderror" 
                       id="template_limit" 
                       name="template_limit" 
                       value="{{ old('template_limit', $plan->template_limit) }}" 
                       min="0" 
                       placeholder="Leave empty for unlimited">
                @error('template_limit')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Number of resumes user can create. Leave blank for unlimited.</small>
              </div>

              <!-- Permissions -->
              <div class="mb-3">
                <label class="form-label">Permissions</label>
                
                <div class="form-check mb-2">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="access_premium_templates" 
                         name="access_premium_templates"
                         {{ old('access_premium_templates', $plan->access_premium_templates) ? 'checked' : '' }}>
                  <label class="form-check-label" for="access_premium_templates">
                    Access Premium Templates
                  </label>
                </div>

                <div class="form-check mb-2">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="priority_support" 
                         name="priority_support"
                         {{ old('priority_support', $plan->priority_support) ? 'checked' : '' }}>
                  <label class="form-check-label" for="priority_support">
                    Priority Support
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="custom_branding" 
                         name="custom_branding"
                         {{ old('custom_branding', $plan->custom_branding) ? 'checked' : '' }}>
                  <label class="form-check-label" for="custom_branding">
                    Custom Branding
                  </label>
                </div>
              </div>

              <!-- Features List -->
              <div class="mb-3">
                <label class="form-label" for="features_text">Features List</label>
                <textarea class="form-control @error('features_text') is-invalid @enderror" 
                          id="features_text" 
                          name="features_text" 
                          rows="6">{{ old('features_text', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                @error('features_text')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Enter one feature per line</small>
              </div>

              <!-- Sort Order -->
              <div class="mb-3">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" 
                       class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', $plan->sort_order) }}" 
                       min="0">
                @error('sort_order')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Active Status -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="is_active" 
                         name="is_active"
                         {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    Active (visible to users)
                  </label>
                </div>
              </div>

              <!-- Submit Buttons -->
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save me-1"></i> Update Plan
                </button>
                <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">
                  Cancel
                </a>
              </div>

            </form>
          </div>
        </div>
      </div>

      <!-- Stats Card -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">📊 Plan Statistics</h5>
            <ul class="list-unstyled mb-0">
              <li class="mb-2">
                <strong>Active Subscribers:</strong> {{ $plan->activeSubscriptionsCount() }}
              </li>
              <li class="mb-2">
                <strong>Total Subscribers:</strong> {{ $plan->subscriptions()->count() }}
              </li>
              <li class="mb-2">
                <strong>Created:</strong> {{ $plan->created_at->format('M d, Y') }}
              </li>
              <li class="mb-2">
                <strong>Slug:</strong> <code>{{ $plan->slug }}</code>
              </li>
            </ul>
          </div>
        </div>

        @if($plan->getSavingsPercentage() > 0)
          <div class="card mt-3 bg-success text-white">
            <div class="card-body">
              <h6 class="text-white">💰 Yearly Savings</h6>
              <p class="mb-0">
                <strong>{{ $plan->getSavingsPercentage() }}%</strong> off<br>
                <small>Save ${{ number_format($plan->getYearlySavings(), 2) }} per year</small>
              </p>
            </div>
          </div>
        @endif
      </div>
    </div>

  </div>
</x-layouts.app>