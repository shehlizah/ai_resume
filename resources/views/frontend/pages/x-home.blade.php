<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Resume Builder | Create Your Perfect Resume</title>
    <meta name="description" content="Create professional resumes in minutes with our easy-to-use resume builder. Choose from premium templates and land your dream job.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Header/Navigation */
        .navbar {
            padding: 1.5rem 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .navbar-brand i {
            font-size: 2rem;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .btn-outline-primary-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-primary-custom:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-section p {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 3rem;
            max-width: 700px;
        }

        /* Main CTA Button */
        .btn-cta {
            background: white;
            color: var(--primary-color);
            padding: 1.5rem 4rem;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 16px;
            border: none;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .btn-cta:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            color: var(--primary-color);
        }

        .btn-cta i {
            font-size: 2rem;
        }

        /* Features Pills */
        .feature-pills {
            display: flex;
            gap: 1.5rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .feature-pill {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-pill i {
            font-size: 1.5rem;
        }

        /* Footer */
        .footer {
            background: #1e293b;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        /* User Stats Badge */
        .stats-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }

            .hero-section p {
                font-size: 1.125rem;
            }

            .btn-cta {
                padding: 1.25rem 2.5rem;
                font-size: 1.25rem;
            }

            .feature-pills {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class='bx bxs-file-doc'></i>
                <span>ResumeBuilder</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <!-- Logged-in User Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}">
                                <i class='bx bx-home-alt me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.resumes.index') }}">
                                <i class='bx bx-file me-1'></i> My Resumes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.cover-letters.index') }}">
                                <i class='bx bx-envelope me-1'></i> Cover Letters
                            </a>
                        </li>
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle me-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-primary fw-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                </div>
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('settings.profile') }}"><i class='bx bx-user me-2'></i>Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.subscription.dashboard') }}"><i class='bx bx-credit-card me-2'></i>Subscription</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-log-out me-2'></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- Guest Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('packages') }}">Pricing</a>
                        </li>
                        <li class="nav-item ms-3">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary-custom">
                                Sign In
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="{{ route('register') }}" class="btn btn-primary-custom">
                                Sign Up
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Main CTA -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="hero-content text-center">
                        @auth
                            <!-- For Logged-in Users -->
                            <div class="stats-badge">
                                <i class='bx bx-user-check'></i>
                                <span>Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}!</span>
                            </div>
                            
                            <h1>Ready to Build Your Professional Resume?</h1>
                            <p>Continue where you left off or start creating a brand new resume with our easy-to-use builder.</p>
                            
                            <!-- Main CTA Button for Logged-in Users -->
                            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="btn-cta">
                                <span>Go to Dashboard</span>
                                <i class='bx bx-right-arrow-alt'></i>
                            </a>

                            <div class="feature-pills justify-content-center">
                                <div class="feature-pill">
                                    <i class='bx bxs-file-doc'></i>
                                    <span>{{ auth()->user()->resumes()->count() }} Resumes</span>
                                </div>
                                <div class="feature-pill">
                                    <i class='bx bxs-envelope'></i>
                                    <span>{{ auth()->user()->coverLetters()->count() }} Cover Letters</span>
                                </div>
                                @if(auth()->user()->activeSubscription)
                                    <div class="feature-pill">
                                        <i class='bx bxs-crown'></i>
                                        <span>Premium Member</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- For Guest Users -->
                            <h1>Create Your Perfect Resume in Minutes</h1>
                            <p>Stand out from the crowd with professionally designed resume templates. Easy to customize, ATS-friendly, and ready to download.</p>
                            
                            <!-- Main CTA Button for Guests -->
                            <a href="{{ route('register') }}" class="btn-cta">
                                <span>Create Your Resume Now</span>
                                <i class='bx bx-right-arrow-alt'></i>
                            </a>

                            <div class="feature-pills justify-content-center">
                                <div class="feature-pill">
                                    <i class='bx bx-check-circle'></i>
                                    <span>Free Templates</span>
                                </div>
                                <div class="feature-pill">
                                    <i class='bx bx-check-circle'></i>
                                    <span>Easy to Use</span>
                                </div>
                                <div class="feature-pill">
                                    <i class='bx bx-check-circle'></i>
                                    <span>ATS-Friendly</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <small style="color: rgba(255, 255, 255, 0.8); font-size: 1rem;">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" style="color: white; font-weight: 600; text-decoration: underline;">Sign In</a>
                                </small>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start mb-3 mb-md-0">
                    <p style="color: rgba(255,255,255,0.5); margin: 0;">© 2024 ResumeBuilder. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p style="color: rgba(255,255,255,0.5); margin: 0;">Made with ❤️ for job seekers worldwide</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>