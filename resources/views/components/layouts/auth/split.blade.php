<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- Left Panel - Modern Gradient -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden; min-height: 100vh;">
      <!-- Decorative elements -->
      <div style="position: absolute; top: -50px; right: -100px; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,255,255,0.1);"></div>
      <div style="position: absolute; bottom: -80px; left: -50px; width: 400px; height: 400px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>

      <!-- Logo absolute top-left -->
      <div style="position: absolute; top: 0; left: 0; padding: 1rem 1.25rem; z-index: 2;">
        <a href="{{ url('/') }}" class="navbar-brand">
          <img
            src="{{ asset('assets/img/logo.png') }}"
            alt="Logo"
            style="max-width: 130px;"
          >
        </a>
      </div>

      <div class="w-100" style="position: relative; z-index: 1; padding: 3.5rem 2.25rem; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
        <div class="text-center text-white">
          <!-- Value Proposition with spacing -->
          <div class="mt-0 pt-0">
            <span class="badge bg-white text-dark fw-bold mb-3 d-inline-block px-3 py-2" style="font-size: 0.95rem; border-radius: 20px;">Why Join</span>

            <h2 class="text-white fw-bold mb-4" style="font-size: 1.75rem; line-height: 1.4;">Get job-ready faster with AI-powered CV, interview practice, and job matching</h2>

            <p class="text-white opacity-85 mb-5" style="font-size: 1.05rem; line-height: 1.6;">Create your CV, practice interviews, and find matching jobs — all in one place.</p>

            <div class="d-flex flex-column gap-3 align-items-start ms-2">
              <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255,255,255,0.25); border-radius: 50%; flex-shrink: 0;">
                  <i class="bx bx-check text-white" style="font-size: 1.4rem; font-weight: bold;"></i>
                </div>
                <span class="text-white" style="font-size: 1.05rem;">Create a professional CV in minutes</span>
              </div>
              <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255,255,255,0.25); border-radius: 50%; flex-shrink: 0;">
                  <i class="bx bx-check text-white" style="font-size: 1.4rem; font-weight: bold;"></i>
                </div>
                <span class="text-white" style="font-size: 1.05rem;">Practice interviews with AI feedback</span>
              </div>
              <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255,255,255,0.25); border-radius: 50%; flex-shrink: 0;">
                  <i class="bx bx-check text-white" style="font-size: 1.4rem; font-weight: bold;"></i>
                </div>
                <span class="text-white" style="font-size: 1.05rem;">Find jobs matching your skills & location</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel - Form Card -->
    <div class="card col-12 col-lg-5 col-xl-4" style="border-radius: 0; border: none;">
      <div class="d-flex align-items-center authentication-bg p-sm-5 p-3 p-md-6 min-vh-100" style="min-height: auto; @media (max-width: 991.98px) { min-height: 100vh; }">
        <div class="w-px-400 mx-auto mt-sm-8 mt-4 w-100">
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>

  <style>
    @media (max-width: 991.98px) {
      .authentication-wrapper .card {
        border-radius: 0 !important;
      }
    }
  </style>
</div>
