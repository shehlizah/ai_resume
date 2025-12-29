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

        <div class="row g-3">
            <!-- Upload Section -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                <i class="bx bx-upload"></i>
                            </div>
                            <div class="ms-2">
                                <div class="text-muted small fw-semibold">Step 1</div>
                                <h6 class="mb-0">Upload or choose a resume</h6>
                            </div>
                        </div>
                        <!-- Saved Resumes -->
                        @if($resumes->count() > 0)
                        <div class="mb-2">
                            <label class="form-label mb-1">Use an existing resume</label>
                            <select class="form-select" id="resumeSelect" style="height:42px;">
                                <option value="">Choose a resume...</option>
                                @foreach($resumes as $resume)
                                    <option value="{{ $resume->id }}">{{ $resume->display_name }}</option>
                                @endforeach
                            </select>
                            <div id="resumeStatusIndicator" class="alert alert-success border-0 mt-2 py-2 px-3" style="display: none;">
                                <small><i class="bx bx-check-circle me-1"></i> <span id="resumeStatusText">Resume selected</span></small>
                            </div>
                        </div>
                        <div class="text-center my-2">
                            <span class="badge bg-light text-muted border">OR</span>
                        </div>
                        @endif

                        <!-- Upload New Resume -->
                        <div class="mb-2">
                            <label class="form-label mb-1">Upload a resume</label>
                            <div class="drop-zone border border-dashed rounded p-2 text-center" id="resumeDropZone" style="border-color: #d1d5db; cursor: pointer; min-height: 90px; background:#fafbff;">
                                <i class="bx bx-cloud-upload" style="font-size: 1.6rem; color: #667eea;"></i>
                                <p class="mb-0 small">Drop resume here or click to upload</p>
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
                        <div class="mb-2">
                            <label class="form-label mb-1">Job Title/Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jobTitle" placeholder="e.g., Software Engineer" style="height: 42px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label mb-1">Experience Level</label>
                            <select class="form-select" id="experienceLevel" style="height: 42px;">
                                <option value="entry">Entry Level (0-2 years)</option>
                                <option value="mid" selected>Mid Level (3-5 years)</option>
                                <option value="senior">Senior Level (6-10 years)</option>
                                <option value="executive">Executive Level (10+ years)</option>
                            </select>
                        </div>

                        <div class="mb-1">
                            <button class="btn btn-primary w-100" id="generateBtn" disabled style="height: 46px; font-weight: 700; box-shadow: 0 10px 20px rgba(102,126,234,0.25);">
                                <span id="btnText">
                                    <i class="bx bx-sparkles me-1"></i> Generate Interview Prep
                                </span>
                                <span id="btnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Analyzing...
                                </span>
                            </button>
                            <small class="text-muted d-block mt-1">AI will generate tailored interview questions from your resume.</small>
                            <div id="generateSuccess" class="alert alert-success border-0 py-2 px-3 mt-2 d-none">
                                <small><i class="bx bx-check-circle me-1"></i> Interview prep generated.</small>
                            </div>
                        </div>

                        @if(!$hasPremiumAccess)
                        <div class="alert alert-warning border mt-3 mb-0 py-2 px-3" style="background:#fffaf1;">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-warning text-dark me-2">Pro</span>
                                <strong class="small mb-0">AI scoring & feedback</strong>
                            </div>
                            <div class="small text-muted">Upgrade to unlock scoring, feedback, and expert review.</div>
                            <a href="{{ route('user.pricing') }}" class="alert-link">Upgrade to Pro</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="col-lg-6">
                <!-- Guidance / Preview Panel -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                                <i class="bx bx-bulb"></i>
                            </div>
                            <div class="ms-2">
                                <h6 class="mb-0">What you get when you generate</h6>
                                <small class="text-muted">Preview of your AI prep output</small>
                            </div>
                        </div>
                        <ul class="list-unstyled small mb-0">
                            <li class="d-flex align-items-start mb-1"><i class="bx bx-question-mark text-primary me-2"></i><span>5–8 tailored interview questions</span></li>
                            <li class="d-flex align-items-start mb-1"><i class="bx bx-target-lock text-primary me-2"></i><span>Role-specific and level-aware prompts</span></li>
                            <li class="d-flex align-items-start mb-1"><i class="bx bx-bulb text-primary me-2"></i><span>Tips on how to answer each question</span></li>
                            <li class="d-flex align-items-start"><i class="bx bx-trending-up text-primary me-2"></i><span>Next steps: practice or get feedback</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Plan Features Info -->
                <div class="card mb-3 border-{{ $hasPremiumAccess ? 'success' : 'warning' }} shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-{{ $hasPremiumAccess ? 'success' : 'warning' }} text-dark me-2">{{ $hasPremiumAccess ? 'Pro Active' : 'Free vs Pro' }}</span>
                            <strong class="small mb-0">{{ $hasPremiumAccess ? 'You have Pro benefits' : 'Unlock more with Pro' }}</strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-1"><i class="bx bx-gift me-1"></i> Free</h6>
                                <ul class="small mb-0">
                                    <li>5–8 tailored questions</li>
                                    <li>Basic tips per question</li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-{{ $hasPremiumAccess ? 'success' : 'primary' }} mb-1"><i class="bx {{ $hasPremiumAccess ? 'bx-check-shield' : 'bx-lock' }} me-1"></i> Pro</h6>
                                <ul class="small mb-0">
                                    <li><strong>AI scoring & feedback</strong></li>
                                    <li>20–25 advanced questions</li>
                                    <li>Real-time practice mode</li>
                                    <li>Expert review option</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-user-voice" style="font-size: 3.5rem; opacity: 0.3;"></i>
                        <h5 class="mt-2">Ready to get questions?</h5>
                        <p class="text-muted mb-0">Pick a resume, set the role, and generate.</p>
                    </div>
                </div>

                <!-- Results Container -->
                <div id="resultsContainer" style="display: none;">
                    <!-- Progress & Questions Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">Your Interview Questions</h5>
                            <small class="text-muted">Practice with tailored AI questions</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary" id="progressBadge">Question 1 of 8</span>
                        </div>
                    </div>

                    <!-- Questions Section -->
                    <div id="questionsList" class="mb-4">
                        <!-- Populated by JavaScript -->
                    </div>

                    <!-- Bottom CTA Bar -->
                    <div class="sticky-bottom bg-white border-top py-3 px-0 mt-4" style="box-shadow: 0 -4px 12px rgba(0,0,0,0.05);">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <p class="mb-0 text-muted small"><strong>Next Step:</strong> Practice these questions to ace your interview.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="{{ route('user.interview.ai-practice') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-microphone me-1"></i> Start Practice
                                </a>
                                @if(!$hasPremiumAccess)
                                <a href="{{ route('user.pricing') }}" class="btn btn-outline-secondary btn-sm ms-2">
                                    <i class="bx bx-crown me-1"></i> Unlock AI Feedback
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pro Features -->
                    @if($hasPremiumAccess)
                    <div class="row mb-4 mt-5">
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

        #resultsContainer {
            max-width: 900px;
            margin: 0 auto;
        }

        .question-card {
            border-left: 3px solid #667eea;
            margin-bottom: 0.8rem;
            padding: 0.9rem;
            background: #f8f9fa;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .question-card:hover {
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }

        .answer-toggle {
            cursor: pointer;
            user-select: none;
            padding: 0.4rem 0.6rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #667eea;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-block;
        }

        .answer-toggle:hover {
            color: #5568d3;
        }

        .answer-box {
            background: #f0f7ff;
            border-radius: 6px;
            padding: 0.8rem;
            margin-top: 0.5rem;
            border-left: 3px solid #28a745;
            display: none;
        }

        .answer-box.show {
            display: block;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .answer-preview {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.4;
            margin-top: 0.3rem;
        }

        .tips-box {
            background: #f3f4f6;
            border-radius: 4px;
            padding: 0.6rem 0.7rem;
            margin-top: 0.5rem;
            font-size: 0.8125rem;
            color: #6b7280;
            border-left: 2px solid #d1d5db;
        }

        .tips-box ul {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }

        .tips-box li {
            margin-bottom: 0.25rem;
        }
    </style>

    <script>
        const hasPremiumAccess = @json($hasPremiumAccess);
        let uploadedResumeFile = null;

        // Clear uploaded file on page load and autofocus
        window.addEventListener('DOMContentLoaded', function() {
            uploadedResumeFile = null;
            const jobTitleInput = document.getElementById('jobTitle');
            if (jobTitleInput) {
                jobTitleInput.focus();
            }
            updateGenerateState();
        });

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
                    updateGenerateState();

                    // Show persistent resume status indicator
                    const statusIndicator = document.getElementById('resumeStatusIndicator');
                    const statusText = document.getElementById('resumeStatusText');
                    if (statusIndicator && statusText) {
                        statusText.textContent = 'Uploaded: ' + file.name;
                        statusIndicator.style.display = 'block';

                        // Clear dropdown selection
                        const resumeSelect = document.getElementById('resumeSelect');
                        if (resumeSelect) {
                            resumeSelect.value = '';
                        }
                    }

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

            if (!jobTitle || (!resumeId && !uploadedResumeFile)) {
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
                    const successAlert = document.getElementById('generateSuccess');
                    if (successAlert) successAlert.classList.remove('d-none');
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
            const progressBadge = document.getElementById('progressBadge');

            if (data.questions && data.questions.length > 0) {
                progressBadge.textContent = `Question 1 of ${data.questions.length}`;
                let html = '';
                data.questions.forEach((q, index) => {
                    const previewText = (q.sample_answer || '').substring(0, 150).trim() + (q.sample_answer && q.sample_answer.length > 150 ? '...' : '');
                    const uniqueId = `answer-${index}`;
                    html += `
                        <div class="question-card">
                            <div class="d-flex align-items-start">
                                <div class="question-number">${index + 1}</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-600">${escapeHtml(q.question)}</h6>
                                    ${q.sample_answer ? `
                                        <div class="answer-preview text-muted small">💡 Sample answer available</div>
                                        <div class="answer-toggle" onclick="toggleAnswer('${uniqueId}')">
                                            <i class="bx bx-chevron-down me-1" style="vertical-align:-2px;"></i>
                                            <span id="${uniqueId}-text">Show sample answer</span>
                                        </div>
                                        <div class="answer-box" id="${uniqueId}">
                                            <strong class="d-block mb-1" style="color:#16a34a;">
                                                <i class="bx bx-bulb me-1"></i>Sample Answer
                                            </strong>
                                            <p class="mb-0 small" style="line-height:1.5;">${escapeHtml(q.sample_answer)}</p>
                                        </div>
                                    ` : ''}
                                    ${q.tips && q.tips.length > 0 ? `
                                        <div class="tips-box">
                                            <strong class="d-block mb-0.5" style="color:#6b7280;">💡 Tips:</strong>
                                            <ul>
                                                ${q.tips.map(tip => `<li>${escapeHtml(tip)}</li>`).join('')}
                                            </ul>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
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

        function toggleAnswer(id) {
            const answerBox = document.getElementById(id);
            const textSpan = document.getElementById(id + '-text');
            if (!answerBox) return;
            answerBox.classList.toggle('show');
            textSpan.textContent = answerBox.classList.contains('show') ? 'Hide sample answer' : 'Show sample answer';
        }

        // Add visual feedback when resume is selected from dropdown
        const resumeSelectElement = document.getElementById('resumeSelect');
        if (resumeSelectElement) {
            resumeSelectElement.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const statusIndicator = document.getElementById('resumeStatusIndicator');
                const statusText = document.getElementById('resumeStatusText');

                if (this.value) {
                    // Clear any uploaded file since user chose saved resume
                    uploadedResumeFile = null;

                    // Show status indicator
                    if (statusIndicator && statusText) {
                        statusText.textContent = 'Using: ' + selectedOption.text;
                        statusIndicator.style.display = 'block';
                    }

                    console.log('Resume selected from dropdown:', this.value, selectedOption.text);
                    updateGenerateState();
                } else {
                    // Hide status indicator when deselected
                    if (statusIndicator) {
                        statusIndicator.style.display = 'none';
                    }
                }
            });
        }

        function updateGenerateState() {
            const resumeId = document.getElementById('resumeSelect')?.value || '';
            const jobTitle = document.getElementById('jobTitle')?.value.trim() || '';
            const btn = document.getElementById('generateBtn');
            if (!btn) return;
            btn.disabled = !(jobTitle && (resumeId || uploadedResumeFile));
        }

        // Track form inputs for enabling CTA
        document.getElementById('jobTitle')?.addEventListener('input', updateGenerateState);
        document.getElementById('resumeSelect')?.addEventListener('change', updateGenerateState);
    </script>
</x-layouts.app>
