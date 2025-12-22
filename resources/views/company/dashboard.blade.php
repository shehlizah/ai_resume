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
                        <span class="badge bg-primary-soft">Jobs</span>
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

    @if($hasAiMatching && $aiMatches->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #2563EB 0%, #1e40af 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-white mb-1">
                        <i class="bx bx-sparkles me-2"></i>AI-Matched Candidates
                    </h5>
                    <p class="text-white-50 small mb-0">Top candidates automatically matched to your job postings</p>
                </div>
                <span class="badge bg-white text-primary">{{ $aiMatches->count() }} Matches</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Job</th>
                            <th>Match Score</th>
                            <th>Skills Match</th>
                            <th>Status</th>
                            <th>Matched</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aiMatches as $match)
                        <tr>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $match->candidate->name }}</div>
                                    <div class="text-muted small">{{ $match->candidate->email }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ Str::limit($match->job->title, 30) }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress" style="width: 60px; height: 8px;">
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
                                        @foreach(array_slice($match->match_details['matched_skills'], 0, 3) as $skill)
                                            <span class="badge bg-light text-dark small">{{ $skill }}</span>
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
                                <span class="badge bg-{{ $match->status === 'shortlisted' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($match->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $match->matched_at->diffForHumans() }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($match->resume)
                                        <a href="{{ Storage::url($match->resume->generated_pdf_path) }}"
                                           target="_blank"
                                           class="btn btn-outline-primary btn-sm"
                                           title="View Resume">
                                            <i class="bx bx-file"></i>
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $match->candidate->email }}"
                                       class="btn btn-outline-success btn-sm"
                                       title="Contact Candidate">
                                        <i class="bx bx-envelope"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($match->ai_summary ?? false)
                <div class="alert alert-info mb-0 mt-3">
                    <strong>AI Insight:</strong> {{ $match->ai_summary }}
                </div>
            @endif
        </div>
    </div>
    @elseif(!$hasAiMatching)
    <div class="card mb-4 border-primary">
        <div class="card-body text-center py-5">
            <i class="bx bx-sparkles text-primary" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">Unlock AI-Powered Candidate Matching</h5>
            <p class="text-muted mb-4">Automatically find and rank the best candidates for your jobs within 30 minutes of posting</p>
            <button class="btn btn-primary" onclick="purchaseAddon('ai-matching')">
                Get AI Matching Add-on
            </button>
        </div>
    </div>
    @endif

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
</x-layouts.app>
