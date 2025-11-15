<x-layouts.app :title="$title ?? 'Create Subscription Plan'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="mb-4">
      <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-sm btn-secondary mb-2">
        <i class="bx bx-arrow-back"></i> Back to Plans
      </a>
      <h4 class="fw-bold">✨ Create New Subscription Plan</h4>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <form action="{{ route('admin.subscription-plans.store') }}" method="POST">
              @csrf

              <!-- Plan Name -->
              <div class="mb-3">
                <label class="form-label" for="name">Plan Name *</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="e.g., Basic, Premium" 
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
                          rows="3" 
                          placeholder="Brief description of the plan">{{ old('description') }}</textarea>
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
                         value="{{ old('monthly_price', 0) }}" 
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
                         value="{{ old('yearly_price', 0) }}" 
                         step="0.01" 
                         min="0" 
                         required>
                  @error('yearly_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">Yearly pricing usually offers savings</small>
                </div>
              </div>

              <!-- Template Limit -->
              <div class="mb-3">
                <label class="form-label" for="template_limit">Resume Creation Limit</label>
                <input type="number" 
                       class="form-control @error('template_limit') is-invalid @enderror" 
                       id="template_limit" 
                       name="template_limit" 
                       value="{{ old('template_limit') }}" 
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
                         {{ old('access_premium_templates') ? 'checked' : '' }}>
                  <label class="form-check-label" for="access_premium_templates">
                    Access Premium Templates
                  </label>
                </div>

                <div class="form-check mb-2">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="priority_support" 
                         name="priority_support"
                         {{ old('priority_support') ? 'checked' : '' }}>
                  <label class="form-check-label" for="priority_support">
                    Priority Support
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="custom_branding" 
                         name="custom_branding"
                         {{ old('custom_branding') ? 'checked' : '' }}>
                  <label class="form-check-label" for="custom_branding">
                    Custom Branding (Remove Watermark)
                  </label>
                </div>
              </div>

              <!-- Features List -->
              <div class="mb-3">
                <label class="form-label" for="features_text">Features List</label>
                <textarea class="form-control @error('features_text') is-invalid @enderror" 
                          id="features_text" 
                          name="features_text" 
                          rows="6" 
                          placeholder="Enter one feature per line&#10;PDF Download&#10;Email Support&#10;Custom Cover Letters">{{ old('features_text') }}</textarea>
                @error('features_text')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Enter one feature per line. These will be displayed as bullet points.</small>
              </div>

              <!-- Sort Order -->
              <div class="mb-3">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" 
                       class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', 0) }}" 
                       min="0">
                @error('sort_order')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Lower numbers appear first</small>
              </div>

              <!-- Active Status -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         id="is_active" 
                         name="is_active"
                         {{ old('is_active', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    Active (visible to users)
                  </label>
                </div>
              </div>

              <!-- Submit Buttons -->
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save me-1"></i> Create Plan
                </button>
                <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">
                  Cancel
                </a>
              </div>

            </form>
          </div>
        </div>
      </div>

      <!-- Preview/Help Card -->
      <div class="col-lg-4">
        <div class="card bg-light">
          <div class="card-body">
            <h5 class="card-title">💡 Tips</h5>
            <ul class="mb-0">
              <li class="mb-2"><strong>Free Plans:</strong> Set both prices to $0.00</li>
              <li class="mb-2"><strong>Yearly Savings:</strong> Set yearly price lower than (monthly × 12)</li>
              <li class="mb-2"><strong>Slug:</strong> Automatically generated from plan name</li>
              <li class="mb-2"><strong>Features:</strong> Make them clear and benefit-focused</li>
              <li class="mb-2"><strong>Limits:</strong> Empty = unlimited access</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</x-layouts.app>