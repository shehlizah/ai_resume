<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed" data-base-url="{{url('/')}}" data-framework="laravel">
  @section('title', __('Welcome'))
  <head>
    @include('partials.head')
  </head>
  <body>
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-end">
          @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary me-2">Log in</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endif
            @endauth
          @endif
      </div>
      <div class="position-absolute top-50 start-50 translate-middle">
        <div class="card">
          <div class="row g-0">
            <div class="col-md-6 d-flex align-items-center">
              <div class="card-body">
                <h1 class="h4 card-title">Jobsease - AI Career Platform</h1>
                <p class="card-text mb-5">Your complete AI-powered career solution. Create resumes, generate cover letters, find jobs, and ace interviews with AI assistance.</p>
                <ul class="mb-0">
                  <li class="mb-3">Read the Laravel <a href="https://laravel.com/docs" target="_blank">Documentation</a></li>
                  <li class="mb-3">Read the Jobsease <a href="https://jobsease.com/docs" target="_blank">Documentation</a></li>
                  <li>Explore <a href="https://jobsease.com/features" target="_blank">Features</a></li>
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <img class="card-img card-img-right" src="{{asset('assets/img/illustrations/laravel-livewire-sneat.png')}}" alt="Card image">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Include Scripts -->
    @include('partials.scripts')
    <!-- / Include Scripts -->
  </body>
</html>
