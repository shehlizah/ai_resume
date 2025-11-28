@section('title', __('AI Mock Interview'))
<x-layouts.app :title="__('AI Mock Interview')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <h4 class="text-white mb-2">
                        <i class="bx bx-bot me-2"></i> AI Mock Interview
                    </h4>
                    <p class="text-white mb-0 opacity-90">
                        Practice with our AI interviewer and get instant feedback
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm" id="interviewCard">
                <div class="card-body">
                    <form id="startInterviewForm" onsubmit="startInterview(event)">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Target Job Title</label>
                                <input type="text" class="form-control" id="jobTitle"
                                       placeholder="e.g. Senior Developer" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company (optional)</label>
                                <input type="text" class="form-control" id="company"
                                       placeholder="e.g. Tech Corp">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Interview Type</label>
                                <select class="form-select" id="interviewType" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="behavioral">Behavioral</option>
                                    <option value="technical">Technical</option>
                                    <option value="both">Both (Behavioral + Technical)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration</label>
                                <select class="form-select" id="duration" required>
                                    <option value="">-- Select Duration --</option>
                                    <option value="15">15 minutes</option>
                                    <option value="30">30 minutes</option>
                                    <option value="60">1 hour</option>
                                </select>
                            </div>
                            @if($resumes->count() > 0)
                            <div class="col-md-12">
                                <label class="form-label">
                                    <i class="bx bx-file me-2"></i> <strong>Select a CV for Reference (Optional)</strong>
                                </label>
                                <select class="form-select" id="resumeId">
                                    <option value="">-- None --</option>
                                    @foreach($resumes as $resume)
                                    <option value="{{ $resume->id }}">{{ $resume->title ?? 'Resume #' . $resume->id }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <i class="bx bx-info-circle me-1"></i> Provide your CV so AI can tailor questions to your experience.
                                </small>
                            </div>
                            @endif
                            
                            <!-- OR Upload Resume -->
                            <div class="col-md-12">
                                <div class="border-top pt-3 mt-2">
                                    <p class="text-muted small mb-2">Or upload a resume from your computer:</p>
                                    <div class="drop-zone border-2 border-dashed rounded p-3 text-center" id="interviewResumeDropZone" style="border-color: #667eea; cursor: pointer; transition: all 0.3s;">
                                        <i class="bx bx-cloud-upload" style="font-size: 2rem; color: #667eea;"></i>
                                        <p class="mb-1 small"><strong>Drop resume here or click</strong></p>
                                        <small class="text-muted">PDF, DOCX (Max 10MB)</small>
                                        <input type="file" id="interviewResumeInput" accept=".pdf,.doc,.docx" style="display: none;">
                                    </div>
                                    <div id="interviewUploadStatus" class="mt-2" style="display: none;">
                                        <div class="alert alert-info border-0 mb-0 py-2">
                                            <small><i class="bx bx-loader-alt bx-spin me-2"></i> <span id="interviewStatusText">Processing resume...</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bx bx-play-circle me-2"></i> Start Mock Interview
                        </button>
                    </form>
                </div>
            </div>

            <!-- Interview Session (hidden initially) -->
            <div id="interviewSession" style="display: none;">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="mb-0">Question <span id="questionNumber">1</span></h6>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted" id="timeElapsed">00:00</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p class="lead mb-3" id="questionText"></p>
                            <small class="text-muted" id="questionHint"></small>
                        </div>
                    </div>
                </div>

                <!-- Answer Box -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0">Your Answer</h6>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="answerBox" rows="6"
                                  placeholder="Type your answer here..."></textarea>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="endInterview()">
                                End Interview
                            </button>
                            <button type="button" class="btn btn-primary" onclick="submitAnswer()">
                                <i class="bx bx-send me-1"></i> Submit & Next Question
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Info -->
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bx bx-brain mb-3" style="font-size: 2rem; color: #667eea;"></i>
                            <h6>AI Powered</h6>
                            <p class="text-muted small">Realistic interview experience powered by AI</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bx bx-star mb-3" style="font-size: 2rem; color: #667eea;"></i>
                            <h6>Instant Feedback</h6>
                            <p class="text-muted small">Get real-time feedback on your answers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bx bx-trending-up mb-3" style="font-size: 2rem; color: #667eea;"></i>
                            <h6>Scoring</h6>
                            <p class="text-muted small">Track your progress and improvements</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentSessionId = null;
    let currentQuestionId = 1;

    function startInterview(event) {
        event.preventDefault();

        const jobTitle = document.getElementById('jobTitle').value;
        const company = document.getElementById('company').value || 'Your Target Company';
        const interviewType = document.getElementById('interviewType').value;
        const resumeId = document.getElementById('resumeId')?.value || null;

        fetch('{{ route("user.interview.ai-practice-start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                job_title: jobTitle,
                company: company,
                interview_type: interviewType,
                resume_id: resumeId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentSessionId = data.session_id;
                displayQuestion(data.first_question);
                document.getElementById('interviewCard').style.display = 'none';
                document.getElementById('interviewSession').style.display = 'block';
            } else {
                alert('Error starting interview: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error starting interview');
        });
    }

    function displayQuestion(question) {
        document.getElementById('questionText').textContent = question.question;
        document.getElementById('questionNumber').textContent = currentQuestionId;
        document.getElementById('answerBox').value = '';
        document.getElementById('answerBox').focus();
    }

    function submitAnswer() {
        const answer = document.getElementById('answerBox').value.trim();

        if (!answer) {
            alert('Please provide an answer');
            return;
        }

        const submitBtn = event.target;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

        fetch('{{ route("user.interview.ai-practice-answer") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                session_id: currentSessionId,
                question_id: currentQuestionId,
                answer: answer
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show feedback
                alert('Feedback: ' + data.feedback + '\nScore: ' + data.score + '/100');

                // Move to next question
                if (data.next_question) {
                    currentQuestionId++;
                    displayQuestion(data.next_question);
                } else {
                    endInterview();
                }
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bx bx-send me-1"></i> Submit & Next Question';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting answer');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bx bx-send me-1"></i> Submit & Next Question';
        });
    }

    function endInterview() {
        if (confirm('End interview? You can view your results.')) {
            window.location.href = '{{ route("user.interview.ai-results", ["sessionId" => ""]) }}'.replace('""', '"' + currentSessionId + '"');
        }
    }

    // Interview Resume file upload handling
    const interviewDropZone = document.getElementById('interviewResumeDropZone');
    const interviewFileInput = document.getElementById('interviewResumeInput');
    const interviewUploadStatus = document.getElementById('interviewUploadStatus');

    if (interviewDropZone && interviewFileInput) {
        // Click to upload
        interviewDropZone.addEventListener('click', () => interviewFileInput.click());

        // Drag and drop
        interviewDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            interviewDropZone.style.borderColor = '#764ba2';
            interviewDropZone.style.backgroundColor = 'rgba(118, 75, 162, 0.05)';
        });

        interviewDropZone.addEventListener('dragleave', () => {
            interviewDropZone.style.borderColor = '#667eea';
            interviewDropZone.style.backgroundColor = 'transparent';
        });

        interviewDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            interviewDropZone.style.borderColor = '#667eea';
            interviewDropZone.style.backgroundColor = 'transparent';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleInterviewResumeUpload(files[0]);
            }
        });

        // File input change
        interviewFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleInterviewResumeUpload(e.target.files[0]);
            }
        });
    }

    function handleInterviewResumeUpload(file) {
        // Validate file
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (!allowedTypes.includes(file.type)) {
            alert('❌ Please upload a PDF or DOCX file');
            return;
        }

        if (file.size > maxSize) {
            alert('❌ File size must be less than 10MB');
            return;
        }

        // Upload file
        const formData = new FormData();
        formData.append('resume_file', file);

        interviewUploadStatus.style.display = 'block';
        document.getElementById('interviewStatusText').textContent = 'Uploading ' + file.name + '...';

        fetch('{{ route("user.resumes.upload-temp") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Upload failed with status ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Store the temporary file path/ID
                sessionStorage.setItem('interviewUploadedResumeFile', data.file_path);
                
                // Update status text
                document.getElementById('interviewStatusText').innerHTML = '<i class="bx bx-check-circle me-2"></i> ' + file.name + ' uploaded!';
                
                // Clear file input
                interviewFileInput.value = '';
                
                // Hide status after 3 seconds
                setTimeout(() => {
                    interviewUploadStatus.style.display = 'none';
                }, 3000);
            } else {
                alert('❌ Error: ' + (data.message || 'Unknown error'));
                interviewUploadStatus.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            interviewUploadStatus.style.display = 'none';
            alert('❌ Error uploading file: ' + error.message);
        });
    }
    </script>
</x-layouts.app>
