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
            <li><a href="{{ route('register.employer') }}">For Employers</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-buttons">
            @auth
                <a href="{{ auth()->user()->isEmployer() ? route('company.dashboard') : route('user.dashboard') }}" class="btn btn-outline">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
            @endauth
        </div>
    </nav>
</header>

<script>
// // Disable right click
document.addEventListener('contextmenu', event => event.preventDefault());

// Disable keys (F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S)
document.onkeydown = function(e) {
    if (e.keyCode == 123) return false; // F12
    if (e.ctrlKey && e.shiftKey && e.keyCode == 73) return false; // Ctrl+Shift+I
    if (e.ctrlKey && e.keyCode == 85) return false; // Ctrl+U
    if (e.ctrlKey && e.keyCode == 83) return false; // Ctrl+S
};

// Detect DevTools (advanced)
(function() {
    const threshold = 160;
    const check = function() {
        const widthThreshold = window.outerWidth - window.innerWidth > threshold;
        const heightThreshold = window.outerHeight - window.innerHeight > threshold;

        if (widthThreshold || heightThreshold) {
            document.body.innerHTML = "<h2 style='text-align:center;margin-top:50px;'>DevTools is not allowed</h2>";
        }
    };
    setInterval(check, 500);
})();
 </script>

