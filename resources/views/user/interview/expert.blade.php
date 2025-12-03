@section('title', __('Book Expert Interview Session'))
<x-layouts.app :title="__('Book Expert Session')">
    <div class="row g-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <h4 class="text-white mb-2">
                        <i class="bx bx-user-check me-2"></i> Book Expert Interview Session
                    </h4>
                    <p class="text-white mb-0 opacity-90">
                        Get personalized coaching from industry experts
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Calendly Integration -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <!-- Calendly inline widget begin -->
                            <div class="calendly-inline-widget"
                                 data-url="https://calendly.com/shehlizah?hide_gdpr_banner=1"
                                 style="min-width:320px;height:700px;">
                            </div>
                            <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
                            <!-- Calendly inline widget end -->
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- What You'll Get -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0">
                                <i class="bx bx-check-circle me-1"></i> What You'll Get
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="ps-3 small">
                                <li class="mb-2">1-on-1 coaching session</li>
                                <li class="mb-2">Personalized feedback</li>
                                <li class="mb-2">Resume review tips</li>
                                <li class="mb-2">Interview strategy</li>
                                <li class="mb-2">Career guidance</li>
                                <li>Follow-up email summary</li>
                            </ul>
                        </div>
                    </div>

                    <!-- How It Works -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0">
                                <i class="bx bx-info-circle me-1"></i> How It Works
                            </h6>
                        </div>
                        <div class="card-body small">
                            <div class="mb-3">
                                <div class="badge bg-primary mb-2">1</div>
                                <p>Select a date and time that works for you</p>
                            </div>
                            <div class="mb-3">
                                <div class="badge bg-primary mb-2">2</div>
                                <p>Fill in your details and confirm booking</p>
                            </div>
                            <div class="mb-0">
                                <div class="badge bg-primary mb-2">3</div>
                                <p>Join the video call at the scheduled time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
