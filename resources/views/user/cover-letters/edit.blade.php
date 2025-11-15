@section('title', __('Edit Cover Letter'))
<x-layouts.app :title="__('Edit Cover Letter')">
    <div class="row g-4">
        <div class="col-lg-12">
            <a href="{{ route('user.cover-letters') }}" class="btn btn-link ps-0 mb-3">
                <i class="bx bx-chevron-left me-1"></i> Back to Cover Letters
            </a>
        </div>

        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="border rounded p-4">
                <h5 class="mb-4">Edit Cover Letter</h5>

                <form action="{{ route('user.cover-letters.update', $coverLetter->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Cover Letter Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                               placeholder="e.g., Application — Frontend Developer at Acme Health"
                               value="{{ old('title', $coverLetter->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recipient Name -->
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name"
                               placeholder="e.g., John Smith"
                               value="{{ old('recipient_name', $coverLetter->recipient_name) }}" required>
                        @error('recipient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Company Name -->
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name"
                               placeholder="e.g., Acme Health"
                               value="{{ old('company_name', $coverLetter->company_name) }}" required>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Company Address -->
                    <div class="mb-3">
                        <label for="company_address" class="form-label">Company Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_address') is-invalid @enderror" id="company_address" name="company_address"
                               placeholder="e.g., 123 Market St, Cityville, CA 90210"
                               value="{{ old('company_address', $coverLetter->company_address) }}" required>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content / Cover Letter Body -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Cover Letter Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                                  rows="15" placeholder="Write your cover letter here..." required>{{ old('content', $coverLetter->content) }}</textarea>
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
                            <i class="bx bx-save me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('user.cover-letters') }}" class="btn btn-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="border rounded p-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);">
                <h6 class="mb-3">📋 Cover Letter Info</h6>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Created</small>
                    <div class="fw-semibold">{{ $coverLetter->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Last Updated</small>
                    <div class="fw-semibold">{{ $coverLetter->updated_at->format('M d, Y \a\t h:i A') }}</div>
                </div>

                <hr>

                <div class="mb-3">
                    <a href="{{ route('user.cover-letters.view', $coverLetter->id) }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="bx bx-eye me-1"></i> Preview
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bx bx-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Cover Letter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $coverLetter->title }}</strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('user.cover-letters.destroy', $coverLetter->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
