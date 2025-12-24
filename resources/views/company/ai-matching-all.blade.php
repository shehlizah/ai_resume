<x-layouts.app :title="__('Shortlisted Candidates')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('company.ai-matching') }}" class="text-muted text-decoration-none mb-2 d-inline-block">
                <i class="bx bx-arrow-back me-1"></i>Back to AI Matching
            </a>
            <h1 class="h4 mb-1">
                <i class="bx bx-sparkles text-primary me-2"></i>Shortlisted Candidates
            </h1>
            <p class="text-muted mb-0">All AI-matched candidates with high match scores across your jobs</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Matches</p>
                            <h4 class="mb-0">{{ $stats['total_matches'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-primary-soft text-primary">All</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
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
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Contacted</p>
                            <h4 class="mb-0">{{ $stats['contacted'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-info-soft text-info">Reached Out</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pending Review</p>
                            <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                        </div>
                        <span class="badge bg-warning-soft text-warning">Needs Review</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($matches->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-user-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">No Shortlisted Candidates Yet</h5>
                <p class="text-muted mb-4">AI is analyzing candidates for your jobs. Shortlisted matches will appear here within 30 minutes of posting a job.</p>
                <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">Post a Job</a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Shortlisted Candidates</h5>
                <div class="text-muted small">Showing {{ $matches->count() }} of {{ $matches->total() }}</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Job Position</th>
                                <th>Match Score</th>
                                <th>Matched Skills</th>
                                <th>Experience</th>
                                <th>Matched On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($matches as $match)
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $match->candidate->name }}</div>
                                        <div class="text-muted small">{{ $match->candidate->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $match->job->title }}</div>
                                    <div class="text-muted small">{{ $match->job->company }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress" style="width: 80px; height: 10px;">
                                            <div class="progress-bar bg-success"
                                                 role="progressbar"
                                                 style="width: {{ $match->match_score }}%"
                                                 aria-valuenow="{{ $match->match_score }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-2 fw-bold text-success">{{ $match->match_score }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($match->match_details && isset($match->match_details['matched_skills']))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($match->match_details['matched_skills'], 0, 3) as $skill)
                                                <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $skill }}</span>
                                            @endforeach
                                            @if(count($match->match_details['matched_skills']) > 3)
                                                <span class="badge bg-light text-muted small">+{{ count($match->match_details['matched_skills']) - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($match->match_details && isset($match->match_details['total_experience_entries']))
                                        <span class="badge bg-light text-dark">
                                            {{ $match->match_details['total_experience_entries'] }}
                                            {{ Str::plural('role', $match->match_details['total_experience_entries']) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $match->matched_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($match->resume)
                                            <a href="{{ route('company.ai-matching.candidate.resume', [$match->job, $match]) }}"
                                               target="_blank"
                                               class="btn btn-outline-primary"
                                               title="View Resume">
                                                <i class="bx bx-file"></i>
                                            </a>
                                        @endif
                                        <a href="mailto:{{ $match->candidate->email }}?subject=Regarding {{ $match->job->title }} position"
                                           class="btn btn-success"
                                           title="Contact Candidate">
                                            <i class="bx bx-envelope"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @if($match->ai_summary)
                            <tr>
                                <td colspan="7" class="bg-light">
                                    <div class="ps-3 py-2">
                                        <i class="bx bx-brain text-primary me-2"></i>
                                        <strong class="text-primary">AI Insight:</strong> {{ $match->ai_summary }}
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $matches->links() }}
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <i class="bx bx-info-circle me-2"></i>
            <strong>Match Score:</strong> Candidates with 75%+ match scores are automatically shortlisted. Scores are based on skills (40%), keywords (30%), experience level (20%), and location (10%).
        </div>
    @endif
</x-layouts.app>
