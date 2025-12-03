@php
    $user = auth()->user();
@endphp


<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="width: 70%;">

    </a>



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

      <!-- Cover Letters Menu -->
    <!-- Cover Letters Menu with Templates -->
        <li class="menu-item {{ request()->routeIs('admin.cover-letters.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
                <div>{{ __('Cover Letters') }}</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('admin.cover-letters.templates*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.cover-letters.templates') }}">
                        {{ __('Templates') }}
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.cover-letters.user-cover-letters') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.cover-letters.user-cover-letters') }}">
                        {{ __('User Cover Letters') }}
                    </a>
                </li>
            </ul>
        </li>

      {{-- Job Finder Module --}}
      <li class="menu-item {{ request()->is('admin/jobs*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-briefcase"></i>
          <div>{{ __('Job Finder') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/jobs/user-activity') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/jobs/user-activity') }}">{{ __('User Activity') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/jobs/api-settings') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/jobs/api-settings') }}">{{ __('API Settings') }}</a>
          </li>
        </ul>
      </li>

      {{-- Interview Prep Module --}}
      <li class="menu-item {{ request()->is('admin/interviews*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-chat"></i>
          <div>{{ __('Interview Prep') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/interviews/sessions') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/interviews/sessions') }}">{{ __('User Sessions') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/interviews/questions') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/interviews/questions') }}">{{ __('Question Bank') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/interviews/settings') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/interviews/settings') }}">{{ __('Settings') }}</a>
          </li>
        </ul>
      </li>

      <!--{{-- AI Prompts --}}-->
      <!--<li class="menu-item {{ request()->is('admin/prompts*') ? 'active open' : '' }}">-->
      <!--  <a href="javascript:void(0);" class="menu-link menu-toggle">-->
      <!--    <i class="menu-icon tf-icons bx bx-brain"></i>-->
      <!--    <div>{{ __('AI Prompts') }}</div>-->
      <!--  </a>-->
      <!--  <ul class="menu-sub">-->
      <!--    <li class="menu-item {{ request()->is('admin/prompts') ? 'active' : '' }}">-->
      <!--      <a class="menu-link" href="{{ url('admin/prompts') }}">{{ __('Prompt Library') }}</a>-->
      <!--    </li>-->
      <!--  </ul>-->
      <!--</li>-->

      <!--{{-- Jobs Sources --}}-->
      <!--<li class="menu-item {{ request()->is('admin/jobs*') ? 'active open' : '' }}">-->
      <!--  <a href="javascript:void(0);" class="menu-link menu-toggle">-->
      <!--    <i class="menu-icon tf-icons bx bx-briefcase"></i>-->
      <!--    <div>{{ __('Job Sources') }}</div>-->
      <!--  </a>-->
      <!--  <ul class="menu-sub">-->
      <!--    <li class="menu-item {{ request()->is('admin/jobs') ? 'active' : '' }}">-->
      <!--      <a class="menu-link" href="{{ url('admin/jobs') }}">{{ __('All Job Sources') }}</a>-->
      <!--    </li>-->
      <!--  </ul>-->
      <!--</li>-->


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

       <li class="menu-item {{ request()->is('admin/subscriptions*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-credit-card"></i>
          <div>{{ __('Manage Plans') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/subscriptions') && !request()->is('admin/payments') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/subscriptions') }}">{{ __('All Subscriptions') }}</a>
          </li>
          <li class="menu-item {{ request()->is('admin/payments') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('admin/payments') }}">{{ __('Manage Payments') }}</a>
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

      <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('user.dashboard') }}">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div>{{ __('Dashboard') }}</div>
        </a>
      </li>

      <!-- 1. Resume Builder -->
      <li class="menu-item {{ request()->is('resumes*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-file-doc"></i>
          <div>{{ __('Resume Builder') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('resumes') && !request()->is('resumes/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('resumes') }}">{{ __('My Resumes') }}</a>
          </li>
          <li class="menu-item {{ request()->is('resumes/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('resumes/create') }}">{{ __('Create New') }}</a>
          </li>
          <li class="menu-item {{ request()->is('resumes/choose') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('resumes/choose') }}">{{ __('Choose Template') }}</a>
          </li>
        </ul>
      </li>

      <!-- 2. Cover Letters -->
      <li class="menu-item {{ request()->is('cover-letters*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-envelope"></i>
          <div>{{ __('Cover Letters') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('cover-letters') && !request()->is('cover-letters/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('user.cover-letters.index') }}">{{ __('My Cover Letters') }}</a>
          </li>
          <li class="menu-item {{ request()->is('cover-letters/create') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('user.cover-letters.create') }}">{{ __('Create New') }}</a>
          </li>
        </ul>
      </li>

      <!-- 3. Job Finder -->
      <li class="menu-item {{ request()->is('jobs*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-briefcase"></i>
          <div>{{ __('Job Finder') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('jobs/recommended') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('jobs/recommended') }}">{{ __('Recommended') }}</a>
          </li>
          <li class="menu-item {{ request()->is('jobs/by-location') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('jobs/by-location') }}">{{ __('By Location') }}</a>
          </li>
        </ul>
      </li>

      <!-- 4. Interview Preparation -->
      <li class="menu-item {{ request()->is('interview*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-chat"></i>
          <div>{{ __('Interview Prep') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('interview/prep') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('interview/prep') }}">{{ __('AI Interview Prep') }}</a>
          </li>
          <li class="menu-item {{ request()->is('interview/questions') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('interview/questions') }}">{{ __('Practice Questions') }}</a>
          </li>
          <li class="menu-item {{ request()->is('interview/ai-practice') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('interview/ai-practice') }}">{{ __('AI Mock Interview') }}
              <span class="badge badge-center rounded-pill bg-warning ms-2">PRO</span>
            </a>
          </li>
          <li class="menu-item {{ request()->is('interview/expert') ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('interview/expert') }}">{{ __('Book Expert Session') }}
              <span class="badge badge-center rounded-pill bg-danger ms-2">PRO</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- Subscription & Pricing -->
      <li class="menu-item {{ request()->is('subscription*') || request()->is('pricing') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-crown"></i>
          <div>{{ __('Pricing & Plans') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('user.subscription.dashboard') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('user.subscription.dashboard') }}">{{ __('My Subscription') }}</a>
          </li>
          <li class="menu-item {{ request()->is('pricing') || request()->is('user/pricing') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('user.pricing') }}">{{ __('Browse Plans') }}</a>
          </li>
        </ul>
      </li>

      <!-- Settings -->
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
          <li class="menu-item {{ request()->routeIs('settings.monetization') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('settings.monetization') }}">
              {{ __('Monetization') }}
              @if(!auth()->user() || !auth()->user()->subscriptions()->where('status', 'active')->first())
                <span class="badge badge-center rounded-pill bg-warning ms-2">NEW</span>
              @endif
            </a>
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
/* Sidebar Scrollbar */
#layout-menu {
  overflow-y: auto;
  height: 100vh;
}

/* Custom Scrollbar for Sidebar */
#layout-menu::-webkit-scrollbar {
  width: 6px;
}

#layout-menu::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
}

#layout-menu::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

#layout-menu::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}

/* Firefox scrollbar */
#layout-menu {
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.05);
}

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
