@section('title', __('Dashboard'))
<x-layouts.app :title="__('Dashboard')">
    <div class="row g-4">
        
         <!-- Quick Actions -->
        <div class="col-lg-12">
            <div class="overflow-hidden rounded border">
                <div class="p-4">
                    <h6 class="text-muted fw-normal small mb-3">Quick Actions</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-user-plus me-1"></i> Add User
                        </a>
                        <a href="{{ route('admin.templates.create') }}" class="btn btn-sm btn-info">
                            <i class="bx bx-file-blank me-1"></i> Add Template
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bx bx-list-ul me-1"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.templates.index') }}" class="btn btn-sm btn-warning">
                            <i class="bx bx-layer me-1"></i> Manage Templates
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- First Row - 3 Cards -->
        <div class="col-lg-4">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Total Users</h6>
                        <h3 class="mb-1 text-primary">{{ $totalUsers }}</h3>
                    </div>
                    <small class="text-muted">
                        <i class="bx bx-up-arrow-alt"></i>
                        <span class="fw-semibold">{{ $userGrowth }}%</span> this month
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(34, 197, 94, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Active Users</h6>
                        <h3 class="mb-1 text-success">{{ $activeUsers }}</h3>
                    </div>
                    <small class="text-muted">
                        {{ number_format(($activeUsers / max($totalUsers, 1) * 100), 1) }}% of total users
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="overflow-hidden rounded border h-100" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Resume Templates</h6>
                        <h3 class="mb-1 text-info">{{ $totalTemplates }}</h3>
                    </div>
                    <small class="text-muted">
                        <span class="badge bg-light" style="color: #fb923c;">{{ $premiumTemplates }}</span> Premium | 
                        <span class="badge bg-light" style="color: #22c55e;">{{ $activeTemplates }}</span> Active
                    </small>
                </div>
            </div>
        </div>

        <!-- Second Row - Full Width -->
        <div class="col-lg-12">
            <div class="overflow-hidden rounded border" style="aspect-ratio: 16/6;">
                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, rgba(251, 146, 60, 0.05) 0%, rgba(251, 146, 60, 0.02) 100%);">
                    <div>
                        <h6 class="text-muted fw-normal small mb-2">Resumes Generated This Month</h6>
                        <h3 class="mb-1" style="color: #fb923c;">{{ $downloadsThisMonth }}</h3>
                    </div>
                    <div class="d-flex gap-3">
                        <div>
                            <small class="text-muted d-block">Total (All Time)</small>
                            <strong>{{ $totalResumesGenerated }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">New Users</small>
                            <strong>{{ $newUsersThisWeek }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Avg/User</small>
                            <strong>{{ $avgDownloadsPerUser }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Admin Users</small>
                            <strong>{{ $adminCount }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third Row - 2 Tables -->
        <div class="col-lg-6">
            <div class="overflow-hidden rounded border">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="px-4 py-3">Recent Users</th>
                                <th class="px-4 py-3 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center text-muted">No users yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="overflow-hidden rounded border">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="px-4 py-3">Popular Templates</th>
                                <th class="px-4 py-3 text-end">Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popularTemplates as $template)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div>
                                            <strong>{{ $template->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                @if($template->is_premium)
                                                    <span class="badge bg-warning">Premium</span>
                                                @else
                                                    <span class="badge bg-info">Free</span>
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <strong>{{ $template->downloads ?? 0 }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center text-muted">No templates yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05) !important;
        }
        
        .table thead {
            background-color: #f8f9fa;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        .btn-sm {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
    </style>
</x-layouts.app>