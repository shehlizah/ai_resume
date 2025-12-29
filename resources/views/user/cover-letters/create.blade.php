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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 pb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted small fw-semibold mb-1">Step-by-step flow</div>
                            <h5 class="mb-0">Create New Cover Letter</h5>
                        </div>
                        <span class="badge bg-primary-subtle text-primary fw-semibold">3 steps</span>
                    </div>

                    <form id="coverLetterForm" action="{{ route('user.cover-letters.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Step 1 -->
                        <div class="step-section mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="text-muted small fw-semibold">Step 1 of 3</div>
                                    <h6 class="mb-0">Basic Details</h6>
                                </div>
                                <span class="badge bg-light text-muted border">Required</span>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Cover Letter Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control compact-input @error('title') is-invalid @enderror" 
                                       id="title" name="title"
                                       placeholder="e.g., Application for Software Engineer at Google"
                                       value="{{ old('title') }}" required>
                                <small class="text-muted">Keep it clear so you can find it later.</small>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="user_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control compact-input @error('user_name') is-invalid @enderror" 
                                           id="user_name" name="user_name"
                                           value="{{ old('user_name', $userData['name']) }}" required>
                                    @error('user_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="user_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control compact-input @error('user_email') is-invalid @enderror" 
                                           id="user_email" name="user_email"
                                           value="{{ old('user_email', $userData['email']) }}" required>
                                    @error('user_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label for="user_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control compact-input @error('user_phone') is-invalid @enderror" 
                                           id="user_phone" name="user_phone"
                                           placeholder="+1 (555) 123-4567"
                                           value="{{ old('user_phone', $userData['phone']) }}" required>
                                    @error('user_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="user_address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control compact-input @error('user_address') is-invalid @enderror" 
                                           id="user_address" name="user_address"
                                           placeholder="123 Main St, City, State ZIP"
                                           value="{{ old('user_address', $userData['address']) }}" required>
                                    @error('user_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control compact-input @error('recipient_name') is-invalid @enderror" 
                                           id="recipient_name" name="recipient_name"
                                           placeholder="e.g., John Smith"
                                           value="{{ old('recipient_name') }}" required>
                                    @error('recipient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control compact-input @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name"
                                           placeholder="e.g., Google Inc."
                                           value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-2">
                                <label for="company_address" class="form-label">Company Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control compact-input @error('company_address') is-invalid @enderror" 
                                       id="company_address" name="company_address"
                                       placeholder="e.g., 1600 Amphitheatre Parkway, Mountain View, CA 94043"
                                       value="{{ old('company_address') }}" required>
                                @error('company_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="step-section mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="text-muted small fw-semibold">Step 2 of 3</div>
                                    <h6 class="mb-0">Job Information <span class="text-muted">(Optional)</span></h6>
                                </div>
                                <span class="badge bg-light text-muted border">Improves AI quality</span>
                            </div>

                            <div class="mb-3">
                                <label for="job_description" class="form-label">Job Description</label>
                                <textarea class="form-control autogrow @error('job_description') is-invalid @enderror" id="job_description" name="job_description" 
                                          rows="5" placeholder="Paste the job description here to help AI generate a better cover letter...">{{ old('job_description') }}</textarea>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle"></i> Adding this helps AI tailor the letter to the role.
                                </small>
                            </div>

                            <div class="mb-0">
                                <label for="additional_info" class="form-label">Your Skills & Experience</label>
                                <textarea class="form-control autogrow @error('additional_info') is-invalid @enderror" id="additional_info" name="additional_info" 
                                          rows="4" placeholder="Briefly describe your relevant skills, experience, or achievements...">{{ old('additional_info') }}</textarea>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="step-section mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="text-muted small fw-semibold">Step 3 of 3</div>
                                    <h6 class="mb-0">Cover Letter Content</h6>
                                </div>
                                <span class="badge bg-light text-muted border">Required</span>
                            </div>

                            <div class="ai-hero mb-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">Generate with AI</div>
                                        <small class="text-muted">Fill basic details, then let AI write the letter for you.</small>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Tone selector">
                                            <input type="radio" class="btn-check" name="tone" id="toneFormal" value="formal" checked>
                                            <label class="btn btn-outline-primary" for="toneFormal">Formal</label>
                                            <input type="radio" class="btn-check" name="tone" id="toneFriendly" value="friendly">
                                            <label class="btn btn-outline-primary" for="toneFriendly">Friendly</label>
                                            <input type="radio" class="btn-check" name="tone" id="toneConfident" value="confident">
                                            <label class="btn btn-outline-primary" for="toneConfident">Confident</label>
                                        </div>
                                        <button type="button" id="generateAIBtn" class="btn btn-primary px-4 fw-semibold">
                                            <i class="bx bx-bot me-1"></i> Generate with AI
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="livePreviewToggle">
                                    <label class="form-check-label small" for="livePreviewToggle">Live Preview</label>
                                </div>
                                <span class="small text-muted" id="wordCount">0 words</span>
                            </div>

                            <div id="livePreviewCard" class="card border mb-2 d-none">
                                <div class="card-body py-2">
                                    <div class="small text-muted mb-1">Preview</div>
                                    <div id="livePreviewContent" class="small">Start typing to preview your letter.</div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="content" class="form-label mb-1">Letter Body <span class="text-danger">*</span></label>
                                <textarea class="form-control autogrow @error('content') is-invalid @enderror" 
                                          id="content" name="content"
                                          rows="10" placeholder="Your cover letter content will appear here..." required>{{ old('content') }}</textarea>
                                <div id="contentError" class="text-danger small mt-1" style="display: none;">Letter body is required</div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-info-circle"></i>
                                    Click "Generate with AI" or start writing from a template. Tone applies to AI output.
                                </small>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="aiLoadingIndicator" class="alert alert-info mb-2" style="display: none;">
                                <i class="bx bx-loader-alt bx-spin me-2"></i> Generating your cover letter with AI... This may take a few seconds.
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mt-3 align-items-center flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Cover Letter
                            </button>
                            <a href="{{ route('user.cover-letters.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <small class="text-muted">You can edit this later.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Template Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 16px;">
                <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                    <h6 class="mb-0 text-primary">
                        <i class="bx bx-bolt me-1"></i> Start Faster with Templates
                    </h6>
                    <small class="text-muted">Pick one, then tweak or regenerate.</small>
                </div>
                <div class="card-body py-3" style="max-height: 540px; overflow-y: auto;">
                    @forelse($templates as $template)
                        <div class="template-item mb-2 p-3 border rounded cursor-pointer" data-template-id="{{ $template->id }}">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <h6 class="mb-1">{{ $template->name }}</h6>
                                    <small class="text-muted d-block mb-2">{{ $template->description }}</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge rounded-pill bg-light text-muted border">Professional</span>
                                        <span class="badge rounded-pill bg-light text-muted border">Concise</span>
                                        <span class="badge rounded-pill bg-light text-muted border">Conversational</span>
                                    </div>
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
                <div class="card-body py-3">
                    <h6 class="text-primary mb-2">
                        <i class="bx bx-bulb me-1"></i> AI Generation Tips
                    </h6>
                    <ul class="small mb-0">
                        <li>Fill Step 1 before generating.</li>
                        <li>Add job description to boost relevance.</li>
                        <li>Select a tone to shape AI output.</li>
                        <li>You can edit everything afterward.</li>
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
        .step-section {
            background: #fbfcff;
            border: 1px solid #edf1ff;
            border-radius: 10px;
            padding: 1rem 1.1rem;
        }

        .compact-input {
            height: 44px;
        }

        textarea.autogrow {
            overflow: hidden;
            min-height: 140px;
        }

        #content {
            min-height: 200px;
        }

        .ai-hero {
            background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(99,102,241,0.02) 100%);
            border: 1px solid rgba(99,102,241,0.12);
            border-radius: 10px;
            padding: 0.95rem 1rem;
        }

        .template-item {
            transition: all 0.2s ease;
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

        .step-section + .step-section { margin-top: 1rem; }
        .card-body.p-4.pb-3 { padding-bottom: 1.5rem !important; }
    </style>

    <script>
        const contentEl = document.getElementById('content');
        const contentError = document.getElementById('contentError');
        const wordCountEl = document.getElementById('wordCount');
        const livePreviewToggle = document.getElementById('livePreviewToggle');
        const livePreviewCard = document.getElementById('livePreviewCard');
        const livePreviewContent = document.getElementById('livePreviewContent');

        // Prefill letter header with form data
        const formFields = ['user_name','user_address','user_email','user_phone','recipient_name','company_name','company_address'];
        let lastGeneratedBody = '';

        function formatToday() {
            const d = new Date();
            return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
        }

        function buildPrefillBody() {
            const getVal = id => (document.getElementById(id)?.value || '').trim();
            const userName = getVal('user_name');
            const userAddress = getVal('user_address');
            const userEmail = getVal('user_email');
            const userPhone = getVal('user_phone');
            const recipientName = getVal('recipient_name') || 'Hiring Manager';
            const companyName = getVal('company_name');
            const companyAddress = getVal('company_address');
            const title = getVal('title');

            return [
                userName,
                userAddress,
                userEmail,
                userPhone,
                formatToday(),
                '',
                recipientName,
                companyName,
                companyAddress,
                '',
                `Dear ${recipientName},`,
                '',
                title ? `I am excited to apply for ${title}.` : 'I am excited to apply for this role.',
                'Please find my cover letter below.',
                '',
                'Best regards,',
                userName
            ].filter(Boolean).join('\n');
        }

        function hasPlaceholders(text) {
            return /(\[Your Name\]|\[Your Address\]|\[City, State, Zip Code\]|\[Email Address\]|\[Phone Number\]|\[Date\]|\[Recipient Name\]|\[Company Name\]|\[Company Address\]|John Abc)/i.test(text);
        }

        function replacePlaceholders(text) {
            const getVal = id => (document.getElementById(id)?.value || '').trim();
            const map = {
                '[Your Name]': getVal('user_name'),
                '[Your Address]': getVal('user_address'),
                '[City, State, Zip Code]': getVal('user_address'),
                '[Email Address]': getVal('user_email'),
                '[Phone Number]': getVal('user_phone'),
                '[Date]': formatToday(),
                '[Recipient Name]': getVal('recipient_name') || 'Hiring Manager',
                '[Company Name]': getVal('company_name'),
                '[Company Address]': getVal('company_address'),
                'John Abc': getVal('recipient_name') || 'Hiring Manager'
            };

            let updated = text;
            Object.entries(map).forEach(([key, val]) => {
                const re = new RegExp(key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
                updated = updated.replace(re, val || '');
            });
            return updated.replace(/\n{3,}/g, '\n\n').trim();
        }

        function maybePrefillBody(force = false) {
            const current = contentEl.value;
            if (hasPlaceholders(current)) {
                const replaced = replacePlaceholders(current);
                if (replaced !== current) {
                    contentEl.value = replaced;
                    autoGrow(contentEl);
                    updateWordCount();
                    renderPreview();
                    return;
                }
            }
            const trimmed = current.trim();
            if (force || !trimmed || trimmed === lastGeneratedBody) {
                const prefill = buildPrefillBody();
                contentEl.value = prefill;
                lastGeneratedBody = prefill.trim();
                autoGrow(contentEl);
                updateWordCount();
                renderPreview();
            }
        }

        formFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', () => maybePrefillBody(false));
            }
        });

        // Template selection
        document.querySelectorAll('.use-template-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const templateId = this.dataset.templateId;
                const content = document.getElementById('template-content-' + templateId).textContent;
                contentEl.value = content;
                autoGrow(contentEl);
                updateWordCount();
                renderPreview();
                
                contentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                showToast('Template applied. You can edit it now.', 'success');
            });
        });

        // Auto-grow textareas
        function autoGrow(textarea) {
            const min = Number(textarea.dataset.minHeight || 140);
            textarea.style.height = 'auto';
            textarea.style.height = Math.max(textarea.scrollHeight, min) + 'px';
        }

        document.querySelectorAll('textarea.autogrow').forEach(el => {
            el.dataset.minHeight = el.clientHeight;
            autoGrow(el);
            el.addEventListener('input', () => autoGrow(el));
        });

        function updateWordCount() {
            const words = contentEl.value.trim().split(/\s+/).filter(Boolean).length;
            wordCountEl.textContent = `${words} word${words === 1 ? '' : 's'}`;
        }

        function renderPreview() {
            const text = contentEl.value.trim() || 'Start typing to preview your letter.';
            livePreviewContent.innerHTML = text.replace(/\n/g, '<br>');
        }

        contentEl.addEventListener('input', () => {
            updateWordCount();
            renderPreview();
            if (contentEl.value.trim()) {
                contentError.style.display = 'none';
                contentEl.classList.remove('is-invalid');
            }
        });

        livePreviewToggle.addEventListener('change', () => {
            livePreviewCard.classList.toggle('d-none', !livePreviewToggle.checked);
            if (livePreviewToggle.checked) {
                renderPreview();
            }
        });

        // AI Generation
        document.getElementById('generateAIBtn').addEventListener('click', async function() {
            const form = document.getElementById('coverLetterForm');
            const formData = new FormData(form);
            const loadingIndicator = document.getElementById('aiLoadingIndicator');
            const generateBtn = this;

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
                        tone: document.querySelector('input[name="tone"]:checked')?.value || 'formal'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    contentEl.value = data.content;
                    autoGrow(contentEl);
                    updateWordCount();
                    renderPreview();
                    contentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    showToast(data.message || 'Generated with AI. You can edit it now.', 'success');
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

        // Form validation for letter body
        document.getElementById('coverLetterForm').addEventListener('submit', function(e) {
            if (!contentEl.value.trim()) {
                e.preventDefault();
                contentEl.classList.add('is-invalid');
                contentError.style.display = 'block';
                contentEl.focus();
            } else {
                contentError.style.display = 'none';
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
            const toast = document.createElement('div');
            toast.className = `alert ${alertClass} position-fixed top-0 end-0 m-3 shadow`; 
            toast.style.zIndex = '9999';
            toast.innerHTML = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3200);
        }

        // Initialize counters
        updateWordCount();
        renderPreview();
        maybePrefillBody(true);
    </script>
</x-layouts.app>