<x-layouts.app :title="__('Job Finder - User Activity')">
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Job Finder - User Activity</h4>
            <p class="text-muted mb-0">Monitor user job search activity and engagement</p>
        </div>
        <div>
            <a href="{{ route('admin.jobs.api-settings') }}" class="btn btn-outline-primary">
                <i class='bx bx-cog me-1'></i>API Settings
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded me-3">
                            <i class="bx bx-user text-primary" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Users</small>
                            <h4 class="mb-0">{{ number_format($stats['total_users']) }}</h4>
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
                            <i class="bx bx-file text-success" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Users with Resumes</small>
                            <h4 class="mb-0">{{ number_format($stats['users_with_resumes']) }}</h4>
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
                            <i class="bx bx-briefcase text-info" style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Resumes</small>
                            <h4 class="mb-0">{{ number_format($stats['total_resumes']) }}</h4>
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
                            <small class="text-muted d-block">Active Today</small>
                            <h4 class="mb-0">{{ number_format($stats['active_today']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Job Search Activity</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Resumes</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ $user->resumes_count }} resumes</span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class='bx bx-show'></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class='bx bx-folder-open' style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-2">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
