<!-- ====== Header Start ====== -->
<header class="ud-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <nav class="navbar navbar-expand-lg">
          <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('frontend/assets/images/logo/logo.svg') }}" alt="Logo" />
          </a>
          <button class="navbar-toggler">
            <span class="toggler-icon"> </span>
            <span class="toggler-icon"> </span>
            <span class="toggler-icon"> </span>
          </button>

          <div class="navbar-collapse">
            <ul id="nav" class="navbar-nav mx-auto">
              <li class="nav-item">
                <a class="ud-menu-scroll" href="#home">Home</a>
              </li>

              <li class="nav-item">
                <a class="ud-menu-scroll" href="#about">About</a>
              </li>
              <li class="nav-item">
                <a class="ud-menu-scroll" href="#pricing">Pricing</a>
              </li>
              <li class="nav-item">
                <a class="ud-menu-scroll" href="#team">Team</a>
              </li>
              <li class="nav-item">
                <a class="ud-menu-scroll" href="#contact">Contact</a>
              </li>
            </ul>
          </div>

          <div class="navbar-btn d-none d-sm-inline-block">
            <a href="{{ route('login') }}" class="ud-main-btn ud-login-btn">
              Sign In
            </a>
            <a class="ud-main-btn ud-white-btn" href="javascript:void(0)">
              Sign Up
            </a>
          </div>
        </nav>
      </div>
    </div>
  </div>
</header>
<!-- ====== Header End ====== -->
