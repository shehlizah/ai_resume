<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- Left Panel - Modern Gradient -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
      <!-- Decorative elements -->
      <div style="position: absolute; top: -50px; right: -100px; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,255,255,0.1);"></div>
      <div style="position: absolute; bottom: -80px; left: -50px; width: 400px; height: 400px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
      
      <div class="w-100 d-flex justify-content-center" style="position: relative; z-index: 1;">
        <div class="text-center text-white">
          <!-- Logo -->
          <div class="app-brand auth-cover-brand mb-5">
            <a href="{{ url('/') }}" class="navbar-brand">
              <img
                src="{{ asset('assets/img/logo.png') }}"
                alt="Logo"
                style="max-width: 160px;"
              >
            </a>
          </div>

          <!-- Value Proposition -->
          <div class="mt-5">
            <span class="badge bg-white bg-opacity-20 text-white fw-semibold mb-3 d-inline-block">Why join</span>
            <h3 class="text-white fw-bold mb-3">Get job-ready faster with AI-powered CV, interview practice, and job matching</h3>
            <p class="text-white opacity-90 mb-4">Create your CV, practice interviews, and find matching jobs — all in one place.</p>
            
            <div class="d-flex flex-column gap-2 align-items-start ms-3">
              <div class="d-flex align-items-center gap-2">
                <i class="bx bx-check-circle text-white" style="font-size: 1.3rem;"></i>
                <span class="text-white">Create a professional CV in minutes</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="bx bx-check-circle text-white" style="font-size: 1.3rem;"></i>
                <span class="text-white">Practice interviews with AI feedback</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="bx bx-check-circle text-white" style="font-size: 1.3rem;"></i>
                <span class="text-white">Find jobs matching your skills & location</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel - Form Card -->
    <div class="card col-12 col-lg-5 col-xl-4">
      <div class="d-flex align-items-center authentication-bg p-sm-12 p-6 h-100">
        <div class="w-px-400 mx-auto mt-sm-12 mt-8">
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</div>
