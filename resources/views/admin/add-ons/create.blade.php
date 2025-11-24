<x-layouts.app :title="'Create Add-On'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-plus me-2"></i> Create New Add-On
            </h4>
            <a href="{{ route('admin.add-ons.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        {{-- Debug Info (Remove in production) --}}
        @if(config('app.debug'))
            <div class="alert alert-info alert-dismissible">
                <strong>Debug Info:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Form Action:</strong> {{ route('admin.add-ons.store') }}</li>
                    <li><strong>Expected URL:</strong> {{ url('/admin/add-ons') }}</li>
                    <li><strong>Method:</strong> POST</li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Error Display --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading mb-2"><i class="bx bx-error-circle me-1"></i> <strong>Validation Errors:</strong></h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i> <strong>Error:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                {{-- Make sure the form action explicitly uses the route --}}
                <form action="{{ route('admin.add-ons.store') }}" method="POST" id="addOnForm">
                    @csrf

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-lg-8">
                            <h5 class="mb-3">Basic Information</h5>

                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="e.g., Premium Job Board Access">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="slug" 
                                       id="slugInput"
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug') }}" 
                                       required
                                       placeholder="e.g., premium-job-board-access">
                                <small class="text-muted">URL-friendly version (must be unique)</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" 
                                          rows="4" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          required
                                          placeholder="Provide a detailed description of what this add-on includes...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="price" 
                                               step="0.01" 
                                               min="0"
                                               class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price', '20.00') }}" 
                                               required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" 
                                            class="form-select @error('type') is-invalid @enderror" 
                                            required>
                                        <option value="">-- Select Type --</option>
                                        <option value="job_links" {{ old('type') === 'job_links' ? 'selected' : '' }}>Job Links</option>
                                        <option value="interview_prep" {{ old('type') === 'interview_prep' ? 'selected' : '' }}>Interview Preparation</option>
                                        <option value="custom" {{ old('type') === 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Icon (BoxIcons class)</label>
                                <div class="input-group">
                                    <input type="text" 
                                           name="icon" 
                                           class="form-control @error('icon') is-invalid @enderror" 
                                           value="{{ old('icon', 'bx-gift') }}" 
                                           placeholder="e.g., bx-briefcase, bx-user-voice">
                                    <a href="https://boxicons.com/" target="_blank" class="btn btn-outline-secondary" rel="noopener">
                                        <i class="bx bx-link-external"></i> Browse Icons
                                    </a>
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Features (one per line)</label>
                                <textarea name="features_text" 
                                          rows="6" 
                                          class="form-control @error('features_text') is-invalid @enderror" 
                                          placeholder="Access to 100+ job boards&#10;Weekly job recommendations&#10;Interview prep resources&#10;Application tracking">{{ old('features_text') }}</textarea>
                                <small class="text-muted">Each line will become a feature bullet point</small>
                                @error('features_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- Settings Sidebar -->
                        <div class="col-lg-4">
                            <h5 class="mb-3">Settings</h5>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong>
                                            <br>
                                            <small class="text-muted">Make available for purchase</small>
                                        </label>
                                    </div>

                                    <hr>

                                    <div class="mb-0">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" 
                                               name="sort_order" 
                                               class="form-control @error('sort_order') is-invalid @enderror" 
                                               value="{{ old('sort_order', 0) }}"
                                               min="0">
                                        <small class="text-muted">Lower numbers appear first (0-999)</small>
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="bx bx-info-circle me-1"></i>
                                <small><strong>Note:</strong> You can add detailed content after creating the add-on.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-save me-1"></i> Create Add-On
                            </button>
                            <a href="{{ route('admin.add-ons.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Add-On Form Initialized');
            console.log('Form Action:', document.getElementById('addOnForm').action);
            
            // Auto-generate slug from name
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');
            
            if (nameInput && slugInput) {
                let manualEdit = false;
                
                nameInput.addEventListener('input', function(e) {
                    if (!manualEdit) {
                        const slug = e.target.value
                            .toLowerCase()
                            .trim()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                        slugInput.value = slug;
                    }
                });

                // Track manual edits
                slugInput.addEventListener('input', function() {
                    manualEdit = true;
                });
                
                // Reset manual edit if slug is cleared
                slugInput.addEventListener('blur', function() {
                    if (this.value === '') {
                        manualEdit = false;
                    }
                });
            }

            // Form submission handling
            const form = document.getElementById('addOnForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('=== FORM SUBMITTING ===');
                    console.log('Action:', form.action);
                    console.log('Method:', form.method);
                    
                    // Get form data
                    const formData = new FormData(form);
                    const formObject = {};
                    formData.forEach((value, key) => {
                        formObject[key] = value;
                    });
                    console.log('Form Data:', formObject);
                    
                    // Disable submit button
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';
                    }
                    
                    // Don't prevent default - let form submit normally
                });
            }

            // Log any validation errors
            const errorAlert = document.querySelector('.alert-danger');
            if (errorAlert) {
                console.error('Validation errors present on page load');
            }
        });
    </script>
    @endpush
</x-layouts.app>