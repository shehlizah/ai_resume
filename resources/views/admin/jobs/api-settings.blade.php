@extends('components.layouts.admin')

@section('title', 'Job Finder - API Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Job Finder - API Settings</h4>
            <p class="text-muted mb-0">Configure API integrations and job search limits</p>
        </div>
        <div>
            <a href="{{ route('admin.jobs.user-activity') }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i>Back to Activity
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
        <!-- OpenAI Settings -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-brain me-2'></i>OpenAI Configuration</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.jobs.update-api-settings') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">OpenAI Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="openai_enabled"
                                    id="openai_enabled" {{ $settings['openai_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="openai_enabled">
                                    Enable OpenAI job generation
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="text" class="form-control" value="{{ $settings['openai_key'] }}" disabled>
                            <small class="text-muted">Configure in .env file (OPENAI_API_KEY)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class='bx bx-info-circle me-2'></i>
                            <strong>Note:</strong> OpenAI API key must be configured in your .env file for job recommendations to work.
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Job Limits -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-slider me-2'></i>Job Search Limits</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.jobs.update-api-settings') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Free Tier Limit</label>
                            <input type="number" class="form-control" name="job_limit_free"
                                value="{{ $settings['job_limit_free'] }}" min="1" max="20">
                            <small class="text-muted">Jobs shown per search for free users</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Premium Tier Limit</label>
                            <input type="number" class="form-control" name="job_limit_premium"
                                value="{{ $settings['job_limit_premium'] }}" min="1" max="50">
                            <small class="text-muted">Jobs shown per search for premium users</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Session Limits</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="session_limit_enabled"
                                    id="session_limit_enabled" {{ $settings['session_limit_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="session_limit_enabled">
                                    Enable session-based limits (5 views, 1 apply per session)
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-save me-1'></i>Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- API Status -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-info-circle me-2'></i>Current Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx bx-check-circle text-success' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">OpenAI</h6>
                                <small class="text-muted">{{ $settings['openai_enabled'] ? 'Enabled' : 'Disabled' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx bx-user text-info' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">Free Tier</h6>
                                <small class="text-muted">{{ $settings['job_limit_free'] }} jobs/search</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx bx-crown text-warning' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">Premium</h6>
                                <small class="text-muted">{{ $settings['job_limit_premium'] }} jobs/search</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <i class='bx bx-time text-primary' style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-0">Session Limits</h6>
                                <small class="text-muted">{{ $settings['session_limit_enabled'] ? 'Active' : 'Inactive' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
