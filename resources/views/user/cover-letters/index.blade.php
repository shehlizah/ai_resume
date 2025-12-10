@section('title', __('Cover Letters'))
<x-layouts.app :title="'My Cover Letters'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📄 My Cover Letters</h4>
            <p class="text-muted mb-0">Manage and create professional cover letters</p>
        </div>
        
           <div class="btn-group">
          <a href="{{ route('user.cover-letters.create') }}" class="btn btn-outline-primary">
                <i class="bx bx-plus me-1"></i> Create Your Cover Letter
            </a>
            </div>
        
        <!--<div class="btn-group">-->
        <!--    <a href="{{ route('user.cover-letters.select-template') }}" class="btn btn-primary">-->
        <!--        <i class="bx bx-palette me-1"></i> Use Template-->
        <!--    </a>-->
        <!--    <a href="{{ route('user.cover-letters.create') }}" class="btn btn-outline-primary">-->
        <!--        <i class="bx bx-plus me-1"></i> Start from Scratch-->
        <!--    </a>-->
        <!--</div>-->
    </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bx bx-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Cover Letters Grid -->
        <div class="row g-4">
            @forelse($coverLetters as $letter)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <i class="bx bx-file" style="font-size: 2.5rem; color: #6366f1;"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.cover-letters.view', $letter) }}">
                                                <i class="bx bx-show me-2"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.cover-letters.edit', $letter) }}">
                                                <i class="bx bx-edit me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.cover-letters.print', $letter) }}" target="_blank">
                                                <i class="bx bx-printer me-2"></i> Print
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.cover-letters.download', $letter) }}">
                                                <i class="bx bx-download me-2"></i> Download
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('user.cover-letters.destroy', $letter) }}" 
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

                            <h5 class="mb-2">{{ Str::limit($letter->title, 40) }}</h5>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bx bx-building me-1"></i>
                                    {{ Str::limit($letter->company_name, 30) }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    <i class="bx bx-user me-1"></i>
                                    {{ $letter->recipient_name }}
                                </small>
                            </div>

                            <div class="border-top pt-3">
                                <small class="text-muted">
                                    <i class="bx bx-calendar me-1"></i>
                                    Created {{ $letter->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-grid gap-2">
                                <a href="{{ route('user.cover-letters.view', $letter) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bx bx-show me-1"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-file" style="font-size: 5rem; opacity: 0.3;"></i>
                            <h4 class="mt-3">No Cover Letters Yet</h4>
                            <p class="text-muted mb-4">
                                Create your first professional cover letter to impress employers!
                            </p>
                            <a href="{{ route('user.cover-letters.create') }}" class="btn btn-primary btn-lg">
                                <i class="bx bx-plus me-1"></i> Create Your First Cover Letter
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($coverLetters->hasPages())
            <div class="mt-4">
                {{ $coverLetters->links() }}
            </div>
        @endif

    </div>

    <style>
                @media (max-width: 768px) {
                    .row.g-4 > [class^="col-"] {
                        flex: 0 0 100%;
                        max-width: 100%;
                    }
                    .card.h-100 {
                        min-height: unset;
                        margin-bottom: 1.2rem;
                    }
                    .card-body, .card-footer {
                        padding-left: 1rem;
                        padding-right: 1rem;
                    }
                    .d-flex.justify-content-between.align-items-center.mb-4 {
                        flex-direction: column;
                        align-items: flex-start !important;
                        gap: 0.5rem;
                    }
                    .btn-group, .btn {
                        width: 100%;
                    }
                    .dropdown-menu {
                        min-width: 150px;
                        font-size: 0.95rem;
                    }
                    .card-footer .btn {
                        font-size: 1rem;
                    }
                }
                @media (max-width: 576px) {
                    .card-body, .card-footer {
                        padding-left: 0.7rem;
                        padding-right: 0.7rem;
                    }
                    .card-footer .btn {
                        font-size: 0.95rem;
                    }
                    .d-flex.justify-content-between.align-items-start.mb-3 {
                        flex-direction: column;
                        align-items: flex-start !important;
                        gap: 0.5rem;
                    }
                }
        .hover-card {
            transition: all 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</x-layouts.app>