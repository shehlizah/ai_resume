<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>JobSease - The Global Home of Employment</title>
  

</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="container">
            <div class="logo">

            <a class="navbar-brand" href="https://www.jobsease.com">
                <img src="https://www.jobsease.com/assets/img/logo.png" alt="Logo">
        </a>
        </div>
            <ul class="nav-links">
                <li><a href="https://jobsease.com">Home</a></li>
                <li><a href="{{route('user.resumes') }}">Create CV</a></li>
                <li><a href="{{route('user.jobs.recommended') }}">Upload CV</a></li>
                <li><a href="{{route('user.interview.prep') }}">Prepare Interview</a></li>
                <li><a href="#contact">Contact</a></li>

            </ul>
            <div class="nav-buttons">
                <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
            </div>
        </nav>
    </header>


     <script>
// // Disable right click
// document.addEventListener('contextmenu', event => event.preventDefault());

// // Disable keys (F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S)
// document.onkeydown = function(e) {
//     if (e.keyCode == 123) return false; // F12
//     if (e.ctrlKey && e.shiftKey && e.keyCode == 73) return false; // Ctrl+Shift+I
//     if (e.ctrlKey && e.keyCode == 85) return false; // Ctrl+U
//     if (e.ctrlKey && e.keyCode == 83) return false; // Ctrl+S
// };

// // Detect DevTools (advanced)
// (function() {
//     const threshold = 160;
//     const check = function() {
//         const widthThreshold = window.outerWidth - window.innerWidth > threshold;
//         const heightThreshold = window.outerHeight - window.innerHeight > threshold;

//         if (widthThreshold || heightThreshold) {
//             document.body.innerHTML = "<h2 style='text-align:center;margin-top:50px;'>DevTools is not allowed</h2>";
//         }
//     };
//     setInterval(check, 500);
// })();
</script>

