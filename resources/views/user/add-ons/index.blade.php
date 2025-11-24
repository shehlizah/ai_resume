<x-layouts.app :title="'Browse Add-Ons'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="mb-4">
            <h4 class="fw-bold mb-2">🎁 Boost Your Career</h4>
            <p class="text-muted">Enhance your job search with our premium add-ons</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- My Add-Ons Link -->
        @if(auth()->user()->userAddOns()->count() > 0)
            <div class="alert alert-primary mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bx bx-check-circle me-2"></i>
                        You have {{ auth()->user()->userAddOns()->where('status', 'active')->count() }} active add-on(s)
                    </div>
                    <a href="{{ route('user.add-ons.my-add-ons') }}" class="btn btn-sm btn-primary">
                        View My Add-Ons
                    </a>
                </div>
            </div>
        @endif

        <!-- Add-Ons Grid -->
        <div class="row g-4">
            @forelse($addOns as $addOn)
                <div class="col-lg-6">
                    <div class="card h-100 {{ in_array($addOn->id, $purchasedAddOnIds) ? 'border-success' : '' }}">
                        <div class="card-body">
                            @if(in_array($addOn->id, $purchasedAddOnIds))
                                <div class="badge bg-success position-absolute top-0 end-0 m-3">
                                    <i class="bx bx-check"></i> Purchased
                                </div>
                            @endif

                            <div class="mb-3">
                                <i class="bx {{ $addOn->icon ?? 'bx-gift' }}" style="font-size: 3rem; color: #6366f1;"></i>
                            </div>

                            <h4 class="mb-2">{{ $addOn->name }}</h4>
                            <p class="text-muted mb-3">{{ $addOn->description }}</p>

                            <h3 class="text-primary mb-3">${{ number_format($addOn->price, 2) }}</h3>

                            @if($addOn->features)
                                <div class="mb-4">
                                    <strong class="d-block mb-2">What's included:</strong>
                                    <ul class="list-unstyled">
                                        @foreach(array_slice($addOn->features, 0, 5) as $feature)
                                            <li class="mb-2">
                                                <i class="bx bx-check text-success me-2"></i>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                        @if(count($addOn->features) > 5)
                                            <li class="text-muted">
                                                <small>+ {{ count($addOn->features) - 5 }} more features</small>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                @if(in_array($addOn->id, $purchasedAddOnIds))
                                    <a href="{{ route('user.add-ons.access', $addOn) }}" class="btn btn-success">
                                        <i class="bx bx-lock-open me-1"></i> Access Now
                                    </a>
                                @else
                                    <a href="{{ route('user.add-ons.show', $addOn) }}" class="btn btn-outline-primary">
                                        View Details
                                    </a>
                                    <a href="{{ route('user.add-ons.checkout', $addOn) }}" class="btn btn-primary">
                                        <i class="bx bx-cart me-1"></i> Purchase Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-package" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="mt-3">No Add-Ons Available</h5>
                            <p class="text-muted">Check back soon for exciting add-ons!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</x-layouts.app>