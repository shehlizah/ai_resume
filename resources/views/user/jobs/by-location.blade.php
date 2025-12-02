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
                        @if($resumes->count() > 0)
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">
                                    <i class="bx bx-file me-2"></i> <strong>Select a CV (Optional)</strong>
                                </label>
                                <select class="form-select" id="resumeId" name="resume_id">
                                    <option value="">-- Choose CV for better matching --</option>
                                    @foreach($resumes as $resume)
                                    <option value="{{ $resume->id }}">{{ $resume->title ?? 'Resume #' . $resume->id }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-info-circle me-1"></i> Select a CV for AI to find more relevant matches.
                                </small>
                            </div>
                        </div>
                        @endif

                        <!-- OR Upload Resume -->
                        <div class="row g-3 mt-1">
                            <div class="col-md-12">
                                <p class="text-muted small mb-2">Or upload a resume from your computer:</p>
                                <div class="drop-zone border-2 border-dashed rounded p-3 text-center" id="locationResumeDropZone" style="border-color: #667eea; cursor: pointer; transition: all 0.3s;">
                                    <i class="bx bx-cloud-upload" style="font-size: 2rem; color: #667eea;"></i>
                                    <p class="mb-1 small"><strong>Drop resume here or click</strong></p>
                                    <small class="text-muted">PDF, DOCX (Max 10MB)</small>
                                    <input type="file" id="locationResumeInput" accept=".pdf,.doc,.docx" style="display: none;">
                                </div>
                                <div id="locationUploadStatus" class="mt-2" style="display: none;">
                                    <div class="alert alert-info border-0 mb-0 py-2">
                                        <small><i class="bx bx-loader-alt bx-spin me-2"></i> <span id="locationStatusText">Processing resume...</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(!$hasPremiumAccess)
                    <div class="alert alert-warning border-0 mb-0 mt-3">
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
    const jobsList = document.getElementById('jobsList');
    const resultsContainer = document.getElementById('resultsContainer');
    const emptyState = document.getElementById('emptyState');

    function searchJobs(event, triggerSource = 'button') {
        if (event) {
            event.preventDefault();
        }

        const location = document.getElementById('location').value;
        const jobTitle = document.getElementById('jobTitle').value;
        const resumeId = document.getElementById('resumeId')?.value || null;
        const uploadedFile = sessionStorage.getItem('locationUploadedResumeFile');

        console.log('searchJobs called', {
            triggerSource,
            location,
            jobTitle,
            resumeId,
            uploadedFile
        });

        if (!location || !jobTitle) {
            alert('Please enter both job title and location');
            return;
        }

        showSearchLoadingState();

        const payload = {
            location: location,
            job_title: jobTitle,
            resume_id: resumeId,
            uploaded_file: uploadedFile
        };

        console.log('Sending by-location payload:', payload);

        fetch('{{ route("user.jobs.by-location") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log('By-location response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('By-location response data:', data);
            if (data.success) {
                displayResults(data.jobs);
                emptyState.style.display = 'none';
                resultsContainer.style.display = 'block';
            } else {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('❌ ' + (data.message || 'Failed to search jobs'));
                }
            }
        })
        .catch(error => {
            console.error('By-location fetch error:', error);
            alert('❌ Network error: ' + error.message);
        });
    }

    function showSearchLoadingState() {
        if (!jobsList) {
            return;
        }

        jobsList.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-center py-5">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="text-muted">Analyzing your resume with AI...</h5>
                        <p class="mb-0 text-muted">Finding the best job matches for you</p>
                    </div>
                </div>
            </div>
        `;
    }

    function displayResults(jobs) {
        if (!jobs || jobs.length === 0) {
            jobsList.innerHTML = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-error-circle mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mb-2">No jobs found</h6>
                        <p class="text-muted small">Try adjusting your search criteria or uploading a resume for better matches.</p>
                    </div>
                </div>
            `;
            return;
        }

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
                                <button class="btn btn-primary btn-sm w-100" onclick="window.open('${job.apply_url || '#'}', '_blank')">
                                    <i class="bx bx-send me-1"></i> Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        jobsList.innerHTML = html;
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

    // Location Resume file upload handling
    const locationDropZone = document.getElementById('locationResumeDropZone');
    const locationFileInput = document.getElementById('locationResumeInput');
    const locationUploadStatus = document.getElementById('locationUploadStatus');

    if (locationDropZone && locationFileInput) {
        // Click to upload
        locationDropZone.addEventListener('click', () => locationFileInput.click());

        // Drag and drop
        locationDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            locationDropZone.style.borderColor = '#764ba2';
            locationDropZone.style.backgroundColor = 'rgba(118, 75, 162, 0.05)';
        });

        locationDropZone.addEventListener('dragleave', () => {
            locationDropZone.style.borderColor = '#667eea';
            locationDropZone.style.backgroundColor = 'transparent';
        });

        locationDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            locationDropZone.style.borderColor = '#667eea';
            locationDropZone.style.backgroundColor = 'transparent';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleLocationResumeUpload(files[0]);
            }
        });

        // File input change
        locationFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleLocationResumeUpload(e.target.files[0]);
            }
        });
    }

    function handleLocationResumeUpload(file) {
        // Validate file - check both MIME type and extension
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedExtensions = ['.pdf', '.doc', '.docx'];
        const allowedMimeTypes = ['application/pdf', 'application/msword', 'application/x-msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        // Get file extension
        const fileName = file.name.toLowerCase();
        const fileExtension = fileName.substring(fileName.lastIndexOf('.'));

        // Check both extension and MIME type (either can pass)
        const hasValidExtension = allowedExtensions.includes(fileExtension);
        const hasValidMimeType = allowedMimeTypes.includes(file.type);

        console.log('By-location file validation:', {
            name: file.name,
            extension: fileExtension,
            mimeType: file.type,
            hasValidExtension,
            hasValidMimeType
        });

        if (!hasValidExtension && !hasValidMimeType) {
            alert('❌ Please upload a PDF or DOCX file (detected: ' + fileExtension + ', type: ' + file.type + ')');
            return;
        }

        if (file.size > maxSize) {
            alert('❌ File size must be less than 10MB');
            return;
        }

        // Upload file
        const formData = new FormData();
        formData.append('resume_file', file);

        locationUploadStatus.style.display = 'block';
        document.getElementById('locationStatusText').textContent = 'Uploading ' + file.name + '...';

        console.log('Starting by-location file upload:', file.name);

        fetch('{{ route("user.resumes.upload-temp") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('By-location upload response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Upload failed with status ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('By-location upload response:', data);

            if (data.success) {
                // Store the temporary file path/ID
                const filePath = data.file_path;
                console.log('Storing by-location file path:', filePath);
                sessionStorage.setItem('locationUploadedResumeFile', filePath);

                // Update status text
                document.getElementById('locationStatusText').innerHTML = '<i class="bx bx-check-circle me-2"></i> ' + file.name + ' uploaded!';

                // Show upload success alert
                alert('✅ Resume uploaded successfully! Now analyzing with AI...');

                // Clear file input
                locationFileInput.value = '';

                // Auto-trigger search if form is filled
                const location = document.getElementById('location').value;
                const jobTitle = document.getElementById('jobTitle').value;

                console.log('Form state:', { location, jobTitle });

                if (location && jobTitle) {
                    // Auto-search after brief delay
                    setTimeout(() => {
                        locationUploadStatus.style.display = 'none';
                        searchJobs(null, 'upload');
                    }, 800);
                } else {
                    // Just hide status if form incomplete
                    setTimeout(() => {
                        locationUploadStatus.style.display = 'none';
                    }, 3000);
                }
            } else {
                alert('❌ Error: ' + (data.message || 'Unknown error'));
                locationUploadStatus.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            locationUploadStatus.style.display = 'none';
            alert('❌ Error uploading file: ' + error.message);
        });
    }
    </script>
</x-layouts.app>
