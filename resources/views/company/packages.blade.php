<x-layouts.app :title="__('Packages')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Job Posting Packages</h1>
            <p class="text-muted mb-0">Choose a package to post multiple jobs</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($packages as $package)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-2">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="bx bx-briefcase text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="mb-2">{{ $package['name'] }}</h4>
                        <h3 class="text-primary mb-3">IDR {{ number_format($package['price'], 0) }}</h3>
                        <div class="mb-4">
                            <div class="fw-semibold mb-2">{{ $package['jobs'] }} Job Postings</div>
                            <div class="text-muted small">Valid for 30 days</div>
                        </div>
                        <button class="btn btn-primary w-100" onclick="purchasePackage('{{ $package['slug'] }}')">
                            Buy with Stripe
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function purchasePackage(slug) {
            window.location.href = `/company/packages/${slug}/checkout`;
        }
    </script>
</x-layouts.app>
