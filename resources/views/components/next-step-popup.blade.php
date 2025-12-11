@props(['nextStep'])

@php
$popupConfig = [
    'cover_letter' => [
        'icon' => 'bx-file-blank',
        'iconColor' => 'text-success',
        'title' => '🎉 Great Job on Your Resume!',
        'message' => 'Your resume looks fantastic! Why not create a professional cover letter to complement it?',
        'buttonText' => 'Create Cover Letter',
        'buttonIcon' => 'bx-file-blank',
        'route' => route('user.cover-letters.create'),
    ],
    'interview_prep' => [
        'icon' => 'bx-briefcase',
        'iconColor' => 'text-primary',
        'title' => '🚀 Ready for the Next Step?',
        'message' => 'You have your resume and cover letter ready! Time to ace your interviews with AI-powered practice sessions.',
        'buttonText' => 'Start Interview Prep',
        'buttonIcon' => 'bx-microphone',
        'route' => route('user.interview.prep'),
    ],
    'job_search' => [
        'icon' => 'bx-search-alt',
        'iconColor' => 'text-info',
        'title' => '💼 Time to Find Your Dream Job!',
        'message' => 'You\'re interview-ready! Let\'s find the perfect job opportunities that match your skills and experience.',
        'buttonText' => 'Search Jobs',
        'buttonIcon' => 'bx-briefcase',
        'route' => route('user.jobs.recommended'),
    ],
    'book_session' => [
        'icon' => 'bx-calendar-check',
        'iconColor' => 'text-danger',
        'title' => '🎯 Take Your Career to the Next Level!',
        'message' => 'Ready for personalized guidance? Book a 1-on-1 session with our career experts to refine your strategy.',
        'buttonText' => 'Book Expert Session',
        'buttonIcon' => 'bx-user-check',
        'route' => route('user.interview.expert'),
    ],
];

$config = $popupConfig[$nextStep] ?? null;
@endphp

@if($config)
<div class="modal fade" id="nextStepModal" tabindex="-1" aria-labelledby="nextStepModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="mb-3">
                    <i class="bx {{ $config['icon'] }} {{ $config['iconColor'] }}" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-3">{{ $config['title'] }}</h5>
                <p class="text-muted mb-4">{{ $config['message'] }}</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Maybe Later
                    </button>
                    <a href="{{ $config['route'] }}" class="btn btn-primary">
                        <i class="bx {{ $config['buttonIcon'] }} me-1"></i>
                        {{ $config['buttonText'] }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Show modal immediately
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('nextStepModal'));
        modal.show();
    });
</script>
@endif
