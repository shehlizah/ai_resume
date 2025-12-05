@section('title', __('AI Interview Prep'))

<x-layouts.app>
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Header -->
        <div class="card bg-gradient-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bx bx-brain" style="font-size: 4rem;"></i>
                    <div class="ms-4">
                        <h3 class="text-white mb-2">🎯 AI-Powered Interview Preparation</h3>
                        <p class="mb-0 opacity-75">Upload your resume and get personalized interview questions based on your experience</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upload Section -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">
                            <i class="bx bx-upload me-2"></i>
                            Upload Resume
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Saved Resumes -->
                        @if($resumes->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Select Saved Resume</label>
                            <select class="form-select" id="resumeSelect">
                                <option value="">Choose a resume...</option>
                                @foreach($resumes as $resume)
                                    <option value="{{ $resume->id }}">{{ $resume->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center my-3">
                            <span class="badge bg-secondary">OR</span>
                        </div>
                        @endif

                        <!-- Upload New Resume -->
                        <div class="mb-3">
                            <label class="form-label">Upload New Resume</label>
                            <div class="drop-zone border-2 border-dashed rounded p-3 text-center" id="resumeDropZone" style="border-color: #667eea; cursor: pointer;">
                                <i class="bx bx-cloud-upload" style="font-size: 2rem; color: #667eea;"></i>
                                <p class="mb-1 small"><strong>Drop resume here or click</strong></p>
                                <small class="text-muted">PDF, DOCX (Max 10MB)</small>
                                <input type="file" id="resumeFileInput" accept=".pdf,.doc,.docx" style="display: none;">
                            </div>
                            <div id="uploadStatus" class="mt-2" style="display: none;">
                                <div class="alert alert-info border-0 mb-0 py-2">
                                    <small><i class="bx bx-loader-alt bx-spin me-2"></i> <span id="statusText">Processing...</span></small>
                                </div>
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="mb-3">
                            <label class="form-label">Job Title/Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jobTitle" placeholder="e.g., Software Engineer">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Experience Level</label>
                            <select class="form-select" id="experienceLevel">
                                <option value="entry">Entry Level (0-2 years)</option>
                                <option value="mid" selected>Mid Level (3-5 years)</option>
                                <option value="senior">Senior Level (6-10 years)</option>
                                <option value="executive">Executive Level (10+ years)</option>
                            </select>
                        </div>

                        <button class="btn btn-primary btn-lg w-100" id="generateBtn">
                            <span id="btnText">
                                <i class="bx bx-sparkles me-1"></i> Generate Interview Prep
                            </span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Analyzing...
                            </span>
                        </button>

                        @if(!$hasPremiumAccess)
                        <div class="alert alert-warning border-0 mt-3 mb-0">
                            <i class="bx bx-crown me-2"></i>
                            <strong>Free Plan:</strong> Basic interview questions
                            <br><a href="{{ route('user.pricing') }}" class="alert-link">Upgrade to Pro</a> for AI scoring, feedback & expert sessions
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="col-lg-8">
                <!-- Plan Features Info -->
                <div class="card mb-4 border-{{ $hasPremiumAccess ? 'success' : 'warning' }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-{{ $hasPremiumAccess ? 'muted' : 'primary' }}">
                                    <i class="bx bx-gift me-1"></i> Free Plan Features
                                </h6>
                                <ul class="small mb-0">
                                    <li>Basic interview questions (5-8 questions)</li>
                                    <li>Resume-based question generation</li>
                                    <li>General interview tips</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-{{ $hasPremiumAccess ? 'success' : 'muted' }}">
                                    <i class="bx {{ $hasPremiumAccess ? 'bx-check-shield' : 'bx-lock' }} me-1"></i> Pro Plan Features
                                </h6>
                                <ul class="small mb-0">
                                    <li>Advanced questions (20-25 questions)</li>
                                    <li>AI interview practice with scoring</li>
                                    <li>Real-time AI feedback & suggestions</li>
                                    <li>Book human expert interview sessions</li>
                                    <li>Technical topic preparation</li>
                                    <li>Salary negotiation tips</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-user-voice" style="font-size: 5rem; opacity: 0.3;"></i>
                        <h4 class="mt-3">Ready to Ace Your Interview?</h4>
                        <p class="text-muted mb-0">
                            Upload your resume and tell us about the role to get started
                        </p>
                    </div>
                </div>

                <!-- Results Container -->
                <div id="resultsContainer" style="display: none;">
                    <!-- Questions Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-question-mark me-2"></i>
                                Interview Questions
                            </h5>
                        </div>
                        <div class="card-body" id="questionsList">
                            <!-- Populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Pro Features -->
                    @if($hasPremiumAccess)
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-microphone" style="font-size: 3rem; color: #28a745;"></i>
                                    <h6 class="mt-3">AI Practice</h6>
                                    <p class="small text-muted">Practice with AI scoring</p>
                                    <a href="{{ route('user.interview.ai-practice') }}" class="btn btn-success btn-sm">
                                        Start Practice
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-chart" style="font-size: 3rem; color: #17a2b8;"></i>
                                    <h6 class="mt-3">AI Feedback</h6>
                                    <p class="small text-muted">Get detailed scoring</p>
                                    <a href="{{ route('user.interview.ai-practice') }}" class="btn btn-info btn-sm">
                                        View Results
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-calendar" style="font-size: 3rem; color: #ffc107;"></i>
                                    <h6 class="mt-3">Expert Session</h6>
                                    <p class="small text-muted">Book human interview</p>
                                    <a href="{{ route('user.interview.expert') }}" class="btn btn-warning btn-sm">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Sections (Pro Only) -->
                    <div id="proSections" class="{{ $hasPremiumAccess ? '' : 'd-none' }}">
                        <div id="technicalSection" class="card mb-4" style="display: none;">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0 text-white">
                                    <i class="bx bx-code-alt me-2"></i>
                                    Technical Topics
                                </h5>
                            </div>
                            <div class="card-body" id="technicalContent"></div>
                        </div>

                        <div id="salarySection" class="card mb-4" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0 text-white">
                                    <i class="bx bx-dollar-circle me-2"></i>
                                    Salary Negotiation Tips
                                </h5>
                            </div>
                            <div class="card-body" id="salaryContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .question-card {
            border-left: 4px solid #667eea;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .answer-box {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.75rem;
            border: 1px solid #e3e6f0;
        }
    </style>

    <script>
        const hasPremiumAccess = @json($hasPremiumAccess);
        let uploadedResumeFile = null;

        // File upload handling
        const dropZone = document.getElementById('resumeDropZone');
        const fileInput = document.getElementById('resumeFileInput');
        const uploadStatus = document.getElementById('uploadStatus');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#4CAF50';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#667eea';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#667eea';
            const file = e.dataTransfer.files[0];
            if (file) handleFileUpload(file);
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) handleFileUpload(file);
        });

        function handleFileUpload(file) {
            const formData = new FormData();
            formData.append('resume_file', file);

            uploadStatus.style.display = 'block';
            document.getElementById('statusText').textContent = 'Uploading ' + file.name + '...';

            fetch('{{ route("user.resumes.upload-temp") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadedResumeFile = data.file_path;
                    document.getElementById('statusText').innerHTML = '<i class="bx bx-check-circle me-2"></i>' + file.name + ' uploaded!';
                    setTimeout(() => {
                        uploadStatus.style.display = 'none';
                    }, 2000);
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                    uploadStatus.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed');
                uploadStatus.style.display = 'none';
            });
        }

        // Generate interview prep
        document.getElementById('generateBtn').addEventListener('click', function() {
            const resumeId = document.getElementById('resumeSelect')?.value || '';
            const jobTitle = document.getElementById('jobTitle').value.trim();
            const experienceLevel = document.getElementById('experienceLevel').value;

            console.log('Generate button clicked', {
                resumeId,
                uploadedResumeFile,
                jobTitle,
                experienceLevel
            });

            if (!jobTitle) {
                alert('Please enter a job title');
                return;
            }

            // Check if either a saved resume is selected OR a file was uploaded
            if (!resumeId && !uploadedResumeFile) {
                alert('Please select a saved resume or upload a new one');
                return;
            }

            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            const generateBtn = document.getElementById('generateBtn');

            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            generateBtn.disabled = true;

            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('resultsContainer').style.display = 'none';

            const payload = {
                resume_id: resumeId || null,
                uploaded_file: uploadedResumeFile || null,
                job_title: jobTitle,
                experience_level: experienceLevel
            };

            fetch('{{ route("user.interview.generate-prep") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data.data);
                    document.getElementById('resultsContainer').style.display = 'block';
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate interview prep'));
                    document.getElementById('emptyState').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
                document.getElementById('emptyState').style.display = 'block';
            })
            .finally(() => {
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                generateBtn.disabled = false;
            });
        });

        function displayResults(data) {
            const questionsList = document.getElementById('questionsList');

            if (data.questions && data.questions.length > 0) {
                let html = '';
                data.questions.forEach((q, index) => {
                    html += `
                        <div class="question-card">
                            <h6>
                                <span class="badge bg-primary me-2">${index + 1}</span>
                                ${escapeHtml(q.question)}
                            </h6>
                            ${q.sample_answer ? `
                                <div class="answer-box">
                                    <strong class="text-success d-block mb-2">
                                        <i class="bx bx-bulb me-1"></i>Sample Answer:
                                    </strong>
                                    <p class="mb-0">${escapeHtml(q.sample_answer)}</p>
                                </div>
                            ` : ''}
                            ${q.tips && q.tips.length > 0 ? `
                                <div class="mt-2">
                                    <strong class="small text-muted">Tips:</strong>
                                    <ul class="small mb-0">
                                        ${q.tips.map(tip => `<li>${escapeHtml(tip)}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                questionsList.innerHTML = html;
            }

            // Pro features
            if (hasPremiumAccess) {
                if (data.technical_topics) {
                    document.getElementById('technicalSection').style.display = 'block';
                    document.getElementById('technicalContent').innerHTML = formatText(data.technical_topics);
                }

                if (data.salary_tips) {
                    document.getElementById('salarySection').style.display = 'block';
                    document.getElementById('salaryContent').innerHTML = formatText(data.salary_tips);
                }
            }
        }

        function formatText(text) {
            return text.replace(/\n/g, '<br>');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Add visual feedback when resume is selected from dropdown
        const resumeSelectElement = document.getElementById('resumeSelect');
        if (resumeSelectElement) {
            resumeSelectElement.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    // Clear any uploaded file since user chose saved resume
                    uploadedResumeFile = null;

                    // Show success feedback
                    const uploadStatus = document.getElementById('uploadStatus');
                    const statusText = document.getElementById('statusText');
                    if (uploadStatus && statusText) {
                        uploadStatus.style.display = 'block';
                        statusText.innerHTML = '<i class="bx bx-check-circle me-2"></i> Selected: ' + selectedOption.text;
                        uploadStatus.querySelector('.alert').classList.remove('alert-info');
                        uploadStatus.querySelector('.alert').classList.add('alert-success');

                        setTimeout(() => {
                            uploadStatus.style.display = 'none';
                            uploadStatus.querySelector('.alert').classList.remove('alert-success');
                            uploadStatus.querySelector('.alert').classList.add('alert-info');
                        }, 2000);
                    }

                    console.log('Resume selected from dropdown:', this.value, selectedOption.text);
                }
            });
        }
    </script>
</x-layouts.app>
