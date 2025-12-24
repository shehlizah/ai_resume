{{-- resources/views/layouts/navbar/default.blade.php --}}
<nav
  class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
  id="layout-navbar">

      <button id="menu-toggle" class="btn d-xl-none order-1 flex-shrink-0">
 {{-- SVG so we don't depend on Boxicons --}}
      <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <rect x="3" y="5" width="18" height="2" rx="1"></rect>
        <rect x="3" y="11" width="18" height="2" rx="1"></rect>
        <rect x="3" y="17" width="18" height="2" rx="1"></rect>
      </svg>
      </button>

      <!-- Logo for mobile centered -->
      <div class="navbar-brand navbar-brand-autodark d-xl-none mx-auto flex-grow-1" id="mobile-logo">
        <a href="{{ Auth::user()?->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">
          <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="height: 40px;">
        </a>
      </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end order-2 flex-shrink-0" id="navbar-collapse">

    {{-- SEARCH REMOVED. If you had a search include/component, delete it. --}}

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      {{-- Language Switcher --}}
      <li class="nav-item me-2">
        @include('partials.language-switcher')
      </li>

      {{-- User dropdown (unchanged) --}}
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        @if (Auth::check())
          <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0)" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ route('settings.profile') }}" wire:navigate>
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <small class="text-body-secondary">{{ Auth::user()->role ?? 'User' }}</small>
                  </div>
                </div>
              </a>
            </li>
            <li><div class="dropdown-divider my-1"></div></li>
            <li>
              <a class="dropdown-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}" href="{{ route('settings.profile') }}" wire:navigate>
                <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item {{ request()->routeIs('settings.password') ? 'active' : '' }}" href="{{ route('settings.password') }}" wire:navigate>
                <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
              </a>
            </li>
            <li><div class="dropdown-divider my-1"></div></li>
            <li>
              <form method="POST" action="{{ route('logout') }}"> @csrf
                <button class="dropdown-item" type="submit">
                  <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                </button>
              </form>
            </li>
          </ul>
        @else
          <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0)" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('login') }}">Log In</a></li>
          </ul>
        @endif
      </li>
    </ul>
  </div>

  <style>
    @media (max-width: 1199px) {
      .layout-navbar {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        position: relative !important;
        gap: 0.5rem !important;
      }

      #menu-toggle {
        order: 1 !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
      }

      #mobile-logo {
        flex-grow: 1 !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        margin: 0 !important;
      }

      .navbar-nav-right {
        flex: 0 0 auto !important;
        order: 2 !important;
        width: auto !important;
        margin-left: 0 !important;
      }

      .navbar-nav {
        width: auto !important;
      }
    }
  </style>
</nav>
