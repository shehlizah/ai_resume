<x-layouts.app :title="$title ?? 'User Management'">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="bx bx-user-circle text-primary"></i> User Management</h4>
                <p class="text-muted mb-0">Manage all users and their resumes</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus-circle"></i> Add User
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-group fs-2 text-primary mb-2"></i>
                        <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                        <small class="text-muted">Total Users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-check-circle fs-2 text-success mb-2"></i>
                        <h4 class="mb-0">{{ $stats['active_users'] }}</h4>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-x-circle fs-2 text-warning mb-2"></i>
                        <h4 class="mb-0">{{ $stats['inactive_users'] }}</h4>
                        <small class="text-muted">Inactive</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-file fs-2 text-info mb-2"></i>
                        <h4 class="mb-0">{{ $stats['total_resumes'] }}</h4>
                        <small class="text-muted">Resumes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-time-five fs-2 text-secondary mb-2"></i>
                        <h4 class="mb-0">{{ $stats['new_users_today'] }}</h4>
                        <small class="text-muted">Today</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-calendar-week fs-2 text-dark mb-2"></i>
                        <h4 class="mb-0">{{ $stats['new_users_week'] }}</h4>
                        <small class="text-muted">This Week</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm">
            <!-- Search & Filters -->
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text"
                                       class="form-control border-start-0 ps-0"
                                       name="search"
                                       placeholder="Search by name or email..."
                                       value="{{ request('search') }}">
                                @if(request('search'))
                                    <a href="{{ route('admin.users.index') }}"
                                       class="btn btn-outline-secondary"
                                       data-bs-toggle="tooltip"
                                       title="Clear search">
                                        <i class="bx bx-x"></i>
                                    </a>
                                @endif
                                <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-search-alt"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Filters -->
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2">
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort_by" class="form-select">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Join Date</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort_order" class="form-select">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Newest First</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bx bx-filter-alt"></i> Apply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <form method="POST" action="{{ route('admin.users.bulk-action') }}" id="bulkActionForm">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Select All
                            </label>
                        </div>
                        <div class="d-flex gap-2">
                            <select name="action" class="form-select form-select-sm" style="width: auto;" required>
                                <option value="">Bulk Actions</option>
                                <option value="activate">✓ Activate</option>
                                <option value="deactivate">✗ Deactivate</option>
                                <option value="delete">🗑 Delete</option>
                            </select>
                            <button type="submit"
                                    class="btn btn-sm btn-dark"
                                    onclick="return confirm('Apply bulk action to selected users?')">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="30"><input type="checkbox" id="selectAllTable"></th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Resumes</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   class="user-checkbox form-check-input"
                                                   name="user_ids[]"
                                                   value="{{ $user->id }}"
                                                   {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle bg-primary text-white">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong class="d-block">{{ $user->name }}</strong>
                                                    <div class="d-flex gap-1">
                                                        @if($user->id === auth()->id())
                                                            <span class="badge bg-info" style="font-size: 9px;">You</span>
                                                        @endif
                                                        @if($user->role === 'admin')
                                                            <span class="badge bg-danger" style="font-size: 9px;">Admin</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bx bx-envelope"></i> {{ $user->email }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary rounded-pill">
                                                <i class="bx bx-file"></i> {{ $user->resumes_count }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @if($user->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-check"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-x"></i> Inactive
                                                    </span>
                                                @endif
                                                @if($user->has_lifetime_access)
                                                    <span class="badge bg-primary">
                                                        <i class="bx bx-infinite"></i> Lifetime Pro
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bx bx-calendar"></i> {{ $user->created_at->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <!-- View -->
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   data-bs-toggle="tooltip"
                                                   title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <!-- Edit -->
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   data-bs-toggle="tooltip"
                                                   title="Edit User">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if($user->id !== auth()->id())
                                                    <!-- Toggle Lifetime Access -->
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-{{ $user->has_lifetime_access ? 'danger' : 'primary' }} toggle-lifetime-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            data-has-access="{{ $user->has_lifetime_access ? 'true' : 'false' }}"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ $user->has_lifetime_access ? 'Revoke Lifetime Access' : 'Grant Lifetime Access' }}">
                                                        <i class="bx {{ $user->has_lifetime_access ? 'bx-crown' : 'bx-crown' }}"></i>
                                                    </button>

                                                    <!-- Toggle Status -->
                                                    <form method="POST"
                                                          action="{{ route('admin.users.toggle-status', $user->id) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-{{ $user->is_active ? 'secondary' : 'success' }}"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bx {{ $user->is_active ? 'bx-lock' : 'bx-lock-open' }}"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Delete -->
                                                    <form method="POST"
                                                          action="{{ route('admin.users.destroy', $user->id) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Delete {{ $user->name }} and all their resumes?\n\nThis action cannot be undone!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="tooltip"
                                                                title="Delete User">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- Can't delete yourself -->
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                            disabled
                                                            data-bs-toggle="tooltip"
                                                            title="Can't modify yourself">
                                                        <i class="bx bx-shield"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="bx bx-user-x fs-1 text-muted d-block mb-2"></i>
                                            <p class="text-muted mb-0">No users found</p>
                                            @if(request('search'))
                                                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link">
                                                    Clear filters
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </small>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem 0.5rem;
        }

        .btn-outline-info:hover {
            color: #fff;
            background-color: #0dcaf0;
            border-color: #0dcaf0;
        }

        .btn-outline-warning:hover {
            color: #000;
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-outline-danger:hover {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-success:hover {
            color: #fff;
            background-color: #198754;
            border-color: #198754;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Select All functionality
            document.getElementById('selectAll')?.addEventListener('change', function() {
                document.querySelectorAll('.user-checkbox:not([disabled])').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            document.getElementById('selectAllTable')?.addEventListener('change', function() {
                document.querySelectorAll('.user-checkbox:not([disabled])').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Toggle Lifetime Access
            document.querySelectorAll('.toggle-lifetime-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    const hasAccess = this.dataset.hasAccess === 'true';
                    const action = hasAccess ? 'revoke' : 'grant';

                    if (confirm(`${action === 'grant' ? 'Grant' : 'Revoke'} lifetime Pro access for ${userName}?`)) {
                        fetch(`/admin/users/${userId}/toggle-lifetime-access`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to update access'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred');
                        });
                    }
                });
            });
        });
    </script>
</x-layouts.app>
