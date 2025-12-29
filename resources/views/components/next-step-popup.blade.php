@props(['nextStep'])

@php
$popupConfig = [
    'cover_letter' => [
        'icon' => 'bx-file-blank',
        'iconColor' => 'text-success',
        'title' => 'Boost Your Chances of Getting Hired',
        'message' => 'Recruiters prefer resumes with a strong cover letter.<br>Create a professional cover letter in seconds using AI.',
        'buttonText' => 'Create Cover Letter with AI',
        'buttonIcon' => 'bx-file-blank',
        'route' => route('user.cover-letters.create'),
    ],
    'interview_prep' => [
        'icon' => 'bx-briefcase',
        'iconColor' => 'text-primary',
        'title' => 'Ace Your Interviews with Confidence',
        'message' => 'Practice real interview questions with AI coaching.<br>Get instant feedback and improve in minutes.',
        'buttonText' => 'Start Interview Prep',
        'buttonIcon' => 'bx-microphone',
        'route' => route('user.interview.prep'),
    ],
    'job_search' => [
        'icon' => 'bx-search-alt',
        'iconColor' => 'text-info',
        'title' => 'Find Roles That Fit You',
        'message' => 'Discover curated jobs matched to your skills.<br>Apply faster with your ready resume and cover letter.',
        'buttonText' => 'Search Jobs',
        'buttonIcon' => 'bx-briefcase',
        'route' => route('user.jobs.recommended'),
    ],
    'book_session' => [
        'icon' => 'bx-chat',
        'iconColor' => 'text-primary',
        'title' => 'Improve Your Resume with Expert Feedback',
        'message' => 'Not getting results? Get expert help.<br>Want feedback on your resume? We can review and refine it.',
        'buttonText' => 'Talk to an Expert',
        'buttonIcon' => 'bx-message-dots',
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
                        Skip for now
                    </button>
                    <a href="{{ $config['route'] }}" class="btn btn-primary px-4 fw-semibold">
                        <i class="bx {{ $config['buttonIcon'] }} me-1"></i>
                        {{ $config['buttonText'] }}
                    </a>
                </div>
                <div class="text-muted small mt-2">⏱ Takes less than 1 minute</div>
            </div>
        </div>
    </div>
</div>

<style>
  /* Slightly reduce popup width and emphasize primary CTA */
  #nextStepModal .modal-dialog { max-width: 520px; }
  @media (max-width: 576px) {
    #nextStepModal .modal-dialog { max-width: 92%; }
  }
  #nextStepModal .btn-primary { box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
  #nextStepModal .btn-outline-secondary { border-width: 1px; }
</style>

<script>
    // Show modal immediately
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('nextStepModal'));
        modal.show();
    });
</script>
@endif
