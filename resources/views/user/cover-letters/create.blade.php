@section('title', __('Create Cover Letter'))
<x-layouts.app :title="__('Create Cover Letter')">
    <div class="row g-4">
        <div class="col-lg-12">
            <a href="{{ route('user.cover-letters.index') }}" class="btn btn-link ps-0 mb-3">
                <i class="bx bx-chevron-left me-1"></i> Back to Cover Letters
            </a>
        </div>

        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Create New Cover Letter</h5>
                </div>
                <div class="card-body">
                    <form id="coverLetterForm" action="{{ route('user.cover-letters.store') }}" method="POST">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Cover Letter Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title"
                                   placeholder="e.g., Application for Software Engineer at Google"
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Your Information -->
                        <h6 class="mb-3 text-primary">
                            <i class="bx bx-user me-1"></i> Your Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                       id="user_name" name="user_name"
                                       value="{{ old('user_name', $userData['name']) }}" required>
                                @error('user_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('user_email') is-invalid @enderror" 
                                       id="user_email" name="user_email"
                                       value="{{ old('user_email', $userData['email']) }}" required>
                                @error('user_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('user_phone') is-invalid @enderror" 
                                       id="user_phone" name="user_phone"
                                       placeholder="+1 (555) 123-4567"
                                       value="{{ old('user_phone', $userData['phone']) }}" required>
                                @error('user_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('user_address') is-invalid @enderror" 
                                       id="user_address" name="user_address"
                                       placeholder="123 Main St, City, State ZIP"
                                       value="{{ old('user_address', $userData['address']) }}" required>
                                @error('user_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Recipient Information -->
                        <h6 class="mb-3 text-primary">
                            <i class="bx bx-building me-1"></i> Recipient Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" 
                                       id="recipient_name" name="recipient_name"
                                       placeholder="e.g., John Smith"
                                       value="{{ old('recipient_name') }}" required>
                                @error('recipient_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name"
                                       placeholder="e.g., Google Inc."
                                       value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="company_address" class="form-label">Company Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('company_address') is-invalid @enderror" 
                                   id="company_address" name="company_address"
                                   placeholder="e.g., 1600 Amphitheatre Parkway, Mountain View, CA 94043"
                                   value="{{ old('company_address') }}" required>
                            @error('company_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Job Description (Optional - for AI Generation) -->
                        <h6 class="mb-3 text-primary">
                            <i class="bx bx-briefcase me-1"></i> Job Details (For AI Generation)
                        </h6>

                        <div class="mb-3">
                            <label for="job_description" class="form-label">Job Description <small class="text-muted">(Optional)</small></label>
                            <textarea class="form-control" id="job_description" name="job_description" 
                                      rows="4" placeholder="Paste the job description here to help AI generate a better cover letter...">{{ old('job_description') }}</textarea>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i> Adding job description helps AI tailor the cover letter to the position
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="additional_info" class="form-label">Your Skills & Experience <small class="text-muted">(Optional)</small></label>
                            <textarea class="form-control" id="additional_info" name="additional_info" 
                                      rows="3" placeholder="Briefly describe your relevant skills, experience, or achievements...">{{ old('additional_info') }}</textarea>
                        </div>

                        <hr class="my-4">

                        <!-- Cover Letter Content -->
                        <h6 class="mb-3 text-primary">
                            <i class="bx bx-edit me-1"></i> Cover Letter Content
                        </h6>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="content" class="form-label mb-0">Letter Body <span class="text-danger">*</span></label>
                                <button type="button" id="generateAIBtn" class="btn btn-sm btn-primary">
                                    <i class="bx bx-bot me-1"></i> Generate with AI
                                </button>
                            </div>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content"
                                      rows="15" placeholder="Your cover letter content will appear here..." required>{{ old('content') }}</textarea>
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle"></i>
                                Click "Generate with AI" button above or select a template from the sidebar
                            </small>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Loading Indicator -->
                        <div id="aiLoadingIndicator" class="alert alert-info" style="display: none;">
                            <i class="bx bx-loader-alt bx-spin me-2"></i> Generating your cover letter with AI... This may take a few seconds.
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Cover Letter
                            </button>
                            <a href="{{ route('user.cover-letters.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Template Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary bg-opacity-10 border-0">
                    <h6 class="mb-0 text-primary">
                        <i class="bx bx-file-blank me-1"></i> Quick Templates
                    </h6>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($templates as $template)
                        <div class="template-item mb-3 p-3 border rounded cursor-pointer" data-template-id="{{ $template->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $template->name }}</h6>
                                    <small class="text-muted">{{ $template->description }}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary use-template-btn" data-template-id="{{ $template->id }}">
                                    Use
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-4">
                            <i class="bx bx-file-blank" style="font-size: 2rem; opacity: 0.3;"></i>
                            <br>No templates available
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- AI Tips Card -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="text-primary mb-2">
                        <i class="bx bx-bulb me-1"></i> AI Generation Tips
                    </h6>
                    <ul class="small mb-0">
                        <li>Fill in all required fields before generating</li>
                        <li>Add job description for better results</li>
                        <li>Include your relevant skills & experience</li>
                        <li>You can edit the generated content</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden template contents -->
    @foreach($templates as $template)
        <div id="template-content-{{ $template->id }}" style="display: none;">{{ $template->content }}</div>
    @endforeach

    <style>
        .template-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .template-item:hover {
            background-color: rgba(99, 102, 241, 0.05);
            border-color: #6366f1 !important;
            transform: translateY(-2px);
        }

        .sticky-top {
            position: sticky;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .bx-spin {
            animation: spin 1s linear infinite;
        }
    </style>

    <script>
        // Template selection
        document.querySelectorAll('.use-template-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const templateId = this.dataset.templateId;
                const content = document.getElementById('template-content-' + templateId).textContent;
                document.getElementById('content').value = content;
                
                // Smooth scroll to content textarea
                document.getElementById('content').scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Show success message
                showToast('Template applied successfully!', 'success');
            });
        });

        // AI Generation
        document.getElementById('generateAIBtn').addEventListener('click', async function() {
            const form = document.getElementById('coverLetterForm');
            const formData = new FormData(form);
            const loadingIndicator = document.getElementById('aiLoadingIndicator');
            const generateBtn = this;
            
            // Validate required fields
            const requiredFields = ['user_name', 'user_email', 'user_phone', 'user_address', 'recipient_name', 'company_name', 'company_address'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                showToast('Please fill in all required fields before generating', 'error');
                return;
            }
            
            // Show loading
            loadingIndicator.style.display = 'block';
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Generating...';
            
            try {
                const response = await fetch('{{ route("user.cover-letters.generate-ai") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_name: formData.get('user_name'),
                        user_email: formData.get('user_email'),
                        user_phone: formData.get('user_phone'),
                        user_address: formData.get('user_address'),
                        recipient_name: formData.get('recipient_name'),
                        company_name: formData.get('company_name'),
                        company_address: formData.get('company_address'),
                        job_description: formData.get('job_description'),
                        additional_info: formData.get('additional_info'),
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('content').value = data.content;
                    showToast(data.message, 'success');
                    
                    // Smooth scroll to content
                    document.getElementById('content').scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    showToast(data.message || 'Failed to generate cover letter', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while generating the cover letter', 'error');
            } finally {
                loadingIndicator.style.display = 'none';
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<i class="bx bx-bot me-1"></i> Generate with AI';
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            // You can use any toast library or create a simple one
            const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
            const toast = document.createElement('div');
            toast.className = `alert ${alertClass} position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
</x-layouts.app>