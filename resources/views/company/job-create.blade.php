<x-layouts.app :title="__('Post a Job')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Post a Job</h1>
            <p class="text-muted mb-0">Publish a new opening. Featured jobs are highlighted (+ IDR 300,000).</p>
        </div>
        <a href="{{ route('company.dashboard') }}" class="btn btn-light">Back to Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <form method="POST" action="{{ route('company.jobs.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Job Title *</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company *</label>
                                <input type="text" name="company" class="form-control" value="{{ old('company', auth()->user()->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location *</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" value="{{ old('type', 'Full Time') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Salary</label>
                                <input type="text" name="salary" class="form-control" placeholder="e.g. IDR 15-20M" value="{{ old('salary') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Role, requirements, benefits">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tags (comma separated)</label>
                                <input type="text" name="tags" class="form-control" placeholder="e.g. product, fintech, remote" value="{{ old('tags') }}">
                            </div>
                            <div class="col-12 form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Make this a Featured job (+ IDR 300,000)
                                </label>
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Publish Job</button>
                            <a href="{{ route('company.dashboard') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
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

            <div class="card">
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
</x-layouts.app>

@section('page-script')
<script>
    function purchasePackage(slug) {
        window.location.href = `/company/packages/${slug}/checkout`;
    }
    function purchaseAddon(slug) {
        window.location.href = `/company/addons/${slug}/checkout`;
    }
</script>
@endsection
