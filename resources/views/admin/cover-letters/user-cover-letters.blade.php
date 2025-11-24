<x-layouts.app :title="'User Cover Letters'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-folder-open me-2"></i> All User Cover Letters
            </h4>
            <a href="{{ route('admin.cover-letters.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.cover-letters.user-cover-letters') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search title, company..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.cover-letters.user-cover-letters') }}" class="btn btn-secondary">
                                <i class="bx bx-reset me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cover Letters Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>User</th>
                            <th>Recipient</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coverLetters as $coverLetter)
                            <tr>
                                <td><strong>#{{ $coverLetter->id }}</strong></td>
                                <td>
                                    <div>
                                        <strong>{{ Str::limit($coverLetter->title, 40) }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $coverLetter->user->name }}<br>
                                        <small class="text-muted">{{ $coverLetter->user->email }}</small>
                                    </div>
                                </td>
                                <td>{{ $coverLetter->recipient_name }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $coverLetter->company_name }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($coverLetter->company_address, 30) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $coverLetter->is_deleted ? 'danger' : 'success' }}">
                                        {{ $coverLetter->is_deleted ? 'Deleted' : 'Active' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $coverLetter->created_at->format('M d, Y') }}<br>
                                    {{ $coverLetter->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.cover-letters.view-cover-letter', $coverLetter) }}">
                                                    <i class="bx bx-show me-1"></i> View Details
                                                </a>
                                            </li>
                                            @if($coverLetter->is_deleted)
                                                <li>
                                                    <form action="{{ route('admin.cover-letters.restore', $coverLetter) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="bx bx-undo me-1"></i> Restore
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.cover-letters.permanent-delete', $coverLetter) }}" method="POST" onsubmit="return confirm('Permanently delete this cover letter? This cannot be undone!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash-alt me-1"></i> Delete Permanently
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.cover-letters.delete-cover-letter', $coverLetter) }}" method="POST" onsubmit="return confirm('Delete this cover letter?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bx bx-envelope" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No cover letters found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($coverLetters->hasPages())
                <div class="card-footer">
                    {{ $coverLetters->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>