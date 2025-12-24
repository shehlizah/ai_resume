<x-layouts.app :title="__('AI Matches for') . ' ' . $job->title">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('company.ai-matching') }}" class="text-muted text-decoration-none mb-2 d-inline-block">
                <i class="bx bx-arrow-back me-1"></i>Back to All Jobs
            </a>
            <h1 class="h4 mb-1">
                <i class="bx bx-sparkles text-primary me-2"></i>{{ $job->title }}
            </h1>
            <p class="text-muted mb-0">
                {{ $job->company }} • {{ $job->location }} • Posted {{ $job->created_at->diffForHumans() }}
            </p>
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
                        <span class="badge bg-primary-soft text-primary">Candidates</span>
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
                <i class="bx bx-time text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">Matching in Progress</h5>
                <p class="text-muted mb-4">AI is analyzing candidates for this job. Matches will appear within 30 minutes.</p>
                <a href="{{ route('company.ai-matching') }}" class="btn btn-outline-primary">Back to Jobs</a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Matched Candidates</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Match Score</th>
                                <th>Matched Skills</th>
                                <th>Experience</th>
                                <th>Status</th>
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
                                    <div class="d-flex align-items-center">
                                        <div class="progress" style="width: 100px; height: 12px;">
                                            <div class="progress-bar bg-{{ $match->score_color }}"
                                                 role="progressbar"
                                                 style="width: {{ $match->match_score }}%"
                                                 aria-valuenow="{{ $match->match_score }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-2 fw-bold text-{{ $match->score_color }}">{{ $match->match_score }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($match->match_details && isset($match->match_details['matched_skills']))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($match->match_details['matched_skills'], 0, 5) as $skill)
                                                <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $skill }}</span>
                                            @endforeach
                                            @if(count($match->match_details['matched_skills']) > 5)
                                                <span class="badge bg-light text-muted small">+{{ count($match->match_details['matched_skills']) - 5 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">No data</span>
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
                                    <span class="badge bg-{{ $match->status === 'shortlisted' ? 'success' : ($match->status === 'contacted' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($match->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $match->matched_at->diffForHumans() }}</span>
                                    <div class="text-muted small">{{ $match->matched_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($match->resume && $match->resume->generated_pdf_path)
                                            <a href="{{ Storage::url($match->resume->generated_pdf_path) }}"
                                               target="_blank"
                                               class="btn btn-outline-primary"
                                               title="View Resume">
                                                <i class="bx bx-file me-1"></i>Resume
                                            </a>
                                        @endif
                                        <a href="mailto:{{ $match->candidate->email }}?subject=Regarding {{ $job->title }} position"
                                           class="btn btn-success"
                                           title="Contact Candidate">
                                            <i class="bx bx-envelope me-1"></i>Contact
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
            <strong>Match Score:</strong> Based on skills (40%), keywords (30%), experience level (20%), and location (10%). Scores above 75% are automatically shortlisted.
        </div>
    @endif
</x-layouts.app>
