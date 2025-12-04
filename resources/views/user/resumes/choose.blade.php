<x-layouts.app :title="$title ?? 'Choose Template'">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <h4 class="text-white mb-2">✨ Create Your Professional Resume</h4>
          <p class="mb-0 text-white-50">Choose from our beautiful templates and build your resume in minutes</p>
        </div>
      </div>
    </div>
  </div>

  @if($templates->isEmpty())
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="bx bx-error-circle" style="font-size: 64px; color: #ddd;"></i>
        <h5 class="mt-3 text-muted">No templates available</h5>
        <p class="text-muted">Please check back later or contact support.</p>
      </div>
    </div>
  @else
    <div class="row g-4">
      @foreach($templates as $template)
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 hover-shadow">
            @if($template->preview_image)
              <img src="{{ asset($template->preview_image) }}"
                   class="card-img-top"
                   alt="{{ $template->name }}"
                   style="height: 300px; object-fit: cover;">
            @else
              <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                   style="height: 300px;">
                <i class="bx bx-file" style="font-size: 64px; color: #ddd;"></i>
              </div>
            @endif

            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="card-title mb-0">{{ $template->name }}</h5>
                @if($template->is_premium)
                  <span class="badge bg-label-warning">
                    <i class="bx bx-crown"></i> Premium
                  </span>
                @endif
              </div>

              <p class="card-text text-muted small flex-grow-1">
                {{ Str::limit($template->description, 100) }}
              </p>

              <div class="d-grid gap-2">
                @php
                  $user = auth()->user();
                  $hasPackage = $user && $user->activeSubscription()->exists();
                  $hasPremium = $hasPackage && ($user->activeSubscription->plan->name === 'Premium' || $user->has_lifetime_access);
                  $canUseTemplate = !$template->is_premium || $hasPremium;
                @endphp

                @if($canUseTemplate)
                  <a href="{{ route('user.resumes.fill', $template->id) }}"
                     class="btn btn-primary">
                    <i class="bx bx-edit me-1"></i> Use This Template
                  </a>
                @else
                  <button class="btn btn-primary" disabled>
                    <i class="bx bx-lock me-1"></i> Premium Only
                  </button>
                @endif

                <a href="{{ route('user.resumes.preview', $template->id) }}"
                   target="_blank"
                   class="btn btn-outline-secondary btn-sm">
                  <i class="bx bx-show me-1"></i> Preview with Sample Data
                </a>
              </div>
            </div>

            <div class="card-footer bg-transparent">
              <small class="text-muted">
                <i class="bx bx-time me-1"></i>
                Updated {{ $template->updated_at->diffForHumans() }}
              </small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <style>
    .hover-shadow {
      transition: all 0.3s ease;
    }
    .hover-shadow:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
  </style>
</x-layouts.app>
