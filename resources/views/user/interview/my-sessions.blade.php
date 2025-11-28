@section('title', __('My Interview Sessions'))
<x-layouts.app :title="__('My Interview Sessions')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-white mb-2">
                                <i class="bx bx-calendar-check me-2"></i> My Interview Sessions
                            </h4>
                            <p class="text-white mb-0 opacity-90">
                                View and manage your scheduled expert sessions
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('user.interview.expert') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-plus-circle me-1"></i> Book New Session
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions -->
        <div class="col-lg-12">
            @if(count($sessions) > 0)
                @foreach($sessions as $session)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-2">{{ $session['expert_name'] }}</h6>
                                <p class="text-muted small mb-0">
                                    <i class="bx bx-calendar-alt me-1"></i> {{ $session['scheduled_date'] }}
                                </p>
                                <p class="text-muted small">
                                    <i class="bx bx-time me-1"></i> {{ $session['duration'] }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <span class="badge bg-{{ $session['status'] === 'scheduled' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($session['status']) }}
                                </span>
                            </div>
                            <div class="col-md-3 text-md-end">
                                @if($session['status'] === 'scheduled')
                                <a href="{{ $session['zoom_link'] }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="bx bx-video me-1"></i> Join Call
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bx bx-calendar mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                    <h6 class="mb-2">No scheduled sessions</h6>
                    <p class="text-muted small mb-3">Book your first expert interview session to get personalized coaching</p>
                    <a href="{{ route('user.interview.expert') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus-circle me-1"></i> Book Expert Session
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
