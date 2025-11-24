<x-layouts.app :title="'My Add-Ons'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="mb-4">
            <h4 class="fw-bold mb-2">📦 My Add-Ons</h4>
            <p class="text-muted">Manage and access your purchased add-ons</p>
        </div>

        <div class="row g-4">
            @forelse($addOns as $userAddOn)
                @php $addOn = $userAddOn->addOn; @endphp
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bx {{ $addOn->icon ?? 'bx-gift' }} me-3" style="font-size: 2.5rem; color: #6366f1;"></i>
                                    <div>
                                        <h5 class="mb-1">{{ $addOn->name }}</h5>
                                        <small class="text-muted">{{ Str::limit($addOn->description, 80) }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-{{ $userAddOn->getStatusColor() }}">
                                    {{ ucfirst($userAddOn->status) }}
                                </span>
                            </div>

                            <div class="border-top border-bottom py-3 mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Purchased</small>
                                        <strong>{{ $userAddOn->purchased_at->format('M d, Y') }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Amount</small>
                                        <strong class="text-primary">${{ number_format($userAddOn->amount_paid, 2) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Gateway</small>
                                        <span class="badge bg-info">{{ ucfirst($userAddOn->payment_gateway ?? 'N/A') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($userAddOn->isActive())
                                <div class="d-grid gap-2">
                                    <a href="{{ route('user.add-ons.access', $addOn) }}" class="btn btn-primary">
                                        <i class="bx bx-lock-open me-1"></i> Access Content
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="bx bx-info-circle me-1"></i>
                                    This add-on is no longer active.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-package" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="mt-3">No Add-Ons Yet</h5>
                            <p class="text-muted mb-4">You haven't purchased any add-ons yet. Browse our collection to boost your career!</p>
                            <a href="{{ route('user.add-ons.index') }}" class="btn btn-primary">
                                <i class="bx bx-shopping-bag me-1"></i> Browse Add-Ons
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</x-layouts.app>