<x-layouts.app :title="__('AI-Matched Candidates')">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bx bx-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bx bx-sparkles text-primary me-2"></i>AI Candidate Matching
            </h1>
            <p class="text-muted mb-0">View AI-matched candidates for each of your job postings</p>
        </div>
        <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Post New Job
        </a>
    </div>

    @if(!$hasAiMatching)
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bx bx-info-circle me-2"></i>
            <div>
                AI Matching is not active for your account. Purchase the add-on to view matches.
                <a href="{{ route('company.addons') }}" class="alert-link">Go to Add-ons</a>.
            </div>
        </div>
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
                        <span class="badge bg-primary-soft text-primary">Posted</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Matches</p>
                            <h4 class="mb-0">{{ $stats['total_matches'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-info-soft text-info">Candidates</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Shortlisted</p>
                            <h4 class="mb-0">{{ $stats['shortlisted'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-success-soft text-success">High Match</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($jobs->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-briefcase text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">No Jobs Posted Yet</h5>
                <p class="text-muted mb-4">Post your first job to start receiving AI-matched candidates automatically</p>
                <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Post Your First Job
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Jobs with AI Matching</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Posted</th>
                                <th>Total Matches</th>
                                <th>Shortlisted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $job->title }}</div>
                                    @if($job->tags && count($job->tags) > 0)
                                        <div class="mt-1">
                                            @foreach(array_slice($job->tags, 0, 3) as $tag)
                                                <span class="badge bg-light text-dark small me-1">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $job->company }}</td>
                                <td>
                                    <small class="text-muted">{{ $job->location }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($job->candidate_matches_count > 0)
                                        <span class="badge bg-info">{{ $job->candidate_matches_count }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($job->shortlisted_count > 0)
                                        <span class="badge bg-success">{{ $job->shortlisted_count }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$hasAiMatching)
                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            <i class="bx bx-lock-alt me-1"></i>Requires AI Matching
                                        </button>
                                    @elseif($job->candidate_matches_count > 0)
                                        <a href="{{ route('company.ai-matching.job', $job) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-search me-1"></i>View Matches
                                        </a>
                                    @else
                                        <form action="{{ route('company.ai-matching.trigger', $job) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" onclick="this.disabled=true; this.innerHTML='<i class=&quot;bx bx-loader-alt bx-spin me-1&quot;></i>Processing...'; this.form.submit();">
                                                <i class="bx bx-play me-1"></i>Start Matching
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <i class="bx bx-info-circle me-2"></i>
            <strong>How it works:</strong> Click "Start Matching" to generate AI candidate matches instantly and then "View Matches" to see scores and profiles.
        </div>
    @endif
</x-layouts.app>
