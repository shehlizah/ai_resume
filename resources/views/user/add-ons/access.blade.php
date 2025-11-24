<x-layouts.app :title="'Access - ' . $addOn->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.add-ons.my-add-ons') }}">My Add-Ons</a></li>
                    <li class="breadcrumb-item active">{{ $addOn->name }}</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bx {{ $addOn->icon ?? 'bx-gift' }}" style="font-size: 4rem;"></i>
                    <div class="ms-4">
                        <h3 class="text-white mb-2">{{ $addOn->name }}</h3>
                        <p class="mb-0 opacity-75">{{ $addOn->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- AI-Powered Features Banner -->
        <div class="alert alert-success mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2">
                        <i class="bx bx-brain me-2"></i>
                        🚀 New! AI-Powered {{ $addOn->type === 'job_links' ? 'Job Search' : 'Interview Prep' }}
                    </h5>
                    <p class="mb-0">
                        @if($addOn->type === 'job_links')
                            Get personalized job board recommendations based on your specific role and skills using artificial intelligence!
                        @else
                            Get AI-generated interview questions and answers tailored to your specific role and experience level!
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if($addOn->type === 'job_links')
                        <a href="{{ route('user.add-ons.job-search', $addOn) }}" class="btn btn-success">
                            <i class="bx bx-brain me-1"></i> Try AI Job Search
                        </a>
                    @else
                        <a href="{{ route('user.add-ons.interview-prep', $addOn) }}" class="btn btn-warning text-white">
                            <i class="bx bx-brain me-1"></i> Try AI Interview Prep
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                
                @if($addOn->type === 'job_links')
                    <!-- JOB LINKS CONTENT -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bx bx-briefcase me-2"></i>
                                Verified Job Boards & Employment Websites
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Click on any job board below to explore thousands of opportunities. 
                                Each link opens in a new tab so you can browse multiple sites at once!
                            </div>

                            @if($addOn->content && isset($addOn->content['job_sites']))
                                @php
                                    $categories = collect($addOn->content['job_sites'])->groupBy('category');
                                @endphp

                                @foreach($categories as $category => $sites)
                                    <div class="mb-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="bx bx-folder-open me-2"></i>
                                            {{ $category }} Job Sites
                                        </h5>
                                        <div class="row g-3">
                                            @foreach($sites as $site)
                                                <div class="col-md-6">
                                                    <a href="{{ $site['url'] }}" target="_blank" class="text-decoration-none">
                                                        <div class="card border hover-lift">
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-1 text-dark">{{ $site['name'] }}</h6>
                                                                        <small class="text-muted">{{ $site['category'] }}</small>
                                                                    </div>
                                                                    <i class="bx bx-link-external text-primary fs-3"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Quick Access Button -->
                                <div class="alert alert-success mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Pro Tip:</strong> Open multiple sites in tabs and apply to jobs simultaneously!
                                        </div>
                                        <button class="btn btn-success btn-sm" onclick="openAllJobSites()">
                                            <i class="bx bx-world me-1"></i> Open All Sites
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bx bx-error me-2"></i>
                                    Job links content is being updated. Please check back soon!
                                </div>
                            @endif
                        </div>
                    </div>

                @elseif($addOn->type === 'interview_prep')
                    <!-- INTERVIEW PREP CONTENT -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">
                                <i class="bx bx-user-voice me-2"></i>
                                Interview Preparation Resources
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bx bx-trophy me-2"></i>
                                Master your interviews with these comprehensive resources. Take your time to review each section!
                            </div>

                            @if($addOn->content && isset($addOn->content['resources']))
                                @foreach($addOn->content['resources'] as $index => $resource)
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="mb-3">
                                                <span class="badge bg-warning me-2">{{ $index + 1 }}</span>
                                                {{ $resource['title'] }}
                                            </h5>
                                            
                                            @if($resource['type'] === 'questions' && isset($resource['items']))
                                                <ul class="mb-0">
                                                    @foreach($resource['items'] as $item)
                                                        <li class="mb-3">
                                                            <strong>{{ $item }}</strong>
                                                            <p class="text-muted small mb-0">
                                                                ðŸ’¡ Tip: Use the STAR method (Situation, Task, Action, Result) to structure your answer.
                                                            </p>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @elseif($resource['type'] === 'guide')
                                                <div class="alert alert-info mb-0">
                                                    <p class="mb-0">{{ $resource['description'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- External Resources -->
                            @if($addOn->content && isset($addOn->content['external_resources']))
                                <div class="card bg-primary text-white mt-4">
                                    <div class="card-body">
                                        <h5 class="text-white mb-3">
                                            <i class="bx bx-globe me-2"></i>
                                            Additional Learning Resources
                                        </h5>
                                        <div class="row g-3">
                                            @foreach($addOn->content['external_resources'] as $link)
                                                <div class="col-md-6">
                                                    <a href="{{ $link['url'] }}" target="_blank" class="text-decoration-none">
                                                        <div class="card hover-lift">
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0 text-dark">{{ $link['name'] }}</h6>
                                                                    <i class="bx bx-link-external text-primary fs-4"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Purchase Info -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0 text-white">
                            <i class="bx bx-check-shield me-1"></i>
                            Your Purchase
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $userAddOn->getStatusColor() }}">
                                {{ ucfirst($userAddOn->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Purchased On</small>
                            <strong>{{ $userAddOn->purchased_at->format('M d, Y') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Amount Paid</small>
                            <strong class="text-success">${{ number_format($userAddOn->amount_paid, 2) }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Payment Gateway</small>
                            <span class="badge bg-primary">
                                {{ ucfirst($userAddOn->payment_gateway ?? 'N/A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Support Card -->
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="bx bx-help-circle me-1"></i>
                            Need Help?
                        </h6>
                        <p class="text-muted small mb-3">
                            If you have any questions about using this add-on or need support, we're here to help!
                        </p>
                        <div class="d-grid gap-2">
                            <a href="mailto:support@example.com" class="btn btn-outline-info btn-sm">
                                <i class="bx bx-envelope me-1"></i> Contact Support
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('user.add-ons.my-add-ons') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-folder me-1"></i> My Add-Ons
                            </a>
                            <a href="{{ route('user.add-ons.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-package me-1"></i> Browse More Add-Ons
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-home me-1"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-3px);
        }
    </style>

    <script>
        // Function to open all job sites at once
        function openAllJobSites() {
            @if($addOn->type === 'job_links' && $addOn->content && isset($addOn->content['job_sites']))
                @foreach($addOn->content['job_sites'] as $site)
                    window.open('{{ $site['url'] }}', '_blank');
                @endforeach
                alert('Opening all job sites in new tabs! Check your browser for pop-up blockers if tabs don\'t open.');
            @endif
        }
    </script>
</x-layouts.app>