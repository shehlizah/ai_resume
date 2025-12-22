<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- Left Panel - Modern Gradient -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-3 p-sm-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden; min-height: 100vh;">
      <!-- Decorative elements -->
      <div style="position: absolute; top: -50px; right: -100px; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,255,255,0.1);"></div>
      <div style="position: absolute; bottom: -80px; left: -50px; width: 400px; height: 400px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>

      <!-- Logo absolute top-left -->
      <div style="position: absolute; top: 0; left: 0; padding: 0.75rem 1rem; z-index: 2;">
        <a href="{{ url('/') }}" class="navbar-brand">
          <img
            src="{{ asset('assets/img/logo.png') }}"
            alt="Logo"
            style="max-width: clamp(100px, 15vw, 130px);"
          >
        </a>
      </div>

      <div class="w-100" style="position: relative; z-index: 1; padding: 3.5rem 1.5rem; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
        @if (request()->routeIs('login'))
          <div class="text-center">
            <div style="max-width: clamp(300px, 40vw, 520px); margin: 0 auto;">
              <picture>
                <source media="(max-width: 575.98px)" srcset="{{ asset('assets/img/illustrations/internal_mockup.png') }}">
                <img src="{{ asset('assets/img/illustrations/internal_mockup.png') }}"
                     alt="App mockup"
                     loading="lazy"
                     style="width:100%; height:auto; display:block; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);"
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/boy-with-rocket-light.png') }}'">
              </picture>
            </div>
          </div>
        @else
          <div class="text-center text-white">
            <!-- Value Proposition with spacing -->
            <div class="mt-0 pt-0">
              <span class="badge bg-white text-dark fw-bold mb-2 d-inline-block px-2 py-1" style="font-size: clamp(0.75rem, 2vw, 0.95rem); border-radius: 20px;">Why Join</span>

              <h2 class="text-white fw-bold mb-3" style="font-size: clamp(1.25rem, 5vw, 1.75rem); line-height: 1.3;">Get job-ready faster with AI-powered CV, interview practice, and job matching</h2>

              <p class="text-white opacity-85 mb-4" style="font-size: clamp(0.9rem, 3vw, 1.05rem); line-height: 1.5;">Create your CV, practice interviews, and find matching jobs — all in one place.</p>

              <div class="d-flex flex-column gap-2 gap-sm-3 align-items-start ms-1 ms-sm-2">
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                  <div class="d-flex align-items-center justify-content-center" style="width: clamp(28px, 5vw, 32px); height: clamp(28px, 5vw, 32px); background: rgba(255,255,255,0.25); border-radius: 50%; flex-shrink: 0;">
                    <i class="bx bx-check text-white" style="font-size: clamp(1rem, 2.5vw, 1.4rem); font-weight: bold;"></i>
                  </div>
                  <span class="text-white" style="font-size: clamp(0.85rem, 2.5vw, 1.05rem);">Create a professional CV in minutes</span>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                  <div class="d-flex align-items-center justify-content-center" style="width: clamp(28px, 5vw, 32px); height: clamp(28px, 5vw, 32px); background: rgba(255,255,255,0.25); border-radius: 50%; flex-shrink: 0;">
                    <i class="bx bx-check text-white" style="font-size: clamp(1rem, 2.5vw, 1.4rem); font-weight: bold;"></i>
                  </div>
                  <span class="text-white" style="font-size: clamp(0.85rem, 2.5vw, 1.05rem);">Practice interviews with AI feedback</span>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                  <div class="d-flex align-items-center justify-content-center" style="width: clamp(28px, 5vw, 32px); height: clamp(28px, 5vw, 32px); background: rgba(255,255,255,0.25); border-radius: 50%; flex-shrink: 0;">
                    <i class="bx bx-check text-white" style="font-size: clamp(1rem, 2.5vw, 1.4rem); font-weight: bold;"></i>
                  </div>
                  <span class="text-white" style="font-size: clamp(0.85rem, 2.5vw, 1.05rem);">Find jobs matching your skills & location</span>
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>

    <!-- Right Panel - Form Card -->
    <div class="card col-12 col-lg-5 col-xl-4" style="border-radius: 0; border: none; min-height: 100vh;">
      <div class="d-flex align-items-center authentication-bg p-3 p-sm-5 p-md-6 p-lg-6 p-xl-7 h-100">
        <div class="w-100 mx-auto" style="max-width: 520px;">
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>

  <style>
    @media (max-width: 991.98px) {
      .authentication-wrapper .card {
        border-radius: 0 !important;
        min-height: auto;
      }
      .authentication-inner {
        flex-wrap: wrap;
      }
    }
  </style>
</div>
