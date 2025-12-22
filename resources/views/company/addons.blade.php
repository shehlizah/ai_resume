<x-layouts.app :title="__('Add-ons')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Premium Add-ons</h1>
            <p class="text-muted mb-0">Enhance your job postings with these optional features</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($addons as $addon)
            @php
                $isActive = in_array($addon['slug'], $activeAddons);
            @endphp
            <div class="col-lg-6">
                <div class="card h-100 {{ $isActive ? 'border-success border-2' : '' }}">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-2">
                                    @if($addon['slug'] === 'ai-matching')
                                        <i class="bx bx-sparkles text-primary me-2"></i>
                                    @elseif($addon['slug'] === 'featured')
                                        <i class="bx bx-star text-warning me-2"></i>
                                    @else
                                        <i class="bx bx-file text-info me-2"></i>
                                    @endif
                                    {{ $addon['name'] }}
                                </h5>
                                <p class="text-muted mb-0">{{ $addon['description'] }}</p>
                            </div>
                            @if($isActive)
                                <span class="badge bg-success">Active</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <h4 class="mb-0 text-primary">IDR {{ number_format($addon['price'], 0) }}</h4>
                                @if(isset($addon['period']))
                                    <small class="text-muted">/ {{ $addon['period'] }}</small>
                                @endif
                            </div>
                            @if($isActive)
                                @if($addon['slug'] === 'ai-matching')
                                    <a href="{{ route('company.ai-matching') }}" class="btn btn-outline-primary">
                                        View Matches
                                    </a>
                                @else
                                    <button class="btn btn-outline-success" disabled>
                                        <i class="bx bx-check me-1"></i>Purchased
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-primary" onclick="purchaseAddon('{{ $addon['slug'] }}')">
                                    Buy Now
                                </button>
                            @endif
                        </div>
                        
                        @if($addon['slug'] === 'ai-matching' && !$isActive)
                            <div class="alert alert-info mt-3 mb-0">
                                <small>
                                    <i class="bx bx-info-circle me-1"></i>
                                    Automatically matches qualified candidates within 30 minutes of posting a job
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function purchaseAddon(slug) {
            window.location.href = `/company/addons/${slug}/checkout`;
        }
    </script>
</x-layouts.app>
