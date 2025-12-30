@section('title', __('Interview Prep - Practice Questions'))
<x-layouts.app :title="__('Practice Questions')">
    <style>
        .sidebar-sticky {
            position: sticky;
            top: 20px;
        }
        
        .progress-indicator {
            font-size: 0.9rem;
            color: #667eea;
            font-weight: 600;
        }
        
        .difficulty-section {
            margin-top: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .difficulty-label {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #667eea;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e8eaf6;
        }
        
        .question-card {
            padding: 1.25rem !important;
            transition: all 0.3s ease;
            border: 1px solid transparent !important;
        }
        
        .question-card:hover {
            border-color: #667eea !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1) !important;
        }
        
        .question-title {
            font-weight: 600;
            font-size: 1rem;
            color: #1a1a1a;
            margin-bottom: 0.75rem;
        }
        
        .tag-container {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }
        
        .difficulty-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .tips-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.25rem 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .tips-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .tips-link.active i {
            transform: rotate(180deg);
        }
        
        .tips-content {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: #f0f4ff;
            border-left: 3px solid #667eea;
            border-radius: 0.25rem;
            display: none;
        }
        
        .tips-content.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .secondary-cta {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 1px dashed #667eea;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            margin: 2rem 0;
        }
        
        .upgrade-highlight {
            background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%);
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 0.25rem;
        }
        
        @media (max-width: 991px) {
            .sidebar-sticky {
                position: static;
                top: auto;
            }
            
            .question-card {
                padding: 1rem !important;
            }
            
            .tag-container {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">
                                <i class="bx bx-chat me-2"></i> Interview Practice
                            </h4>
                            <p class="text-white mb-0 opacity-90">
                                Answer these confidently and you're interview-ready
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Progress Indicator -->
            <div class="progress-indicator mb-4">
                <i class="bx bx-check-circle me-1"></i> Question <span id="totalQuestions">{{ count($questions) }}</span> available
            </div>

            <!-- Questions List -->
            <div id="questionsContainer">
                @php
                    $questionsByDifficulty = collect($questions)->groupBy('difficulty');
                @endphp

                @foreach(['Easy', 'Medium', 'Hard'] as $difficulty)
                    @if($questionsByDifficulty->has($difficulty))
                    <div class="difficulty-section">
                        <div class="difficulty-label">
                            {{ $difficulty }}
                            @if($difficulty === 'Easy')
                                <span class="badge bg-success ms-2">Common question</span>
                            @elseif($difficulty === 'Medium')
                                <span class="badge bg-warning ms-2">Frequently asked</span>
                            @else
                                <span class="badge bg-danger ms-2">High-impact question</span>
                            @endif
                        </div>

                        @foreach($questionsByDifficulty[$difficulty] as $index => $question)
                        <div class="card border-0 shadow-sm question-card mb-3">
                            <div class="card-body">
                                <!-- Tags at Top-Right -->
                                <div class="tag-container">
                                    <span class="badge bg-primary difficulty-badge">{{ $question['category'] }}</span>
                                </div>

                                <!-- Question Title -->
                                <h6 class="question-title mb-3">{{ $question['question'] }}</h6>

                                <!-- Tips Link -->
                                <a class="tips-link" onclick="toggleTips(this, 'tips{{ $question['id'] }}')">
                                    <i class="bx bx-chevron-down"></i> Tips
                                </a>

                                <!-- Tips Content (Hidden by Default) -->
                                <div class="tips-content" id="tips{{ $question['id'] }}">
                                    <ul class="ps-3 mb-0">
                                        @foreach($question['tips'] as $tip)
                                        <li class="mb-2 small">{{ $tip }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary CTA every 3 questions -->
                        @if(($index + 1) % 3 === 0 && !$loop->last)
                        <div class="secondary-cta">
                            <p class="mb-2 small text-muted">
                                <i class="bx bx-lightbulb text-warning me-1"></i> Get AI feedback on your answers
                            </p>
                            @if($hasPremiumAccess)
                            <a href="{{ route('user.interview.ai-practice') }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-bot me-1"></i> Try AI Mock Interview
                            </a>
                            @else
                            <a href="{{ route('user.pricing') }}" class="btn btn-warning btn-sm">
                                <i class="bx bx-lock me-1"></i> Unlock AI Practice
                            </a>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Next Steps -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-chevron-right me-1"></i> Ready to Practice?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Take your interview prep to the next level with AI-powered feedback and real-time scoring.</p>
                    @if($hasPremiumAccess)
                    <a href="{{ route('user.interview.ai-practice') }}" class="btn btn-primary btn-sm w-100">
                        <i class="bx bx-bot me-1"></i> Start AI Mock Interview
                    </a>
                    @else
                    <a href="{{ route('user.pricing') }}" class="btn btn-warning btn-sm w-100">
                        <i class="bx bx-lock me-1"></i> Unlock AI Practice (PRO)
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-sticky">
                <!-- Interview Tips -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0">
                            <i class="bx bx-info-circle me-1"></i> Interview Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="ps-3 small">
                            <li class="mb-2"><strong>Research:</strong> Learn about the company and role</li>
                            <li class="mb-2"><strong>STAR Method:</strong> Structure answers with Situation, Task, Action, Result</li>
                            <li class="mb-2"><strong>Practice:</strong> Rehearse answers out loud</li>
                            <li class="mb-2"><strong>Body Language:</strong> Maintain eye contact and good posture</li>
                            <li class="mb-2"><strong>Questions:</strong> Prepare questions to ask the interviewer</li>
                        </ul>
                    </div>
                </div>

                <!-- Available Features -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0">
                            <i class="bx bx-sparkles me-1"></i> Improve Faster
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                <strong class="small">Practice Questions</strong>
                            </div>
                            <p class="text-muted small mb-0">Free access to common interview questions</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bx bx-lock text-warning me-2"></i>
                                <strong class="small">AI Mock Interview</strong>
                            </div>
                            <p class="text-muted small mb-0">Get AI-powered feedback and scoring</p>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bx bx-lock text-warning me-2"></i>
                                <strong class="small">Expert Sessions</strong>
                            </div>
                            <p class="text-muted small mb-0">Book 1-on-1 coaching with professionals</p>
                        </div>
                        @if(!$hasPremiumAccess)
                        <hr>
                        <div class="upgrade-highlight">
                            <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100">
                                <i class="bx bx-crown me-1"></i> Next Level Preparation
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTips(linkElement, tipsId) {
            const tipsContent = document.getElementById(tipsId);
            tipsContent.classList.toggle('show');
            linkElement.classList.toggle('active');
        }
    </script>
</x-layouts.app>
