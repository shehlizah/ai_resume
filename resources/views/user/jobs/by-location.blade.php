@section('title', __('Job Finder - Search by Location'))
<x-layouts.app :title="__('Search Jobs by Location')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">
                                <i class="bx bx-map me-2"></i> Search by Location
                            </h4>
                            <p class="text-white mb-0 opacity-90">
                                Find jobs in your preferred location
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="searchForm" onsubmit="searchJobs(event)">
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Job Title</label>
                                <input type="text" class="form-control" id="jobTitle" name="job_title"
                                       placeholder="e.g. Web Developer, Project Manager" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       placeholder="e.g. New York, NY or Remote" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-search me-2"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(!$hasPremiumAccess)
                    <div class="alert alert-warning border-0 mb-0">
                        <i class="bx bx-crown me-2"></i> Free plan: 5 job views per session.
                        <a href="{{ route('user.pricing') }}">Upgrade to Pro</a> for unlimited searches.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Results -->
            <div id="resultsContainer" class="mt-4" style="display: none;">
                <div id="jobsList"></div>
            </div>

            <div id="emptyState" class="mt-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-map mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mb-2">Search for jobs by location</h6>
                        <p class="text-muted small">Enter a job title and location to find opportunities</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function searchJobs(event) {
        event.preventDefault();

        const location = document.getElementById('location').value;
        const jobTitle = document.getElementById('jobTitle').value;
        const resultsContainer = document.getElementById('resultsContainer');
        const emptyState = document.getElementById('emptyState');

        fetch('{{ route("user.jobs.by-location") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                location: location,
                job_title: jobTitle
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.jobs);
                emptyState.style.display = 'none';
                resultsContainer.style.display = 'block';
            } else {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching jobs. Please try again.');
        });
    }

    function displayResults(jobs) {
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
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button class="btn btn-primary btn-sm w-100" onclick="applyJob('${job.id}')">
                                    <i class="bx bx-send me-1"></i> Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        document.getElementById('jobsList').innerHTML = html;
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
                    }
                }
            });
        }
    }
    </script>
</x-layouts.app>
