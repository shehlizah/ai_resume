{{-- resources/views/layouts/navbar/default.blade.php --}}
<nav
  class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
  id="layout-navbar">

      <!-- Logo for mobile -->
      <div class="navbar-brand navbar-brand-autodark d-xl-none" id="mobile-logo">
        <a href="{{ Auth::user()?->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">
          <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="height: 35px;">
        </a>
      </div>

      <button id="menu-toggle" class="btn d-xl-none">
 {{-- SVG so we don't depend on Boxicons --}}
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <rect x="3" y="5" width="18" height="2" rx="1"></rect>
        <rect x="3" y="11" width="18" height="2" rx="1"></rect>
        <rect x="3" y="17" width="18" height="2" rx="1"></rect>
      </svg>
      </button>

      <!-- Language Switcher for mobile -->
      <div class="d-xl-none mobile-lang-switcher">
        @include('partials.language-switcher')
      </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end order-2 flex-shrink-0" id="navbar-collapse">

    {{-- SEARCH REMOVED. If you had a search include/component, delete it. --}}

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      {{-- Language Switcher --}}
      @include('partials.language-switcher')

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
        justify-content: flex-start !important;
        position: relative !important;
        gap: 6px !important;
        padding: 0.5rem 0.75rem !important;
      }

      #mobile-logo {
        order: 1 !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
      }

      #menu-toggle {
        order: 2 !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
        padding: 0.25rem !important;
      }

      .mobile-lang-switcher {
        order: 3 !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
      }

      .mobile-lang-switcher .language-switcher-item {
        margin: 0 !important;
      }

      .mobile-lang-switcher .language-dropdown {
        z-index: 9999 !important;
      }

      .navbar-nav-right {
        order: 4 !important;
        flex-shrink: 0 !important;
        width: auto !important;
        margin-left: 6px !important;
      }

      .navbar-nav-right .navbar-nav {
        gap: 6px !important;
      }

      .navbar-nav-right .language-switcher-item {
        display: none !important;
      }

      .navbar-nav {
        width: auto !important;
      }
    }
  </style>
</nav>
