<x-layouts.app :title="$title ?? 'Choose Template'">

  <div class="row mb-3">
    <div class="col-12">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <h4 class="text-white mb-2">✨ Create Your Professional Resume</h4>
          <p class="mb-0 text-white-50">Choose from our beautiful templates and build your resume in minutes</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-2">
    <div class="col-12 d-flex flex-wrap align-items-center justify-content-between">
      <div class="mb-2">
        <span class="fw-semibold">Filter:</span>
        <button class="btn btn-sm btn-outline-primary me-1 filter-btn" data-filter="all">All</button>
        <button class="btn btn-sm btn-outline-primary me-1 filter-btn" data-filter="free">Free</button>
        <button class="btn btn-sm btn-outline-primary me-1 filter-btn" data-filter="premium">Premium</button>
        <button class="btn btn-sm btn-outline-secondary me-1 filter-btn" data-filter="modern">Modern</button>
        <button class="btn btn-sm btn-outline-secondary me-1 filter-btn" data-filter="professional">Professional</button>
        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="creative">Creative</button>
      </div>
      <div class="mb-2">
        <small class="text-muted">Not sure which template to choose? <span class="fw-semibold">Start with Professional for corporate roles.</span></small>
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
    <div class="row g-4" id="templateGrid">
      @foreach($templates as $template)
        <div class="col-md-4 mb-4 template-card"
             data-type="{{ $template->is_premium ? 'premium' : 'free' }}"
             data-style="{{ strtolower($template->style ?? 'professional') }}">
          <div class="card h-100 hover-shadow" style="border-radius: 12px; min-height: 420px;">
            @if($template->preview_image)
              <img src="{{ asset($template->preview_image) }}"
                   class="card-img-top template-preview"
                   alt="{{ $template->name }}"
                   style="max-height: 260px; object-fit: contain;">
            @else
              <div class="card-img-top bg-light d-flex align-items-center justify-content-center template-placeholder" style="min-height: 180px;">
                <i class="bx bx-file" style="font-size: 48px; color: #ddd;"></i>
              </div>
            @endif

            <div class="card-body d-flex flex-column" style="padding: 1.1rem;">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="card-title mb-0">{{ $template->name }}
                  @if($loop->first)
                    <span class="badge bg-success ms-2">Recommended</span>
                  @endif
                </h5>
                @if($template->is_premium)
                  <span class="badge bg-label-warning">
                    <i class="bx bx-crown"></i> Premium
                  </span>
                @endif
              </div>

              <p class="card-text text-muted small flex-grow-1">
                {{ Str::limit($template->description, 100) }}
              </p>

              <div class="d-grid gap-2 mt-auto">
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
                  <a href="{{ route('user.pricing') }}"
                     class="btn btn-outline-primary d-flex align-items-center justify-content-center">
                    <i class="bx bx-lock me-1"></i> Upgrade to Use
                  </a>
                @endif
              </div>
            </div>

            <div class="card-footer bg-transparent" style="border-radius: 0 0 12px 12px;">
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
    .template-preview {
      width: 100%;
      height: auto;
      max-height: 260px;
      object-fit: contain;
      background-color: #f8f9fa;
    }
    .template-placeholder {
      min-height: 180px;
      background-color: #f8f9fa;
    }
    .template-card {
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    @media (max-width: 991px) {
      .col-md-4 {
        flex: 0 0 50%;
        max-width: 50%;
      }
    }
    @media (max-width: 768px) {
      .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
      }
      .template-preview {
        max-height: 180px;
      }
      .template-placeholder {
        min-height: 120px;
      }
    }
    .card {
      border-radius: 12px;
    }
    .card-title {
      font-size: 1.1rem;
    }
    .btn-outline-primary {
      border-width: 2px;
      font-weight: 500;
    }
    .btn-primary {
      font-weight: 600;
    }
  </style>

  <script>
    // Filtering logic
    document.addEventListener('DOMContentLoaded', function() {
      const filterBtns = document.querySelectorAll('.filter-btn');
      const cards = document.querySelectorAll('.template-card');
      filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const filter = btn.getAttribute('data-filter');
          filterBtns.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          cards.forEach(card => {
            if (filter === 'all') {
              card.style.display = '';
            } else if (filter === 'free' || filter === 'premium') {
              card.style.display = card.getAttribute('data-type') === filter ? '' : 'none';
            } else {
              card.style.display = card.getAttribute('data-style') === filter ? '' : 'none';
            }
          });
        });
      });
    });
    // Lazy load (simple: hide below 6, show on scroll)
    document.addEventListener('DOMContentLoaded', function() {
      const cards = Array.from(document.querySelectorAll('.template-card'));
      const showCount = 6;
      cards.forEach((card, i) => {
        if (i >= showCount) card.style.display = 'none';
      });
      let revealed = showCount;
      window.addEventListener('scroll', function() {
        if (revealed < cards.length && window.scrollY + window.innerHeight > document.body.offsetHeight - 200) {
          for (let i = revealed; i < Math.min(revealed + 3, cards.length); i++) {
            cards[i].style.display = '';
          }
          revealed += 3;
        }
      });
    });
  </script>
</x-layouts.app>
