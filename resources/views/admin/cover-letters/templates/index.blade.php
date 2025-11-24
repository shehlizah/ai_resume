<x-layouts.app :title="'Cover Letter Templates'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-layout me-2"></i> Cover Letter Templates
            </h4>
            <a href="{{ route('admin.cover-letters.templates.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Create Template
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-layout fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Templates</small>
                                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Active</small>
                                <h4 class="mb-0">{{ $stats['active'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        

        </div>

        <!-- Templates Grid -->
        <div class="row g-4">
            @forelse($templates as $template)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <i class="bx bx-file-blank" style="font-size: 2.5rem; color: #6366f1;"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.cover-letters.templates.show', $template) }}">
                                                <i class="bx bx-show me-1"></i> View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.cover-letters.templates.edit', $template) }}">
                                                <i class="bx bx-edit me-1"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.cover-letters.templates.duplicate', $template) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx bx-copy me-1"></i> Duplicate
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.cover-letters.templates.toggle', $template) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item {{ $template->is_active ? 'text-warning' : 'text-success' }}">
                                                    <i class="bx {{ $template->is_active ? 'bx-hide' : 'bx-show' }} me-1"></i> 
                                                    {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.cover-letters.templates.delete', $template) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Delete this template?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h5 class="mb-2">{{ $template->name }}</h5>
                            <p class="text-muted small mb-3">{{ Str::limit($template->description, 100) }}</p>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-{{ $template->getCategoryColor() }}">
                                    {{ ucfirst($template->category) }}
                                </span>
                                @if($template->is_premium)
                                    <span class="badge bg-warning">
                                        <i class="bx bx-crown"></i> Premium - ${{ number_format($template->price, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="bx bx-gift"></i> Free
                                    </span>
                                @endif
                                <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="border-top pt-3">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Usage Count</small>
                                        <strong>{{ number_format($template->usage_count) }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Sort Order</small>
                                        <strong>{{ $template->sort_order }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-layout" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="mt-3">No Templates Yet</h5>
                            <p class="text-muted">Create your first cover letter template!</p>
                            <a href="{{ route('admin.cover-letters.templates.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Create Template
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</x-layouts.app>