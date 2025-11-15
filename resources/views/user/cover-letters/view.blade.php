@section('title', $coverLetter->title)
<x-layouts.app :title="$coverLetter->title">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <a href="{{ route('user.cover-letters') }}" class="btn btn-link ps-0 mb-3">
                        <i class="bx bx-chevron-left me-1"></i> Back to Cover Letters
                    </a>
                    <h4 class="mb-1">{{ $coverLetter->title }}</h4>
                    <p class="text-muted">{{ $coverLetter->company_name }} • {{ $coverLetter->created_at->format('M d, Y') }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('user.cover-letters.edit', $coverLetter->id) }}" class="btn btn-primary">
                        <i class="bx bx-pencil me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bx bx-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Cover Letter Preview -->
        <div class="col-lg-8">
            <div class="border rounded p-5" style="background: linear-gradient(135deg, rgba(250, 250, 250, 0.8) 0%, rgba(255, 255, 255, 1) 100%); line-height: 1.6; font-family: 'Georgia', serif;">
                <div style="white-space: pre-wrap; font-size: 0.95rem; color: #333;">{{ $coverLetter->content }}</div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Cover Letter Details -->
            <div class="border rounded p-4 mb-3">
                <h6 class="mb-3">📋 Details</h6>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Recipient</small>
                    <div class="fw-semibold">{{ $coverLetter->recipient_name }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Company</small>
                    <div class="fw-semibold">{{ $coverLetter->company_name }}</div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Address</small>
                    <div class="fw-semibold" style="font-size: 0.9rem;">{{ $coverLetter->company_address }}</div>
                </div>

                <hr>

                <div class="mb-0">
                    <small class="text-muted d-block mb-1">Created</small>
                    <div style="font-size: 0.9rem;">{{ $coverLetter->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border rounded p-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);">
                <h6 class="mb-3">📄 Actions</h6>

                <a href="{{ route('user.cover-letters.edit', $coverLetter->id) }}" class="btn btn-primary w-100 mb-2">
                    <i class="bx bx-pencil me-1"></i> Edit Letter
                </a>

                @if($coverLetter->pdf_url)
                <a href="{{ $coverLetter->pdf_url }}" class="btn btn-outline-primary w-100 mb-2" download>
                    <i class="bx bx-download me-1"></i> Download PDF
                </a>
                @endif

                <button type="button" class="btn btn-outline-secondary w-100 mb-2" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i> Print
                </button>

                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
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

    <style>
        @media print {
            .btn-group, .border, .sidebar, button {
                display: none;
            }
            .col-lg-8 > .border {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</x-layouts.app>
