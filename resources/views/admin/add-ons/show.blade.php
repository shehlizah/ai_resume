<x-layouts.app :title="$addOn->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx {{ $addOn->icon ?? 'bx-package' }} me-2"></i> {{ $addOn->name }}
            </h4>
            <div class="btn-group">
                <a href="{{ route('admin.add-ons.edit', $addOn) }}" class="btn btn-primary">
                    <i class="bx bx-edit me-1"></i> Edit
                </a>
                <a href="{{ route('admin.add-ons.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Purchases</h6>
                        <h3 class="mb-0 text-primary">{{ $stats['total_purchases'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Active Purchases</h6>
                        <h3 class="mb-0 text-success">{{ $stats['active_purchases'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0 text-info">${{ number_format($stats['total_revenue'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">This Month</h6>
                        <h3 class="mb-0 text-warning">${{ number_format($stats['this_month_revenue'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Add-On Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p class="mb-0">{{ $addOn->description }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Price:</strong>
                                <p class="mb-0">${{ number_format($addOn->price, 2) }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Type:</strong>
                                <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $addOn->type)) }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $addOn->is_active ? 'success' : 'secondary' }}">
                                        {{ $addOn->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if($addOn->features)
                            <div class="mb-3">
                                <strong>Features:</strong>
                                <ul class="mt-2">
                                    @foreach($addOn->features as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Purchases -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Purchases</h5>
                        <a href="{{ route('admin.add-ons.purchases', $addOn) }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Gateway</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $purchase)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $purchase->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $purchase->user->email }}</small>
                                            </div>
                                        </td>
                                        <td><strong>${{ number_format($purchase->amount_paid, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst($purchase->payment_gateway ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $purchase->getStatusColor() }}">
                                                {{ ucfirst($purchase->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $purchase->purchased_at->format('M d, Y') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            No purchases yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions & Info -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.add-ons.edit', $addOn) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit Add-On
                            </a>
                            
                            <form action="{{ route('admin.add-ons.toggle-status', $addOn) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-{{ $addOn->is_active ? 'warning' : 'success' }} w-100">
                                    <i class="bx {{ $addOn->is_active ? 'bx-hide' : 'bx-show' }} me-1"></i> 
                                    {{ $addOn->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <a href="{{ route('admin.add-ons.purchases', $addOn) }}" class="btn btn-info">
                                <i class="bx bx-dollar-circle me-1"></i> View All Purchases
                            </a>

                            <form action="{{ route('admin.add-ons.destroy', $addOn) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this add-on?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bx bx-trash me-1"></i> Delete Add-On
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Created</small>
                            <strong>{{ $addOn->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Last Updated</small>
                            <strong>{{ $addOn->updated_at->format('M d, Y') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Slug</small>
                            <code>{{ $addOn->slug }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>