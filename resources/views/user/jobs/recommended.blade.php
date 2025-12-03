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
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Tabs for selection method -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="cv-select-tab" data-bs-toggle="tab" data-bs-target="#cv-select" type="button" role="tab">
                                <i class="bx bx-file me-2"></i> My Resumes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cv-upload-tab" data-bs-toggle="tab" data-bs-target="#cv-upload" type="button" role="tab">
                                <i class="bx bx-cloud-upload me-2"></i> Upload Resume
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content">
                        <!-- Select from saved resumes -->
                        <div class="tab-pane fade show active" id="cv-select" role="tabpanel">
                            @if($resumes->count() > 0)
                            <label class="form-label mb-2">
                                <strong>Select a CV for Job Matching</strong>
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
                            @else
                            <div class="alert alert-info border-0">
                                <i class="bx bx-info-circle me-2"></i> You don't have any saved resumes yet. Upload one above to get started!
                            </div>
                            @endif
                        </div>

                        <!-- Upload new resume -->
                        <div class="tab-pane fade" id="cv-upload" role="tabpanel">
                            <label class="form-label mb-3">
                                <strong>Upload a Resume from Your Computer</strong>
                            </label>
                            <div class="drop-zone border-2 border-dashed rounded p-4 text-center" id="resumeDropZone" style="border-color: #667eea; cursor: pointer; transition: all 0.3s;">
                                <i class="bx bx-cloud-upload" style="font-size: 3rem; color: #667eea; margin-bottom: 1rem;"></i>
                                <div class="mb-2">
                                    <p class="mb-1"><strong>Drop your resume here or click to browse</strong></p>
                                    <small class="text-muted">Supported: PDF, DOCX (Max 10MB)</small>
                                </div>
                                <input type="file" id="resumeFileInput" accept=".pdf,.doc,.docx" style="display: none;">
                            </div>
                            <div id="uploadStatus" class="mt-3" style="display: none;">
                                <div class="alert alert-info border-0">
                                    <i class="bx bx-loader-alt bx-spin me-2"></i> <span id="statusText">Processing your resume...</span>
                                </div>
                            </div>
                            <div id="uploadSuccess" class="mt-3" style="display: none;">
                                <div class="alert alert-success border-0">
                                    <i class="bx bx-check-circle me-2"></i> Resume uploaded successfully!
                                    <span id="uploadedFileName"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            <small class="text-muted">Views This Session</small>
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
                    @if($user->has_lifetime_access)
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="resetSessionLimit()" id="resetBtn">
                        <i class="bx bx-reset me-1"></i> Reset Session Limit
                    </button>
                    @endif
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
    const jobsContainer = document.getElementById('jobsContainer');

    function resetSessionLimit() {
        const btn = document.getElementById('resetBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Resetting...';

        fetch('{{ route("user.jobs.reset-session") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset UI
                document.getElementById('viewsCount').textContent = '0 / 5';
                document.getElementById('viewsProgress').style.width = '0%';
                document.getElementById('appCount').textContent = '0 / 1';
                document.getElementById('appProgress').style.width = '0%';

                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-reset me-1"></i> Reset Session Limit';

                alert('✅ Session limit reset! You have 5 new job views.');
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-reset me-1"></i> Reset Session Limit';
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-reset me-1"></i> Reset Session Limit';
            alert('Error resetting session limit');
        });
    }

    function generateJobs(triggerSource = 'button') {
        const btn = document.getElementById('generateJobsBtn');
        const resumeId = document.getElementById('resumeSelect')?.value || null;
        const uploadedFile = sessionStorage.getItem('uploadedResumeFile');

        console.log('[generateJobs] Called with triggerSource:', triggerSource);
        console.log('[generateJobs] resumeId:', resumeId);
        console.log('[generateJobs] uploadedFile from sessionStorage:', uploadedFile);

        // Check if either a saved resume is selected OR a file was uploaded
        const hasResume = resumeId || uploadedFile;

        console.log('[generateJobs] hasResume:', hasResume, '(resumeId:', !!resumeId, 'uploadedFile:', !!uploadedFile + ')');

        if (!hasResume) {
            console.warn('[generateJobs] NO RESUME FOUND - showing error alert');
            alert('⚠️ Please select a resume or upload one to get personalized job recommendations');
            return;
        }

        console.log('[generateJobs] Resume found, proceeding...');

        if (btn) {
            btn.disabled = true;
            const label = triggerSource === 'upload'
                ? '<span class="spinner-border spinner-border-sm me-2"></span>Refreshing matches...'
                : '<span class="spinner-border spinner-border-sm me-2"></span> Loading...';
            btn.innerHTML = label;
        }

        showJobsLoadingState();

        const payload = {
            resume_id: resumeId,
            uploaded_file: uploadedFile
        };

        console.log('[generateJobs] Sending payload:', payload);

        fetch('{{ route("user.jobs.recommended") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log('[generateJobs] Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('[generateJobs] Response data:', data);
            if (data.success) {
                console.log('[generateJobs] Success! Jobs count:', data.jobs ? data.jobs.length : 0);
                displayJobs(data.jobs);
                updateProgress(data);
            } else {
                console.warn('[generateJobs] Error response:', data.message);
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('❌ ' + (data.message || 'Failed to generate jobs'));
                }
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-search me-2"></i> Find Recommended Jobs';
            }
        })
        .catch(error => {
            console.error('[generateJobs] Fetch error:', error);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-search me-2"></i> Find Recommended Jobs';
            }
            alert('❌ Network error: ' + error.message);
        });
    }

    function showJobsLoadingState() {
        if (!jobsContainer) {
            return;
        }

        jobsContainer.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-center py-5">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="mb-0">Analyzing your resume and finding fresh matches...</p>
                    </div>
                </div>
            </div>
        `;
    }

    function displayJobs(jobs) {
        if (!jobs || jobs.length === 0) {
            jobsContainer.innerHTML = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-error-circle mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mb-2">We couldn't find any matches yet</h6>
                        <p class="text-muted small">Try uploading a more detailed resume or pick a different saved resume.</p>
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
                                <button class="btn btn-primary btn-sm w-100" onclick="applyJob('${job.id}', '${job.apply_url}')">
                                    <i class="bx bx-send me-1"></i> Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        jobsContainer.innerHTML = html;
    }

    function applyJob(jobId, applyUrl) {
        // Track the application
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
                // Open the job application URL in a new tab
                window.open(applyUrl, '_blank');
            } else {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                }
            }
        });
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

    // Resume file upload handling
    const dropZone = document.getElementById('resumeDropZone');
    const fileInput = document.getElementById('resumeFileInput');
    const uploadStatus = document.getElementById('uploadStatus');
    const uploadSuccess = document.getElementById('uploadSuccess');

    if (dropZone && fileInput) {
        // Click to upload
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#764ba2';
            dropZone.style.backgroundColor = 'rgba(118, 75, 162, 0.05)';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#667eea';
            dropZone.style.backgroundColor = 'transparent';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#667eea';
            dropZone.style.backgroundColor = 'transparent';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileUpload(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });
    }

    function handleFileUpload(file) {
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

        console.log('File validation:', {
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

        uploadStatus.style.display = 'block';
        uploadSuccess.style.display = 'none';
        document.getElementById('statusText').textContent = 'Uploading ' + file.name + '...';

        console.log('Starting file upload:', file.name);

        fetch('{{ route("user.resumes.upload-temp") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('Upload response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Upload failed with status ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            uploadStatus.style.display = 'none';

            console.log('Upload complete. Response:', data);

            if (data.success && data.file_path) {
                uploadSuccess.style.display = 'block';
                document.getElementById('uploadedFileName').textContent = file.name;

                // Store the temporary file path/ID for job search
                const filePath = data.file_path;
                console.log('1. Upload succeeded, storing file path:', filePath);
                sessionStorage.setItem('uploadedResumeFile', filePath);

                // Verify it was stored
                const storedPath = sessionStorage.getItem('uploadedResumeFile');
                console.log('2. Verified stored path:', storedPath);

                // Clear file input
                fileInput.value = '';

                // Force a short delay to ensure storage is committed
                setTimeout(() => {
                    console.log('3. About to trigger generateJobs with file path:', sessionStorage.getItem('uploadedResumeFile'));
                    // Automatically refresh recommendations using the newly uploaded resume
                    generateJobs('upload');
                }, 100);
            } else {
                alert('❌ Upload failed or missing file_path in response: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            uploadStatus.style.display = 'none';
            alert('❌ Error uploading file: ' + error.message);
        });
    }
    </script>
</x-layouts.app>
