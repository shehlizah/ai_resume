@section('title', __('Create Cover Letter'))
<x-layouts.app :title="__('Create Cover Letter')">
    <div class="row g-4">
        <div class="col-lg-12">
            <a href="{{ route('user.cover-letters') }}" class="btn btn-link ps-0 mb-3">
                <i class="bx bx-chevron-left me-1"></i> Back to Cover Letters
            </a>
        </div>

        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="border rounded p-4">
                <h5 class="mb-4">Create New Cover Letter</h5>

                <form action="{{ route('user.cover-letters.store') }}" method="POST">
                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Cover Letter Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                               placeholder="e.g., Application — Frontend Developer at Acme Health"
                               value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recipient Name -->
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name"
                               placeholder="e.g., John Smith"
                               value="{{ old('recipient_name') }}" required>
                        @error('recipient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Company Name -->
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name"
                               placeholder="e.g., Acme Health"
                               value="{{ old('company_name') }}" required>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Company Address -->
                    <div class="mb-3">
                        <label for="company_address" class="form-label">Company Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_address') is-invalid @enderror" id="company_address" name="company_address"
                               placeholder="e.g., 123 Market St, Cityville, CA 90210"
                               value="{{ old('company_address') }}" required>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content / Cover Letter Body -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Cover Letter Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                                  rows="15" placeholder="Write your cover letter here..." required>{{ old('content') }}</textarea>
                        <small class="text-muted d-block mt-2">
                            <i class="bx bx-info-circle"></i>
                            Pro tip: Include specific details about the company and role to stand out
                        </small>
                        @error('content')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Save Cover Letter
                        </button>
                        <a href="{{ route('user.cover-letters') }}" class="btn btn-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Template Suggestions Sidebar -->
        <div class="col-lg-4">
            <div class="border rounded p-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);">
                <h6 class="mb-3">📋 Quick Templates</h6>

                <!-- Professional Template -->
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start" data-template="professional">
                        <div class="text-start">
                            <div class="fw-semibold">Professional</div>
                            <small class="text-muted">3-paragraph formal</small>
                        </div>
                    </button>
                </div>

                <!-- Concise Template -->
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start" data-template="concise">
                        <div class="text-start">
                            <div class="fw-semibold">Concise</div>
                            <small class="text-muted">Short and impactful</small>
                        </div>
                    </button>
                </div>

                <!-- Conversational Template -->
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start" data-template="conversational">
                        <div class="text-start">
                            <div class="fw-semibold">Conversational</div>
                            <small class="text-muted">Engaging and friendly</small>
                        </div>
                    </button>
                </div>

                <hr>

                <div class="alert alert-info alert-sm mb-0" style="font-size: 0.85rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    Click a template to insert it into the content area. You can customize it with your details.
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('[data-template]').forEach(btn => {
        btn.addEventListener('click', function() {
            const template = this.dataset.template;
            const templates = {
                professional: `[Your Full Name]
[Your Email] · [Your Phone]
[Date]

[Recipient Name]
[Company Name]
[Company Address]

Dear [Recipient Name],

I am writing to express my interest in the [Position Title] role at [Company Name]. With [X years] of experience in [relevant field/skill], I have successfully delivered [brief accomplishment or responsibility], and I am confident my background aligns well with the needs of your team.

At my current/previous role, I [describe 1–2 specific achievements—metrics if possible]. These experiences taught me [skill or value], which I will bring to [Company Name] to help [specific company goal or problem you can solve].

Thank you for considering my application. I welcome the opportunity to discuss how my background and enthusiasm can contribute to your team. I can be reached at [Your Phone] or [Your Email].

Sincerely,
[Your Full Name]`,

                concise: `[Date]

Dear [Recipient Name],

I'm excited to apply for the [Position Title] at [Company Name]. I bring [X years] of experience in [skill area] and a track record of [1-line achievement]. I'm confident I can help [Company Name] achieve [specific outcome].

Thank you for your time — I look forward to speaking with you.

Best regards,
[Your Full Name] — [Your Phone] · [Your Email]`,

                conversational: `[Date]

Hi [Recipient Name],

I've been following [Company Name]'s work on [product/initiative], and I'm impressed by [specific point]. As a [your role] who loves building [what you build], I'd be thrilled to join as [Position Title].

In my recent work, I [short story-style achievement that shows impact]. I enjoy collaborating with teams to turn ideas into measurable results, and I'd love to bring that energy to [Company Name].

If you're open to a quick call, I'd be happy to share more about how I can contribute. Thanks for considering my application.

Warmly,
[Your Full Name]
[Your LinkedIn or portfolio link] · [Your Email]`
            };

            document.getElementById('content').value = templates[template];
            document.getElementById('content').focus();
        });
    });
    </script>
</x-layouts.app>
