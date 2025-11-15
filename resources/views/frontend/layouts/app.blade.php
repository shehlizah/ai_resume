<!-- =========

	Template Name: Play
	Author: UIdeck
	Author URI: https://uideck.com/
	Support: https://uideck.com/support/
	Version: 1.1

========== -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'AI Resume By HITECHMAIN')</title>

    <!-- Primary Meta Tags -->
<meta name="title" content="@yield('meta_title', 'AI Resume By HITECHMAI')">
<meta name="description" content="@yield('meta_description', 'AI Resume By HITECHMAI')">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="@yield('og_url', 'https://shehlizah.com')">
<meta property="og:title" content="@yield('og_title', 'AI Resume By HITECHMAI')">
<meta property="og:description" content="@yield('og_description', 'AI Resume By HITECHMAI')">
<!--<meta property="og:image" content="@yield('og_image', 'https://uideck.com/wp-content/uploads/2021/09/play-meta-bs.jpg')">-->

<!-- Twitter -->
<!--<meta property="twitter:card" content="summary_large_image">-->
<!--<meta property="twitter:url" content="@yield('twitter_url', 'https://uideck.com/play/')">-->
<!--<meta property="twitter:title" content="@yield('twitter_title', 'Play - Free Open Source HTML Bootstrap Template by UIdeck')">-->
<!--<meta property="twitter:description" content="@yield('twitter_description', 'Play - Free Open Source HTML Bootstrap Template by UIdeck Team')">-->
<!--<meta property="twitter:image" content="@yield('twitter_image', 'https://uideck.com/wp-content/uploads/2021/09/play-meta-bs.jpg')">-->

    <!--====== Favicon Icon ======-->
    <link
      rel="shortcut icon"
      href="{{ asset('frontend/assets/images/favicon.svg') }}"
      type="image/svg"
    />

    <!-- ===== All CSS files ===== -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/ud-styles.css') }}" />
    
    @yield('styles')
  </head>
  <body>
    @include('frontend.partials.header')

    @yield('content')

    @include('frontend.partials.footer')

    <!-- ====== All Javascript Files ====== -->
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>
    
    @yield('scripts')
    
    <script>
      // ==== for menu scroll
      const pageLink = document.querySelectorAll(".ud-menu-scroll");

      pageLink.forEach((elem) => {
        elem.addEventListener("click", (e) => {
          e.preventDefault();
          document.querySelector(elem.getAttribute("href")).scrollIntoView({
            behavior: "smooth",
            offsetTop: 1 - 60,
          });
        });
      });

      // section menu active
      function onScroll(event) {
        const sections = document.querySelectorAll(".ud-menu-scroll");
        const scrollPos =
          window.pageYOffset ||
          document.documentElement.scrollTop ||
          document.body.scrollTop;

        for (let i = 0; i < sections.length; i++) {
          const currLink = sections[i];
          const val = currLink.getAttribute("href");
          const refElement = document.querySelector(val);
          const scrollTopMinus = scrollPos + 73;
          if (
            refElement.offsetTop <= scrollTopMinus &&
            refElement.offsetTop + refElement.offsetHeight > scrollTopMinus
          ) {
            document
              .querySelector(".ud-menu-scroll")
              .classList.remove("active");
            currLink.classList.add("active");
          } else {
            currLink.classList.remove("active");
          }
        }
      }

      window.document.addEventListener("scroll", onScroll);
    </script>
  </body>
</html>
