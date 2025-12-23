<x-layouts.app :title="__('Job Details')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $job->title }}</h1>
            <p class="text-muted mb-0">{{ $job->company }} — {{ $job->location }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('company.jobs.applications', $job) }}" class="btn btn-primary">View Applicants</a>
            <a href="{{ route('company.jobs.index') }}" class="btn btn-light">Back to Jobs</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Job Information</h5>
            <p><strong>Type:</strong> {{ $job->type }}</p>
            <p><strong>Salary:</strong> {{ $job->salary ?? 'Not specified' }}</p>
            <p><strong>Posted:</strong> {{ optional($job->posted_at)->format('Y-m-d') }}</p>

            <h5 class="mt-4">Description</h5>
            <div class="mb-3">{!! nl2br(e($job->description)) !!}</div>

            @if(!empty($job->tags))
                <h6>Tags</h6>
                <div class="mb-3">
                    @foreach($job->getTagsArrayAttribute() ?? [] as $tag)
                        <span class="badge bg-secondary me-1">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
