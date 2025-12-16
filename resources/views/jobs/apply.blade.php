<x-layouts.app :title="__('Apply to Job')">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h1 class="h4 mb-1">{{ $job->title }}</h1>
                    <p class="mb-0 text-muted">{{ $job->company }} — {{ $job->location }}</p>
                    <p class="text-muted">Type: {{ $job->type }} @if($job->is_featured)<span class="badge bg-warning text-dark ms-2">Featured</span>@endif</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('jobs.apply.store', $job) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="applicant_name" class="form-control" value="{{ old('applicant_name', auth()->user()->name ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="applicant_email" class="form-control" value="{{ old('applicant_email', auth()->user()->email ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="applicant_phone" class="form-control" value="{{ old('applicant_phone') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resume URL</label>
                            <input type="url" name="resume_url" class="form-control" placeholder="https://" value="{{ old('resume_url') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cover Letter</label>
                            <textarea name="cover_letter" class="form-control" rows="4" placeholder="Optional cover letter">{{ old('cover_letter') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
