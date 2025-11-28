@section('title', __('Job Finder - Recommended Jobs'))
<x-layouts.app :title="__('Recommended Jobs')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">
                                <i class="bx bx-briefcase me-2"></i> Job Finder
                            </h4>
                            <p class="text-white mb-0 opacity-90">
                                Discover job opportunities matched to your resume and skills
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-light text-dark" id="jobsRemainingBadge">
                                <span id="jobsRemaining">5</span> views remaining today
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CV Selection -->
        @if($resumes->count() > 0)
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label class="form-label mb-2">
                        <i class="bx bx-file me-2"></i><strong>Select a CV for Job Matching</strong>
                    </label>
                    <select class="form-select" id="resumeSelect">
                        <option value="">-- Choose CV for better matching --</option>
                        @foreach($resumes as $resume)
                        <option value="{{ $resume->id }}">{{ $resume->title ?? 'Resume #' . $resume->id }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                        <i class="bx bx-info-circle me-1"></i> Select a CV for AI to find the most relevant job matches for you.
                    </small>
                </div>
            </div>
        </div>
        @endif

        <!-- Upgrade Banner (if free user) -->
        @if(!$hasPremiumAccess)
        <div class="col-lg-12">
            <div class="alert alert-warning border-0 shadow-sm mb-0 d-flex align-items-center" role="alert">
                <div class="flex-shrink-0 me-3">
                    <i class="bx bx-crown" style="font-size: 2rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <strong>Limited to 5 Job Views + 1 Apply per day</strong> on the free plan. Upgrade to <strong>Pro</strong> for unlimited job searches and applications.
                </div>
                <a href="{{ route('user.pricing') }}" class="btn btn-sm btn-warning">Upgrade to Pro</a>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Generate Jobs Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-sparkles me-1"></i> AI Recommended Jobs
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Our AI analyzes your resume to find the best matching opportunities
                    </p>
                    <button class="btn btn-primary btn-block w-100" id="generateJobsBtn" onclick="generateJobs()">
                        <i class="bx bx-search me-2"></i> Find Recommended Jobs
                    </button>
                </div>
            </div>

            <!-- Jobs List -->
            <div id="jobsContainer">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-briefcase mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mb-2">No jobs loaded yet</h6>
                        <p class="text-muted small">Click "Find Recommended Jobs" to see opportunities</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-info-circle me-1"></i> Your Job Search
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Views Today</small>
                            <strong id="viewsCount">0 / 5</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" id="viewsProgress" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Applications</small>
                            <strong id="appCount">0 / 1</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" id="appProgress" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ route('user.jobs.by-location') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bx bx-map me-1"></i> Search by Location
                    </a>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bx bx-lightbulb me-1"></i> Pro Tips
                    </h6>
                    <ul class="ps-3 small">
                        <li class="mb-2">Make sure your resume is up-to-date</li>
                        <li class="mb-2">Use keywords from job descriptions</li>
                        <li class="mb-2">Customize your cover letter for each application</li>
                        <li>Follow up within a week of applying</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
    function generateJobs() {
        const btn = document.getElementById('generateJobsBtn');
        const resumeId = document.getElementById('resumeSelect')?.value || null;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Loading...';

        fetch('{{ route("user.jobs.recommended") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                resume_id: resumeId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayJobs(data.jobs);
                updateProgress(data);
            } else {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                }
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-search me-2"></i> Find Recommended Jobs';
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-search me-2"></i> Find Recommended Jobs';
        });
    }

    function displayJobs(jobs) {
        let html = '';
        jobs.forEach(job => {
            html += `
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-1">${job.title}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="bx bx-building me-1"></i>${job.company}
                                    <i class="bx bx-map ms-3 me-1"></i>${job.location}
                                </p>
                                <p class="text-muted small mb-0">${job.description}</p>
                                <p class="text-success small mt-2">
                                    <i class="bx bx-dollar me-1"></i>${job.salary}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="mb-3">
                                    <div class="text-center">
                                        <div class="text-warning" style="font-size: 1.5rem; font-weight: bold;">${job.match_score}%</div>
                                        <small class="text-muted">Match Score</small>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm w-100" onclick="applyJob(${job.id})">
                                    <i class="bx bx-send me-1"></i> Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        document.getElementById('jobsContainer').innerHTML = html;
    }

    function applyJob(jobId) {
        if (confirm('Apply to this job?')) {
            fetch(`/jobs/${jobId}/apply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message);
                    }
                }
            });
        }
    }

    function updateProgress(data) {
        if (data.remaining_views !== 'unlimited') {
            document.getElementById('jobsRemaining').textContent = data.remaining_views;
            document.getElementById('viewsCount').textContent = (5 - data.remaining_views) + ' / 5';
            document.getElementById('viewsProgress').style.width = ((5 - data.remaining_views) / 5 * 100) + '%';
        } else {
            document.getElementById('jobsRemainingBadge').innerHTML = '<i class="bx bx-infinity me-1"></i> Unlimited';
        }
    }
    </script>
</x-layouts.app>
