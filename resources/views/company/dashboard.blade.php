@extends('frontend.layouts.app')

@section('title', 'Company Dashboard - Post Jobs')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Company Dashboard</h1>
            <p class="text-muted mb-0">Post jobs and manage your packages.</p>
        </div>
        <div>
            <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-sm">Logout</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Post a Job</h5>
                    <p class="text-muted small">Fill out the details below to publish a job. Featured jobs are highlighted (+ IDR 300,000).</p>
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
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-semibold mb-3">Company Packages</h6>
                    @foreach($packages as $package)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $package['jobs'] }} jobs</div>
                                    <div class="text-muted small">{{ $package['name'] }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">IDR {{ number_format($package['price'], 0) }}</div>
                                    <button class="btn btn-outline-primary btn-sm mt-2" type="button" disabled>Contact sales</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-semibold mb-3">Optional Add-ons</h6>
                    @foreach($addons as $addon)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="fw-semibold">{{ $addon['name'] }}</div>
                                <div class="text-muted small">{{ $addon['description'] }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">IDR {{ number_format($addon['price'], 0) }}{{ isset($addon['period']) ? ' / ' . $addon['period'] : '' }}</div>
                                <button class="btn btn-outline-secondary btn-sm mt-1" type="button" disabled>Coming soon</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Your Jobs</h5>
            </div>
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
                                <th>Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->location }}</td>
                                    <td>{{ $job->type }}</td>
                                    <td>{{ $job->is_featured ? 'Yes' : 'No' }}</td>
                                    <td>{{ optional($job->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
