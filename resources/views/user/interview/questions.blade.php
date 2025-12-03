@section('title', __('Interview Prep - Practice Questions'))
<x-layouts.app :title="__('Practice Questions')">
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
                                Prepare with common interview questions
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Questions List -->
            <div id="questionsContainer">
                @foreach($questions as $question)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-2">{{ $question['question'] }}</h6>
                            <div>
                                <span class="badge bg-primary">{{ $question['category'] }}</span>
                                <span class="badge bg-{{ $question['difficulty'] === 'Easy' ? 'success' : ($question['difficulty'] === 'Medium' ? 'warning' : 'danger') }}">
                                    {{ $question['difficulty'] }}
                                </span>
                            </div>
                        </div>

                        <button class="btn btn-sm btn-outline-primary mb-3" type="button"
                                data-bs-toggle="collapse" data-bs-target="#tips{{ $question['id'] }}">
                            <i class="bx bx-bulb me-1"></i> Tips
                        </button>

                        <div class="collapse" id="tips{{ $question['id'] }}">
                            <div class="alert alert-info border-0 mb-0">
                                <ul class="ps-3 mb-0">
                                    @foreach($question['tips'] as $tip)
                                    <li class="mb-2">{{ $tip }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Next Steps -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-chevron-right me-1"></i> Next Steps
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Ready to practice with AI? Try a mock interview to get real-time feedback and improve your skills.</p>
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
                        <i class="bx bx-sparkles me-1"></i> Interview Features
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
                    <a href="{{ route('user.pricing') }}" class="btn btn-primary btn-sm w-100">
                        <i class="bx bx-crown me-1"></i> Upgrade to Pro
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
