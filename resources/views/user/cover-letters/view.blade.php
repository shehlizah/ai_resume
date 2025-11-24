@section('title', $coverLetter->title)
<x-layouts.app :title="$coverLetter->title">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.cover-letters.index') }}">Cover Letters</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($coverLetter->title, 50) }}</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">{{ $coverLetter->title }}</h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-calendar me-1"></i>
                    Created {{ $coverLetter->created_at->format('M d, Y') }}
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('user.cover-letters.edit', $coverLetter) }}" class="btn btn-primary">
                    <i class="bx bx-edit me-1"></i> Edit
                </a>
                <a href="{{ route('user.cover-letters.print', $coverLetter) }}" class="btn btn-info" target="_blank">
                    <i class="bx bx-printer me-1"></i> Print
                </a>
                <a href="{{ route('user.cover-letters.download', $coverLetter) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i> Download
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="{{ route('user.cover-letters.destroy', $coverLetter) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this cover letter?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bx bx-trash me-2"></i> Delete
                            </button>
                        </form>
                    </li>
                </ul>
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
                <div class="card">
                    <div class="card-body p-5">
                        <!-- Cover Letter Preview -->
                        <div class="cover-letter-content">
                            
                            <!-- Your Information -->
                            <div class="mb-4">
                                <strong>{{ auth()->user()->name }}</strong><br>
                                {{ auth()->user()->email }}<br>
                                <small class="text-muted">{{ now()->format('F d, Y') }}</small>
                            </div>

                            <!-- Recipient Information -->
                            <div class="mb-4">
                                <strong>{{ $coverLetter->recipient_name }}</strong><br>
                                {{ $coverLetter->company_name }}<br>
                                {{ $coverLetter->company_address }}
                            </div>

                            <!-- Salutation -->
                            <div class="mb-4">
                                <strong>Dear {{ $coverLetter->recipient_name }},</strong>
                            </div>

                            <!-- Content -->
                            <div class="mb-4" style="text-align: justify; line-height: 1.8;">
                                {!! nl2br(e($coverLetter->content)) !!}
                            </div>

                            <!-- Closing -->
                            <div class="mt-5">
                                <p class="mb-0">
                                    Sincerely,<br><br>
                                    <strong>{{ auth()->user()->name }}</strong>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                
                <!-- Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">📄 Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Title</small>
                            <strong>{{ $coverLetter->title }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Company</small>
                            <strong>{{ $coverLetter->company_name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Recipient</small>
                            <strong>{{ $coverLetter->recipient_name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Created</small>
                            <strong>{{ $coverLetter->created_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Last Updated</small>
                            <strong>{{ $coverLetter->updated_at->format('M d, Y h:i A') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">⚡ Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('user.cover-letters.edit', $coverLetter) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('user.cover-letters.print', $coverLetter) }}" class="btn btn-info" target="_blank">
                                <i class="bx bx-printer me-1"></i> Print
                            </a>
                            <a href="{{ route('user.cover-letters.download', $coverLetter) }}" class="btn btn-success">
                                <i class="bx bx-download me-1"></i> Download PDF
                            </a>
                            <a href="{{ route('user.cover-letters.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>