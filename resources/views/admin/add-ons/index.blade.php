<x-layouts.app :title="'Manage Add-Ons'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-package me-2"></i> Manage Add-Ons
            </h4>
            <a href="{{ route('admin.add-ons.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Create Add-On
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Add-Ons Grid -->
        <div class="row g-4">
            @forelse($addOns as $addOn)
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <i class="bx {{ $addOn->icon ?? 'bx-gift' }}" style="font-size: 2rem; color: #6366f1;"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.add-ons.show', $addOn) }}">
                                                <i class="bx bx-show me-1"></i> View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.add-ons.edit', $addOn) }}">
                                                <i class="bx bx-edit me-1"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.add-ons.purchases', $addOn) }}">
                                                <i class="bx bx-dollar-circle me-1"></i> View Purchases
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.add-ons.toggle-status', $addOn) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item {{ $addOn->is_active ? 'text-warning' : 'text-success' }}">
                                                    <i class="bx {{ $addOn->is_active ? 'bx-hide' : 'bx-show' }} me-1"></i> 
                                                    {{ $addOn->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.add-ons.destroy', $addOn) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Delete this add-on?')">
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

                            <h5 class="mb-2">{{ $addOn->name }}</h5>
                            <p class="text-muted small mb-3">{{ Str::limit($addOn->description, 100) }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-primary">${{ number_format($addOn->price, 2) }}</span>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $addOn->type)) }}</span>
                                </div>
                                <span class="badge bg-{{ $addOn->is_active ? 'success' : 'secondary' }}">
                                    {{ $addOn->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="border-top pt-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Purchases</small>
                                        <strong>{{ $addOn->user_add_ons_count }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Active</small>
                                        <strong class="text-success">{{ $addOn->active_purchases_count }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Revenue</small>
                                        <strong class="text-primary">${{ number_format($addOn->total_revenue, 2) }}</strong>
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
                            <i class="bx bx-package" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="mt-3">No Add-Ons Yet</h5>
                            <p class="text-muted">Create your first add-on to start earning extra revenue!</p>
                            <a href="{{ route('admin.add-ons.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Create Add-On
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</x-layouts.app>