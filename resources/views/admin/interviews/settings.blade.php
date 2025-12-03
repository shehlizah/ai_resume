@extends('components.layouts.admin')

@section('title', 'Interview Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Interview Prep Settings</h4>
            <p class="text-muted mb-0">Configure AI interview practice settings and limits</p>
        </div>
        <div>
            <a href="{{ route('admin.interviews.sessions') }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i>Back to Sessions
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Session Settings -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-cog me-2'></i>Session Configuration</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.interviews.update-settings') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Max Questions Per Session</label>
                            <input type="number" class="form-control" name="max_questions_per_session"
                                value="{{ $settings['max_questions_per_session'] }}" min="3" max="10">
                            <small class="text-muted">Number of questions in each interview session (3-10)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">AI Generation</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ai_enabled"
                                    id="ai_enabled" {{ $settings['ai_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="ai_enabled">
                                    Enable AI question generation
                                </label>
                            </div>
                            <small class="text-muted">Uses OpenAI to generate dynamic interview questions</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Scoring System</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="scoring_enabled"
                                    id="scoring_enabled" {{ $settings['scoring_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="scoring_enabled">
                                    Enable answer scoring
                                </label>
                            </div>
                            <small class="text-muted">Evaluate answers and provide scores (0-100)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">AI Feedback</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="feedback_enabled"
                                    id="feedback_enabled" {{ $settings['feedback_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="feedback_enabled">
                                    Enable detailed feedback
                                </label>
                            </div>
                            <small class="text-muted">Provide strengths and improvement suggestions</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-save me-1'></i>Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Question Types -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-list-ul me-2'></i>Question Types</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Available interview question categories:</p>

                    @foreach($settings['question_types'] as $type)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ ucfirst($type) }} Questions</h6>
                                <small class="text-muted">
                                    @if($type === 'technical')
                                        Role-specific technical skills and problem-solving
                                    @elseif($type === 'behavioral')
                                        Past experiences and situation handling
                                    @else
                                        Mix of technical and behavioral questions
                                    @endif
                                </small>
                            </div>
                            <span class="badge bg-label-primary">Active</span>
                        </div>
                    </div>
                    @endforeach

                    <div class="alert alert-info mt-3">
                        <i class='bx bx-info-circle me-2'></i>
                        <strong>Note:</strong> Users can select question type when starting an interview session.
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Status -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-info-circle me-2'></i>Current Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx bx-message-dots text-primary' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">Questions</h6>
                                <small class="text-muted">{{ $settings['max_questions_per_session'] }} per session</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx {{ $settings['ai_enabled'] ? 'bx-check-circle text-success' : 'bx-x-circle text-danger' }}' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">AI Generation</h6>
                                <small class="text-muted">{{ $settings['ai_enabled'] ? 'Enabled' : 'Disabled' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx {{ $settings['scoring_enabled'] ? 'bx-check-circle text-success' : 'bx-x-circle text-danger' }}' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">Scoring</h6>
                                <small class="text-muted">{{ $settings['scoring_enabled'] ? 'Active' : 'Inactive' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx {{ $settings['feedback_enabled'] ? 'bx-check-circle text-success' : 'bx-x-circle text-danger' }}' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">Feedback</h6>
                                <small class="text-muted">{{ $settings['feedback_enabled'] ? 'Enabled' : 'Disabled' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
