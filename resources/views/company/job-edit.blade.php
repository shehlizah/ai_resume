<x-layouts.app :title="__('Edit Job')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Edit Job</h1>
            <p class="text-muted mb-0">Update your job posting details.</p>
        </div>
        <a href="{{ route('company.jobs.show', $job) }}" class="btn btn-light">Back to Job</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <form method="POST" action="{{ route('company.jobs.update', $job) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Job Title *</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company *</label>
                                <input type="text" name="company" class="form-control" value="{{ old('company', $job->company) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location *</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $job->location) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" value="{{ old('type', $job->type) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Salary</label>
                                <input type="text" name="salary" class="form-control" placeholder="e.g. IDR 15-20M" value="{{ old('salary', $job->salary) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Role, requirements, benefits">{{ old('description', $job->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tags (comma separated)</label>
                                <input type="text" name="tags" class="form-control" placeholder="e.g. product, fintech, remote" value="{{ old('tags', is_array($job->tags) ? implode(', ', $job->tags) : $job->tags) }}">
                            </div>
                            <div class="col-12 form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured', $job->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Make this a Featured job (+ IDR 300,000)
                                </label>
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('company.jobs.show', $job) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="text-uppercase text-muted fw-semibold mb-0">Job Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Posted</label>
                        <strong>{{ $job->created_at->format('F d, Y g:i A') }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block">Applications</label>
                        <strong>{{ $job->applications()->count() }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block">Status</label>
                        <span class="badge bg-{{ $job->is_active ? 'success' : 'danger' }}">
                            {{ $job->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('company.jobs.show', $job) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-eye me-1"></i>View Job
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
