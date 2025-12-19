<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5" style="background: radial-gradient(circle at 20% 20%, rgba(102,126,234,0.08), transparent 35%), radial-gradient(circle at 80% 30%, rgba(118,75,162,0.08), transparent 40%);">
    <div class="w-100 d-flex justify-content-center">
      <div class="text-center">
        <div class="app-brand auth-cover-brand mb-4">
          <a href="{{ url('/') }}" class="navbar-brand">
              <img
                src="{{ asset('assets/img/logo.png') }}"
                alt="{{ config('app.name') }} logo"
                style="max-width: 180px;"
              >
          </a>
        </div>

        <div class="d-flex flex-column align-items-center gap-3">
            <img
              src="{{ asset('assets/img/illustrations/laravel-livewire-sneat.png') }}"
              class="img-fluid rounded-4 shadow-sm"
              alt="Product UI illustration"
              width="720"
            />
            <img
              src="{{ asset('assets/img/illustrations/boy-with-rocket-light.png') }}"
              class="img-fluid rounded-4 shadow-sm"
              alt="Career growth illustration"
              width="620"
            />
        </div>
      </div>
    </div>
  </div>

    <!-- /Left Text -->

    <!-- Right Text -->
    <div class="card col-12 col-lg-5 col-xl-4">
      <div class="d-flex align-items-center authentication-bg p-sm-12 p-6 h-100">
        <div class="w-px-400 mx-auto mt-sm-12 mt-8">
          {{ $slot }}
        </div>
      </div>
    </div>
    <!-- /Right Text -->
  </div>
</div>
