@section('title', __('Interview Results'))
<x-layouts.app :title="__('Interview Results')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <h4 class="text-white mb-2">
                        <i class="bx bx-bar-chart me-2"></i> Interview Results
                    </h4>
                    <p class="text-white mb-0 opacity-90">
                        Review your performance and get improvement tips
                    </p>
                </div>
            </div>
        </div>

        <!-- Overall Score -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <h1 class="text-primary mb-2" style="font-size: 4rem;">{{ $sessionData['overall_score'] }}%</h1>
                    <h5 class="mb-3">Overall Score</h5>
                    <p class="text-muted">Interview for: <strong>{{ $sessionData['job_title'] }} at {{ $sessionData['company'] }}</strong></p>
                </div>
            </div>

            <!-- Strengths -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-check-circle text-success me-1"></i> Your Strengths
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="ps-3">
                        @foreach($sessionData['strengths'] as $strength)
                        <li class="mb-2">{{ $strength }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Areas to Improve -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-info-circle text-warning me-1"></i> Areas to Improve
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="ps-3">
                        @foreach($sessionData['improvements'] as $improvement)
                        <li class="mb-2">{{ $improvement }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Detailed Feedback -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-comment-detail me-1"></i> Detailed Feedback
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($sessionData['detailed_feedback'] as $feedback)
                        <div class="timeline-item mb-3">
                            <p class="text-muted small mb-0">{{ $feedback }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Next Steps -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-chevron-right me-1"></i> Next Steps
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('user.interview.ai-practice') }}" class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="bx bx-refresh me-1"></i> Try Another Interview
                    </a>
                    <a href="{{ route('user.interview.expert') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bx bx-user-check me-1"></i> Book Expert Session
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bx bx-home me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bx bx-lightbulb me-1"></i> Pro Tips
                    </h6>
                </div>
                <div class="card-body small">
                    <ul class="ps-3 mb-0">
                        <li class="mb-2">Practice regularly to improve your score</li>
                        <li class="mb-2">Review your feedback and work on weak areas</li>
                        <li class="mb-2">Book an expert session for personalized coaching</li>
                        <li>Track your progress over multiple interviews</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 0;
        }

        .timeline-item {
            padding-left: 30px;
            position: relative;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #667eea;
        }
    </style>
</x-layouts.app>
