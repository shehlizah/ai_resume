<x-layouts.app :title="__('Interview Sessions')">
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Interview Prep Sessions</h4>
            <p class="text-muted mb-0">Monitor and manage AI interview practice sessions</p>
        </div>
        <div>
            <a href="{{ route('admin.interviews.questions') }}" class="btn btn-outline-primary me-2">
                <i class='bx bx-message-dots me-1'></i>Question Bank
            </a>
            <a href="{{ route('admin.interviews.settings') }}" class="btn btn-outline-secondary">
                <i class='bx bx-cog me-1'></i>Settings
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded me-3">
                            <i class="bx bx-chat text-primary" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Sessions</small>
                            <h4 class="mb-0">{{ number_format($stats['total_sessions']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success bg-opacity-10 rounded me-3">
                            <i class="bx bx-check-circle text-success" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Completed</small>
                            <h4 class="mb-0">{{ number_format($stats['completed_sessions']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded me-3">
                            <i class="bx bx-time text-warning" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">In Progress</small>
                            <h4 class="mb-0">{{ number_format($stats['in_progress']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info bg-opacity-10 rounded me-3">
                            <i class="bx bx-bar-chart text-info" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Avg Score</small>
                            <h4 class="mb-0">{{ number_format($stats['avg_score'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.interviews.sessions') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search"
                        placeholder="User, job title, or company..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class='bx bx-search me-1'></i>Filter
                    </button>
                    <a href="{{ route('admin.interviews.sessions') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Questions</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ substr($session->user->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $session->user->name ?? 'Unknown' }}</strong>
                                        <small class="text-muted">{{ $session->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $session->job_title }}</td>
                            <td>{{ $session->company }}</td>
                            <td>
                                <span class="badge bg-label-info">{{ ucfirst($session->interview_type) }}</span>
                            </td>
                            <td>{{ $session->total_questions }} / 5</td>
                            <td>
                                @if($session->overall_score)
                                    <span class="badge {{ $session->overall_score >= 70 ? 'bg-success' : ($session->overall_score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($session->overall_score, 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($session->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning">In Progress</span>
                                @endif
                            </td>
                            <td>{{ $session->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class='bx bx-dots-vertical-rounded'></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.interviews.session-details', $session->session_id) }}">
                                                <i class='bx bx-show me-2'></i>View Details
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.interviews.delete-session', $session->session_id) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this session?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class='bx bx-trash me-2'></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class='bx bx-folder-open' style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-2">No interview sessions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $sessions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
