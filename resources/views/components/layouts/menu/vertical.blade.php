@php
    $user = auth()->user();
@endphp


<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>



    {{-- Mobile close button --}}
    <!--<button id="menu-close"-->
    <!--        type="button"-->
    <!--        class="btn p-0 border-0 bg-transparent text-large ms-auto d-xl-none"-->
    <!--        aria-label="Close sidebar">-->
    <!--  <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">-->
    <!--    <path d="M18.3 5.71a1 1 0 0 0-1.41 0L12 10.59 7.11 5.7A1 1 0 0 0 5.7 7.11L10.59 12l-4.9 4.89a1 1 0 1 0 1.41 1.41L12 13.41l4.89 4.9a1 1 0 0 0 1.41-1.41L13.41 12l4.9-4.89a1 1 0 0 0-.01-1.4Z"/>-->
    <!--  </svg>-->
    <!--</button>-->

   </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    {{-- 🔹 ADMIN MENU --}}
    @if($user && $user->role === 'admin')

      <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.dashboard') }}">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div>{{ __('Dashboard') }}</div>
        </a>
      </li>

      {{-- Templates --}}
      <li class="menu-item {{ request()->is('admin/templates*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-layout"></i>
          <div class="text-truncate">{{ __('Templates') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/templates') && !request()->is('admin/templates/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/templates') }}">{{ __('All Templates') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/templates/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/templates/create') }}">{{ __('Add New') }}</a>
          </li>
        </ul>
      </li>

      {{-- AI Prompts --}}
      <li class="menu-item {{ request()->is('admin/prompts*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-brain"></i>
          <div>{{ __('AI Prompts') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/prompts') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/prompts') }}">{{ __('Prompt Library') }}</a>
          </li>
        </ul>
      </li>

      {{-- Jobs Sources --}}
      <li class="menu-item {{ request()->is('admin/jobs*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-briefcase"></i>
          <div>{{ __('Job Sources') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/jobs') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/jobs') }}">{{ __('All Job Sources') }}</a>
          </li>
        </ul>
      </li>

      {{-- Ads Management --}}
      <li class="menu-item {{ request()->is('admin/ads*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-rectangle"></i>
          <div>{{ __('Ad Settings') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/ads') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/ads') }}">{{ __('Manage Ad Slots') }}</a>
          </li>
        </ul>
      </li>

      {{-- User Management --}}
      <li class="menu-item {{ request()->is('admin/users*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div>{{ __('User Management') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/users') && !request()->is('admin/users/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/users') }}">{{ __('All Users') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/users/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/users/create') }}">{{ __('Add New User') }}</a>
          </li>
        </ul>
      </li>

       <li class="menu-item {{ request()->is('admin/subscription-plans*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-credit-card"></i>
          <div>{{ __('Manage Plans') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/subscription-plans') && !request()->is('admin/subscription-plans/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/subscription-plans') }}">{{ __('All Plans') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/subscription-plans/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/subscription-plans/create') }}">{{ __('Add New Plans') }}</a>
          </li>
        </ul>
      </li>


      {{-- Settings --}}
      <li class="menu-item {{ request()->is('settings*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-cog"></i>
          <div>{{ __('Settings') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.profile') }}">{{ __('Profile') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.password') }}">{{ __('Password') }}</a>
          </li>
        </ul>
      </li>

    {{-- 🔹 USER MENU --}}
    @elseif($user && $user->role === 'user')

      <li class="menu-item {{ request()->is('user.dashboard') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('user.dashboard') }}">
          <i class="menu-icon tf-icons bx bx-home"></i>
          <div>{{ __('Dashboard') }}</div>
        </a>
      </li>

      <!-- Current Subscription Package -->
      @php
        $activeSubscription = $user->activeSubscription;
        $currentPlan = $activeSubscription?->plan ?? null;
      @endphp

      @if($currentPlan)
      <li class="menu-item">
        <div class="menu-item-label px-3 py-2 border-bottom">
          <small class="text-muted fw-semibold d-block mb-2">{{ __('Current Plan') }}</small>
          <span class="badge bg-primary">{{ $currentPlan->name }}</span>
          @if($activeSubscription)
            <small class="d-block mt-1 text-muted">
              <i class="bx bx-calendar-check"></i>
              {{ $activeSubscription->end_date?->format('M d, Y') ?? 'Active' }}
            </small>
          @endif
        </div>
      </li>
      @else
      <li class="menu-item">
        <div class="menu-item-label px-3 py-2 border-bottom">
          <small class="text-muted fw-semibold d-block mb-2">{{ __('No Active Plan') }}</small>
          <a href="{{ route('packages') }}" class="badge bg-warning text-dark" style="text-decoration: none;">
            Upgrade Now
          </a>
        </div>
      </li>
      @endif

      <li class="menu-item {{ request()->is('resumes*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-file"></i>
          <div>{{ __('Resume Builder') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('resumes') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('resumes') }}">{{ __('My Resumes') }}</a>
          </li>
          <li class="menu-item {{ request()->is('resumes/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('resumes/create') }}">{{ __('Create New') }}</a>
          </li>
        </ul>
      </li>

     <li class="menu-item {{ request()->is('user/cover-letters*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-envelope"></i>
        <div>{{ __('Cover Letters') }}</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->is('user/cover-letters') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('user.cover-letters.index') }}">{{ __('My Cover Letters') }}</a>
        </li>
        <li class="menu-item {{ request()->is('user/cover-letters/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('user.cover-letters.create') }}">{{ __('Create New') }}</a>
        </li>
    </ul>
</li>

      <li class="menu-item {{ request()->is('jobs*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-briefcase"></i>
          <div>{{ __('Job Discovery') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('jobs') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('jobs') }}">{{ __('Find Jobs') }}</a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ request()->is('interview*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-chat"></i>
          <div>{{ __('Interview Prep') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('interview') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('interview') }}">{{ __('Mock Interview') }}</a>
          </li>
        </ul>
      </li>


     <li class="menu-item {{ request()->is('interview*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-credit-card"></i>
          <div>{{ __('Subscriptions') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('subscription/dashboard') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('subscription/dashboard') }}">{{ __('My Subscriptions') }}</a>
          </li>

           <li class="menu-item {{ request()->is('pricing') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('pricing') }}">{{ __('View Plans') }}</a>
          </li>
        </ul>
      </li>


      <li class="menu-item {{ request()->is('settings*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-cog"></i>
          <div>{{ __('Settings') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.profile') }}">{{ __('Profile') }}</a>
          </li>
          <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.password') }}">{{ __('Password') }}</a>
          </li>
        </ul>
      </li>

    {{-- 🔹 Guest Menu --}}
    @else
      <li class="menu-item">
        <a class="menu-link" href="{{ route('login') }}">
          <i class="menu-icon tf-icons bx bx-log-in"></i>
          <div>{{ __('Login') }}</div>
        </a>
      </li>
    @endif

  </ul>

</aside>
<!-- / Menu -->

<style>
/* Base accordion */
.menu-item .menu-sub {
    max-height: 0;
    overflow: hidden;
    transition: max-height .3s ease;
}

.menu-item.open > .menu-sub {
    max-height: 500px;
}

/* Mobile Sidebar Behavior */
@media (max-width:1199.98px){
  #layout-menu{
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    max-width: 85%;
    height: 100vh;
    background: #fff;
    transform: translateX(-100%);
    transition: transform .3s ease-in-out;
    z-index: 1100;
    overflow-y: auto;
  }

  body.layout-menu-expanded #layout-menu{
    transform: translateX(0);
  }

  body.layout-menu-expanded::before{
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 1090;
  }
}

/* Remove link text selection flicker */
.menu-link {
    user-select: none;
}
</style>


<script>
(function () {

  function initAccordion() {
    document.querySelectorAll('#layout-menu .menu-toggle').forEach((toggle) => {

      if (toggle.dataset.bound === '1') return;
      toggle.dataset.bound = '1';

      toggle.addEventListener('click', function (e) {
        e.preventDefault();

        const parent = this.closest('.menu-item');
        const menu = parent.parentNode;

        // Close other open items (Sneat accordion behavior)
        menu.querySelectorAll('.menu-item.open').forEach(item => {
          if (item !== parent) item.classList.remove('open');
        });

        // Toggle current
        parent.classList.toggle('open');
      });
    });
  }

  function initMobileSidebar() {
    const body = document.body;
    const sidebar = document.querySelector('#layout-menu');
    const openBtn = document.querySelector('#menu-toggle');
    const closeBtn = document.querySelector('#menu-close');

    const open = () => body.classList.add('layout-menu-expanded');
    const close = () => body.classList.remove('layout-menu-expanded');

    openBtn && openBtn.addEventListener('click', (e)=>{ e.preventDefault(); open(); });
    closeBtn && closeBtn.addEventListener('click', (e)=>{ e.preventDefault(); close(); });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!body.classList.contains('layout-menu-expanded')) return;
      if (e.target.closest('#layout-menu') || e.target.closest('#menu-toggle')) return;
      close();
    });

    // ESC closes
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initAccordion();
    initMobileSidebar();
  });

})();
</script>

<script>
(function () {

  function initAccordion() {
    // Select all menu-toggle links dynamically
    document.querySelectorAll('#layout-menu .menu-toggle').forEach((toggle) => {

      // Prevent double-binding
      if (toggle.dataset.bound === '1') return;
      toggle.dataset.bound = '1';

      toggle.addEventListener('click', function (e) {
        e.preventDefault();

        const parent = this.closest('.menu-item');
        const menu = parent.parentNode;

        // Close other open items in the same menu
        menu.querySelectorAll('.menu-item.open').forEach(item => {
          if (item !== parent) item.classList.remove('open');
        });

        // Toggle current item
        parent.classList.toggle('open');
      });
    });
  }

  function initMobileSidebar() {
    const body = document.body;
    const sidebar = document.querySelector('#layout-menu');
    const openBtn = document.querySelector('#menu-toggle');
    const closeBtn = document.querySelector('#menu-close');

    const open = () => body.classList.add('layout-menu-expanded');
    const close = () => body.classList.remove('layout-menu-expanded');

    openBtn && openBtn.addEventListener('click', (e)=>{ e.preventDefault(); open(); });
    closeBtn && closeBtn.addEventListener('click', (e)=>{ e.preventDefault(); close(); });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!body.classList.contains('layout-menu-expanded')) return;
      if (e.target.closest('#layout-menu') || e.target.closest('#menu-toggle')) return;
      close();
    });

    // ESC closes
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  }

  function initMenu() {
    initAccordion();
    initMobileSidebar();
  }

  // Ensure this runs after menu is in DOM
  if (document.readyState === "loading") {
    document.addEventListener('DOMContentLoaded', initMenu);
  } else {
    initMenu();
  }

})();
</script>
