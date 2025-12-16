<x-layouts.app :title="__('Company Dashboard')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Company Dashboard</h1>
            <p class="text-muted mb-0">Track jobs, applicants, and packages.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Jobs</p>
                            <h4 class="mb-0">{{ $stats['total_jobs'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-primary-soft text-primary">Jobs</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Featured Jobs</p>
                            <h4 class="mb-0">{{ $stats['featured_jobs'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-warning-soft text-warning">Featured</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Applications</p>
                            <h4 class="mb-0">{{ $stats['applications'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-success-soft text-success">Applicants</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap gap-2">
            <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">Post a Job</a>
            <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-primary">My Jobs</a>
            <a href="{{ route('company.applications.index') }}" class="btn btn-outline-secondary">Applicants</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card mb-3 h-100">
                <div class="card-header">
                    <h6 class="text-uppercase text-muted fw-semibold mb-0">Company Packages</h6>
                </div>
                <div class="card-body">
                    @foreach($packages as $package)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $package['jobs'] }} jobs</div>
                                    <div class="text-muted small">{{ $package['name'] }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">IDR {{ number_format($package['price'], 0) }}</div>
                                    <button class="btn btn-outline-primary btn-sm mt-2" type="button" onclick="purchasePackage('{{ $package['slug'] }}')">Buy with Stripe</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="text-uppercase text-muted fw-semibold mb-0">Optional Add-ons</h6>
                </div>
                <div class="card-body">
                    @foreach($addons as $addon)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="fw-semibold">{{ $addon['name'] }}</div>
                                <div class="text-muted small">{{ $addon['description'] }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">IDR {{ number_format($addon['price'], 0) }}{{ isset($addon['period']) ? ' / ' . $addon['period'] : '' }}</div>
                                <button class="btn btn-outline-primary btn-sm mt-1" type="button" onclick="purchaseAddon('{{ $addon['slug'] }}')">Buy with Stripe</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Your Jobs</h5>
        </div>
        <div class="card-body">
            @if($jobs->isEmpty())
                <p class="text-muted mb-0">No jobs posted yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Featured</th>
                                <th>Applicants</th>
                                <th>Posted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->location }}</td>
                                    <td>{{ $job->type }}</td>
                                    <td>{{ $job->is_featured ? 'Yes' : 'No' }}</td>
                                    <td>{{ $job->applications_count ?? 0 }}</td>
                                    <td>{{ optional($job->created_at)->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('company.jobs.applications', $job) }}" class="btn btn-sm btn-outline-primary">View Applicants</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

@section('page-script')
<script>
    function purchasePackage(slug) {
        // TODO: replace with real Stripe checkout route/price IDs
        window.location.href = `/company/packages/${slug}/checkout`;
    }

    function purchaseAddon(slug) {
        // TODO: replace with real Stripe checkout route/price IDs
        window.location.href = `/company/addons/${slug}/checkout`;
    }
</script>
@endsection
