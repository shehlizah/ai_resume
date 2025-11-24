<x-layouts.app :title="'AI Interview Prep - ' . $addOn->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.add-ons.my-add-ons') }}">My Add-Ons</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.add-ons.access', $addOn) }}">{{ $addOn->name }}</a></li>
                    <li class="breadcrumb-item active">AI Interview Prep</li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="card bg-gradient-warning text-white mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bx bx-brain" style="font-size: 4rem;"></i>
                    <div class="ms-4">
                        <h3 class="text-white mb-2">🎯 AI-Powered Interview Preparation</h3>
                        <p class="mb-0 opacity-75">Get personalized interview questions, answers, and strategies powered by AI</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0 text-white">
                            <i class="bx bx-user-voice me-2"></i>
                            Interview Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="interviewPrepForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    Job Title / Role <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="job_title" 
                                       id="job_title"
                                       class="form-control" 
                                       placeholder="e.g., Data Analyst, Marketing Manager"
                                       required>
                                <small class="text-muted">Position you're interviewing for</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Experience Level <span class="text-danger">*</span>
                                </label>
                                <select name="experience_level" id="experience_level" class="form-select" required>
                                    <option value="">Select Level</option>
                                    <option value="entry">Entry Level (0-2 years)</option>
                                    <option value="mid">Mid Level (3-5 years)</option>
                                    <option value="senior">Senior Level (6-10 years)</option>
                                    <option value="executive">Executive Level (10+ years)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Company Type <span class="text-danger">*</span>
                                </label>
                                <select name="company_type" id="company_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="startup">Startup</option>
                                    <option value="corporate">Corporate/Enterprise</option>
                                    <option value="nonprofit">Nonprofit</option>
                                    <option value="government">Government</option>
                                    <option value="consulting">Consulting</option>
                                </select>
                                <small class="text-muted">This helps tailor the advice</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg text-white" id="generateBtn">
                                    <span id="btnText">
                                        <i class="bx bx-sparkles me-1"></i>
                                        Generate Interview Prep
                                    </span>
                                    <span id="btnLoading" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        AI is preparing...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            <small>This may take 30-60 seconds. Our AI will create a comprehensive interview guide!</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="col-lg-8">
                <!-- Empty State -->
                <div id="emptyState" class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-user-voice" style="font-size: 5rem; opacity: 0.3;"></i>
                        <h4 class="mt-3">Ready to Ace Your Interview?</h4>
                        <p class="text-muted mb-0">
                            Tell us about the role and we'll prepare you with personalized questions and strategies!
                        </p>
                    </div>
                </div>

                <!-- Error State -->
                <div id="errorState" class="alert alert-danger d-none">
                    <i class="bx bx-error-circle me-2"></i>
                    <span id="errorMessage"></span>
                </div>

                <!-- Results Container -->
                <div id="resultsContainer" class="d-none">
                    
                    <!-- Action Buttons -->
                    <div class="card border-success mb-4">
                        <div class="card-body text-center">
                            <h5>📥 Save This Preparation Guide</h5>
                            <p class="text-muted mb-3">Download or print for offline reference</p>
                            <button class="btn btn-success" onclick="window.print()">
                                <i class="bx bx-printer me-1"></i> Print Guide
                            </button>
                        </div>
                    </div>

                    <!-- Common Questions Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-question-mark me-2"></i>
                                Common Interview Questions
                            </h5>
                        </div>
                        <div class="card-body" id="questionsList">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Technical Topics Section -->
                    <div id="technicalSection" class="card mb-4 d-none">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-code-alt me-2"></i>
                                Technical Topics to Study
                            </h5>
                        </div>
                        <div class="card-body" id="technicalContent">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Questions to Ask Section -->
                    <div id="questionsToAskSection" class="card mb-4 d-none">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">
                                <i class="bx bx-chat me-2"></i>
                                Questions to Ask the Interviewer
                            </h5>
                        </div>
                        <div class="card-body" id="questionsToAskList">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Salary Tips Section -->
                    <div id="salarySection" class="card mb-4 d-none">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-dollar-circle me-2"></i>
                                Salary Negotiation Tips
                            </h5>
                        </div>
                        <div class="card-body" id="salaryContent">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Day of Interview Section -->
                    <div id="dayOfSection" class="card mb-4 d-none">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-calendar-check me-2"></i>
                                Day of Interview Checklist
                            </h5>
                        </div>
                        <div class="card-body" id="dayOfContent">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <style>
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .question-card {
            border-left: 4px solid #6366f1;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .question-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transform: translateX(5px);
        }

        .answer-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.75rem;
        }

        .category-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
        }

        @media print {
            .sticky-top, nav, .btn, .breadcrumb, .card-header.bg-warning { 
                display: none !important; 
            }
            .card { 
                break-inside: avoid; 
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
            .col-lg-8 {
                width: 100% !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('interviewPrepForm');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const btnText = document.getElementById('btnText');
                const btnLoading = document.getElementById('btnLoading');
                const generateBtn = document.getElementById('generateBtn');
                const emptyState = document.getElementById('emptyState');
                const resultsContainer = document.getElementById('resultsContainer');
                const errorState = document.getElementById('errorState');

                // Show loading state
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                generateBtn.disabled = true;
                emptyState.classList.add('d-none');
                errorState.classList.add('d-none');
                resultsContainer.classList.add('d-none');

                const formData = new FormData(this);

                try {
                    const response = await fetch('{{ route("user.add-ons.generate-interview", $addOn) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success && result.data) {
                        displayResults(result.data);
                        resultsContainer.classList.remove('d-none');
                        // Smooth scroll to results
                        setTimeout(() => {
                            resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    } else {
                        showError(result.error || 'Failed to generate interview prep');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError('An unexpected error occurred. Please try again.');
                } finally {
                    // Reset button state
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');
                    generateBtn.disabled = false;
                }
            });
        });

        function displayResults(data) {
            console.log('Displaying results:', data);

            // 1. Display Common Questions (ARRAY)
            if (data.common_questions && Array.isArray(data.common_questions) && data.common_questions.length > 0) {
                const questionsList = document.getElementById('questionsList');
                let html = '';
                
                data.common_questions.forEach((q, index) => {
                    const categoryColors = {
                        'behavioral': 'primary',
                        'technical': 'info',
                        'situational': 'success'
                    };
                    const categoryColor = categoryColors[q.category?.toLowerCase()] || 'secondary';
                    
                    html += `
                        <div class="question-card card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 flex-grow-1">
                                        <span class="badge bg-${categoryColor} me-2">${index + 1}</span>
                                        ${escapeHtml(q.question)}
                                    </h6>
                                    <span class="badge bg-light text-dark ms-2 category-badge">
                                        ${escapeHtml(q.category || 'General')}
                                    </span>
                                </div>
                                
                                <div class="answer-box">
                                    <strong class="text-success d-block mb-2">
                                        <i class="bx bx-bulb me-1"></i>Sample Answer:
                                    </strong>
                                    <p class="mb-0">${formatText(q.sample_answer)}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                questionsList.innerHTML = html;
            }

            // 2. Display Technical Topics (STRING)
            if (data.technical_topics && typeof data.technical_topics === 'string' && data.technical_topics.trim()) {
                const technicalSection = document.getElementById('technicalSection');
                const technicalContent = document.getElementById('technicalContent');
                technicalSection.classList.remove('d-none');
                technicalContent.innerHTML = `<div class="text-content">${formatText(data.technical_topics)}</div>`;
            }

            // 3. Display Questions to Ask (ARRAY)
            if (data.questions_to_ask && Array.isArray(data.questions_to_ask) && data.questions_to_ask.length > 0) {
                const questionsToAskSection = document.getElementById('questionsToAskSection');
                const questionsToAskList = document.getElementById('questionsToAskList');
                questionsToAskSection.classList.remove('d-none');
                
                let html = '<ul class="list-group list-group-flush">';
                data.questions_to_ask.forEach((question, index) => {
                    html += `
                        <li class="list-group-item">
                            <i class="bx bx-chevron-right text-primary me-2"></i>
                            <strong>${index + 1}.</strong> ${escapeHtml(question)}
                        </li>
                    `;
                });
                html += '</ul>';
                
                questionsToAskList.innerHTML = html;
            }

            // 4. Display Salary Tips (STRING)
            if (data.salary_tips && typeof data.salary_tips === 'string' && data.salary_tips.trim()) {
                const salarySection = document.getElementById('salarySection');
                const salaryContent = document.getElementById('salaryContent');
                salarySection.classList.remove('d-none');
                salaryContent.innerHTML = `<div class="text-content">${formatText(data.salary_tips)}</div>`;
            }

            // 5. Display Day of Interview Tips (STRING)
            if (data.day_of_tips && typeof data.day_of_tips === 'string' && data.day_of_tips.trim()) {
                const dayOfSection = document.getElementById('dayOfSection');
                const dayOfContent = document.getElementById('dayOfContent');
                dayOfSection.classList.remove('d-none');
                dayOfContent.innerHTML = `<div class="text-content">${formatText(data.day_of_tips)}</div>`;
            }
        }

        function formatText(text) {
            if (!text) return '';
            
            // Escape HTML first
            let formatted = escapeHtml(text);
            
            // Convert various newline formats to <br>
            formatted = formatted.replace(/\\n/g, '<br>');
            formatted = formatted.replace(/\n/g, '<br>');
            
            // Convert **bold** to <strong>
            formatted = formatted.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            
            // Convert - bullet points to proper list items
            formatted = formatted.replace(/^- (.+)$/gm, '• $1');
            
            return formatted;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showError(message) {
            const errorState = document.getElementById('errorState');
            const errorMessage = document.getElementById('errorMessage');
            const emptyState = document.getElementById('emptyState');
            
            errorMessage.textContent = message;
            errorState.classList.remove('d-none');
            emptyState.classList.add('d-none');
        }
    </script>
</x-layouts.app>