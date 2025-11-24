<x-layouts.app :title="'AI Job Search - ' . $addOn->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.add-ons.my-add-ons') }}">My Add-Ons</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.add-ons.access', $addOn) }}">{{ $addOn->name }}</a></li>
                    <li class="breadcrumb-item active">AI Job Search</li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="card bg-gradient-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bx bx-brain" style="font-size: 4rem;"></i>
                    <div class="ms-4">
                        <h3 class="text-white mb-2">🤖 AI-Powered Job Search</h3>
                        <p class="mb-0 opacity-75">Get personalized job board recommendations powered by artificial intelligence</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">
                            <i class="bx bx-search-alt me-2"></i>
                            Tell Us About Your Search
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="jobSearchForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    Job Title / Role <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="job_title" 
                                       id="job_title"
                                       class="form-control" 
                                       placeholder="e.g., Software Engineer, Data Analyst"
                                       required>
                                <small class="text-muted">The position you're looking for</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location (Optional)</label>
                                <input type="text" 
                                       name="location" 
                                       id="location"
                                       class="form-control" 
                                       placeholder="e.g., New York, Remote, USA">
                                <small class="text-muted">City, state, or "Remote"</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Key Skills (Optional)</label>
                                <textarea name="skills" 
                                          id="skills"
                                          class="form-control" 
                                          rows="3"
                                          placeholder="e.g., Python, React, Project Management"></textarea>
                                <small class="text-muted">Separate with commas</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                                    <i class="bx bx-sparkles me-1"></i>
                                    <span id="btnText">Generate Recommendations</span>
                                    <span id="btnLoading" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        AI is thinking...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            <small>Our AI will analyze your input and suggest the best job boards and search strategies!</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="col-lg-8">
                <!-- Empty State -->
                <div id="emptyState" class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-search-alt" style="font-size: 5rem; opacity: 0.3;"></i>
                        <h4 class="mt-3">Ready to Find Your Dream Job?</h4>
                        <p class="text-muted mb-0">
                            Fill in the form and let our AI help you discover the best job opportunities!
                        </p>
                    </div>
                </div>

                <!-- Results -->
                <div id="results" class="d-none">
                    <!-- Job Boards -->
                    <div id="jobBoardsSection" class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-briefcase me-2"></i>
                                Recommended Job Boards
                            </h5>
                        </div>
                        <div class="card-body" id="jobBoardsList">
                            <!-- Dynamic content -->
                        </div>
                    </div>

                    <!-- Search Strategies -->
                    <div id="strategiesSection" class="card mb-4 d-none">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-bulb me-2"></i>
                                Search Strategies
                            </h5>
                        </div>
                        <div class="card-body" id="strategiesList">
                            <!-- Dynamic content -->
                        </div>
                    </div>

                    <!-- Keywords -->
                    <div id="keywordsSection" class="card mb-4 d-none">
                        <div class="card-header">
                            <h6 class="mb-0">🔑 Recommended Keywords</h6>
                        </div>
                        <div class="card-body" id="keywordsList">
                            <!-- Dynamic content -->
                        </div>
                    </div>

                    <!-- Industry Insights -->
                    <div id="insightsSection" class="card d-none">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0">💡 Industry Insights</h6>
                        </div>
                        <div class="card-body" id="insightsContent">
                            <!-- Dynamic content -->
                        </div>
                    </div>
                </div>

                <!-- Error State -->
                <div id="errorState" class="alert alert-danger d-none">
                    <i class="bx bx-error-circle me-2"></i>
                    <span id="errorMessage"></span>
                </div>
            </div>
        </div>

    </div>

    <style>
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>

    <script>
        document.getElementById('jobSearchForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            const generateBtn = document.getElementById('generateBtn');
            const emptyState = document.getElementById('emptyState');
            const results = document.getElementById('results');
            const errorState = document.getElementById('errorState');

            // Show loading
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            generateBtn.disabled = true;
            emptyState.classList.add('d-none');
            errorState.classList.add('d-none');

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route("user.add-ons.generate-jobs", $addOn) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    displayResults(data.data);
                    results.classList.remove('d-none');
                } else {
                    showError(data.error || 'Failed to generate recommendations');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred. Please try again.');
            } finally {
                // Hide loading
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                generateBtn.disabled = false;
            }
        });

        function displayResults(data) {
            // Display Job Boards
            if (data.job_boards && data.job_boards.length > 0) {
                const jobBoardsList = document.getElementById('jobBoardsList');
                let html = '<div class="row g-3">';
                
                data.job_boards.forEach(board => {
                    const priorityColor = board.priority === 'high' ? 'success' : 
                                        board.priority === 'medium' ? 'warning' : 'secondary';
                    
                    html += `
                        <div class="col-md-6">
                            <a href="${board.url}" target="_blank" class="text-decoration-none">
                                <div class="card hover-lift border-${priorityColor}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 text-dark">${board.name}</h6>
                                            <span class="badge bg-${priorityColor}">${board.priority || 'medium'}</span>
                                        </div>
                                        <p class="text-muted small mb-2">${board.category}</p>
                                        ${board.search_query ? `<p class="small mb-2"><strong>Search:</strong> "${board.search_query}"</p>` : ''}
                                        ${board.why_recommended ? `<p class="small text-muted mb-0">${board.why_recommended}</p>` : ''}
                                        <div class="mt-2">
                                            <i class="bx bx-link-external text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                });
                
                html += '</div>';
                jobBoardsList.innerHTML = html;
            }

            // Display Search Strategies
            if (data.search_strategies && data.search_strategies.length > 0) {
                const strategiesSection = document.getElementById('strategiesSection');
                const strategiesList = document.getElementById('strategiesList');
                strategiesSection.classList.remove('d-none');
                
                let html = '';
                data.search_strategies.forEach((strategy, index) => {
                    html += `
                        <div class="mb-3 ${index > 0 ? 'border-top pt-3' : ''}">
                            <h6><i class="bx bx-right-arrow-alt me-1 text-info"></i>${strategy.strategy}</h6>
                            <p class="text-muted mb-2">${strategy.description}</p>
                            ${strategy.tips ? `
                                <ul class="small">
                                    ${strategy.tips.map(tip => `<li>${tip}</li>`).join('')}
                                </ul>
                            ` : ''}
                        </div>
                    `;
                });
                
                strategiesList.innerHTML = html;
            }

            // Display Keywords
            if (data.keywords && data.keywords.length > 0) {
                const keywordsSection = document.getElementById('keywordsSection');
                const keywordsList = document.getElementById('keywordsList');
                keywordsSection.classList.remove('d-none');
                
                let html = '';
                data.keywords.forEach(keyword => {
                    html += `<span class="badge bg-primary me-2 mb-2">${keyword}</span>`;
                });
                
                keywordsList.innerHTML = html;
            }

            // Display Industry Insights
            if (data.industry_insights) {
                const insightsSection = document.getElementById('insightsSection');
                const insightsContent = document.getElementById('insightsContent');
                insightsSection.classList.remove('d-none');
                insightsContent.innerHTML = `<p class="mb-0">${data.industry_insights}</p>`;
            }

            // Handle raw content fallback
            if (data.raw_content && (!data.job_boards || data.job_boards.length === 0)) {
                const jobBoardsList = document.getElementById('jobBoardsList');
                jobBoardsList.innerHTML = `<div class="alert alert-info">${data.raw_content.replace(/\n/g, '<br>')}</div>`;
            }
        }

        function showError(message) {
            const errorState = document.getElementById('errorState');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorState.classList.remove('d-none');
        }
    </script>
</x-layouts.app>