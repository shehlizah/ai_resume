<x-layouts.app :title="__('AI-Matched Candidates')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bx bx-sparkles text-primary me-2"></i>AI-Matched Candidates
            </h1>
            <p class="text-muted mb-0">Candidates automatically matched to your job postings based on skills and experience</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
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
        <div class="col-md-4">
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
    </div>

    @if($matches->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-search-alt text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">No Matches Yet</h5>
                <p class="text-muted mb-4">Post jobs to start receiving AI-matched candidates automatically</p>
                <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">Post a Job</a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Job</th>
                                <th>Match Score</th>
                                <th>Matched Skills</th>
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
                                    <div class="small">{{ Str::limit($match->job->title, 40) }}</div>
                                    <div class="text-muted small">{{ $match->job->company }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress" style="width: 80px; height: 10px;">
                                            <div class="progress-bar bg-{{ $match->score_color }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $match->match_score }}%"
                                                 aria-valuenow="{{ $match->match_score }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-2 fw-semibold text-{{ $match->score_color }}">{{ $match->match_score }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($match->match_details && isset($match->match_details['matched_skills']))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($match->match_details['matched_skills'], 0, 4) as $skill)
                                                <span class="badge bg-light text-dark small">{{ $skill }}</span>
                                            @endforeach
                                            @if(count($match->match_details['matched_skills']) > 4)
                                                <span class="badge bg-light text-muted small">+{{ count($match->match_details['matched_skills']) - 4 }}</span>
                                            @endif
                                        </div>
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
                                                <i class="bx bx-file"></i>
                                            </a>
                                        @endif
                                        <a href="mailto:{{ $match->candidate->email }}" 
                                           class="btn btn-outline-success"
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
    @endif
</x-layouts.app>
