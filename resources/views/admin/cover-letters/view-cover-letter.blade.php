<x-layouts.app :title="'View Cover Letter'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-file me-2"></i> Cover Letter Details
            </h4>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i> Print
                </button>
                <a href="{{ route('admin.cover-letters.user-cover-letters') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">{{ $coverLetter->title }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cover Letter Preview -->
                        <div class="cover-letter-preview p-4" style="background: #fff; border: 1px solid #dee2e6; min-height: 600px;">
                            <div class="mb-4">
                                <strong>{{ $coverLetter->user->name }}</strong><br>
                                {{ $coverLetter->user->email }}<br>
                                <small class="text-muted">{{ now()->format('F d, Y') }}</small>
                            </div>

                            <div class="mb-4">
                                <strong>{{ $coverLetter->recipient_name }}</strong><br>
                                {{ $coverLetter->company_name }}<br>
                                {{ $coverLetter->company_address }}
                            </div>

                            <div class="mb-4">
                                <strong>Dear {{ $coverLetter->recipient_name }},</strong>
                            </div>

                            <div class="content" style="text-align: justify; line-height: 1.8;">
                                {!! nl2br(e($coverLetter->content)) !!}
                            </div>

                            <div class="mt-5">
                                <p class="mb-0">
                                    Sincerely,<br>
                                    <strong>{{ $coverLetter->user->name }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Status Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Current Status</small>
                            <span class="badge bg-{{ $coverLetter->is_deleted ? 'danger' : 'success' }}">
                                {{ $coverLetter->is_deleted ? 'Deleted' : 'Active' }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Created</small>
                            <strong>{{ $coverLetter->created_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Last Updated</small>
                            <strong>{{ $coverLetter->updated_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Cover Letter ID</small>
                            <code>#{{ $coverLetter->id }}</code>
                        </div>
                    </div>
                </div>

                <!-- User Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">User Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-primary">
                                    {{ strtoupper(substr($coverLetter->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <strong>{{ $coverLetter->user->name }}</strong><br>
                                <small class="text-muted">{{ $coverLetter->user->email }}</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Total Cover Letters:</small>
                            <strong>{{ $coverLetter->user->coverLetters()->count() }}</strong>
                        </div>
                        <div>
                            <small class="text-muted">Member Since:</small>
                            <strong>{{ $coverLetter->user->created_at->format('M Y') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($coverLetter->is_deleted)
                                <form action="{{ route('admin.cover-letters.restore', $coverLetter) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bx bx-undo me-1"></i> Restore Cover Letter
                                    </button>
                                </form>
                                <form action="{{ route('admin.cover-letters.permanent-delete', $coverLetter) }}" method="POST" onsubmit="return confirm('Permanently delete? This cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bx bx-trash-alt me-1"></i> Delete Permanently
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-primary w-100" onclick="window.print()">
                                    <i class="bx bx-printer me-1"></i> Print
                                </button>
                                <form action="{{ route('admin.cover-letters.delete-cover-letter', $coverLetter) }}" method="POST" onsubmit="return confirm('Delete this cover letter?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        @media print {
            .btn, .card-header, nav, .sidebar { display: none !important; }
            .cover-letter-preview { border: none !important; }
            .card { box-shadow: none !important; border: none !important; }
        }
    </style>
</x-layouts.app>