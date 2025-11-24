<x-layouts.app :title="$addOn->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.add-ons.index') }}">Add-Ons</a></li>
                <li class="breadcrumb-item active">{{ $addOn->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="bx {{ $addOn->icon ?? 'bx-gift' }}" style="font-size: 4rem; color: #6366f1;"></i>
                        </div>

                        <h2 class="mb-3">{{ $addOn->name }}</h2>
                        <p class="lead text-muted mb-4">{{ $addOn->description }}</p>

                        @if($hasPurchased)
                            <div class="alert alert-success">
                                <i class="bx bx-check-circle me-2"></i>
                                <strong>You own this add-on!</strong> Click below to access your content.
                            </div>
                        @endif

                        <h4 class="mb-3">Features & Benefits</h4>
                        @if($addOn->features)
                            <div class="row">
                                @foreach($addOn->features as $feature)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex">
                                            <i class="bx bx-check-circle text-success me-2 mt-1"></i>
                                            <span>{{ $feature }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($addOn->type === 'job_links')
                            <div class="alert alert-info mt-4">
                                <h6 class="mb-2"><i class="bx bx-info-circle me-1"></i> About Job Links Directory</h6>
                                <p class="mb-0 small">
                                    Get instant access to a curated list of verified job boards and employment websites. 
                                    Find opportunities across multiple platforms in one place. Perfect for expanding your job search reach!
                                </p>
                            </div>
                        @elseif($addOn->type === 'interview_prep')
                            <div class="alert alert-info mt-4">
                                <h6 class="mb-2"><i class="bx bx-info-circle me-1"></i> About Interview Preparation Kit</h6>
                                <p class="mb-0 small">
                                    Master your next interview with our comprehensive preparation materials. 
                                    Includes common questions, answer strategies, and expert tips to help you stand out!
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="text-primary mb-1">${{ number_format($addOn->price, 2) }}</h3>
                            <small class="text-muted">One-time purchase</small>
                        </div>

                        @if($hasPurchased)
                            <div class="d-grid gap-2">
                                <a href="{{ route('user.add-ons.access', $addOn) }}" class="btn btn-success btn-lg">
                                    <i class="bx bx-lock-open me-1"></i> Access Now
                                </a>
                                <a href="{{ route('user.add-ons.my-add-ons') }}" class="btn btn-outline-secondary">
                                    View My Add-Ons
                                </a>
                            </div>
                        @else
                            <div class="d-grid gap-2">
                                <a href="{{ route('user.add-ons.checkout', $addOn) }}" class="btn btn-primary btn-lg">
                                    <i class="bx bx-cart me-1"></i> Purchase Now
                                </a>
                                <a href="{{ route('user.add-ons.index') }}" class="btn btn-outline-secondary">
                                    Browse Other Add-Ons
                                </a>
                            </div>
                        @endif

                        <div class="border-top mt-4 pt-4">
                            <h6 class="mb-3">Why Choose This Add-On?</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="bx bx-check text-success me-1"></i> Instant access
                                </li>
                                <li class="mb-2">
                                    <i class="bx bx-check text-success me-1"></i> Lifetime access
                                </li>
                                <li class="mb-2">
                                    <i class="bx bx-check text-success me-1"></i> Money-back guarantee
                                </li>
                                <li class="mb-2">
                                    <i class="bx bx-check text-success me-1"></i> Regular updates
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>