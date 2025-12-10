<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <title>JobSease - The Global Home of Employment</title>
  <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px 0;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #2563eb;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #2563eb;
            border-radius: 6px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
        }

        .nav-links a:hover {
            color: #2563eb;
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .lang-selector {
            color: #475569;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-outline {
            border: 2px solid #2563eb;
            color: #2563eb;
            background: transparent;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        /* Hero Section */
        .hero {
            padding: 80px 0;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 48px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-text h1 span {
            color: #2563eb;
        }

        .hero-text p {
            color: #000;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .hero-image {
            text-align: center;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: #fff;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #64748b;
            font-size: 18px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: #eff6ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #1e293b;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Job Listings */
        .jobs {
            padding: 80px 0;
            background: #f8fafc;
        }

        .jobs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .search-bar input {
            flex: 1;
            padding: 14px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
        }

        .job-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .job-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .job-info {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 1;
        }

        .company-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #2563eb;
        }

        .job-details h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .job-meta {
            display: flex;
            gap: 15px;
            color: #64748b;
            font-size: 14px;
        }

        .job-tags {
            display: flex;
            gap: 8px;
        }

        .tag {
            padding: 6px 12px;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #475569;
        }

        .tag.featured {
            background: #fef3c7;
            color: #92400e;
        }

        .job-salary {
            font-weight: 600;
            color: #1e293b;
            font-size: 18px;
        }

        /* CTA Section */
        .cta {
            padding: 80px 0;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            text-align: center;
        }

        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .btn-white {
            background: white;
            color: #2563eb;
        }

        .btn-white:hover {
            background: #f8fafc;
        }

        /* Footer */
        footer {
            background: #007BFF;
            color: #fff;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1.2fr 1.5fr;
            gap: 50px;
            margin-bottom: 40px;
        }

        .footer-brand {
            max-width: 280px;
        }

        .footer-section h3 {
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 12px;
        }

        .footer-section a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #333;
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .social-icon {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .social-icon:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .newsletter-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .newsletter-input {
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 14px;
        }

        .newsletter-input::placeholder {
            color: #fff;
        }

        .btn-subscribe {
            padding: 12px 24px;
            background: white;
            color: black;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-subscribe:hover {
            background: rgba(255, 255, 255, 0.9);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        .footer-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom-content p {
            color: #fff;
            margin: 0;
            font-size: 14px;
        }

        .footer-badges {
            display: flex;
            gap: 15px;
        }

        .badge {
            color: #fff;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .job-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .nav-links {
                display: none;
            }
        }
    </style>
    
  
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="container">
            <div class="logo">
            
            <a class="navbar-brand" href="https://www.jobsease.com">
                <img src="https://www.jobsease.com/assets/img/logo.png" alt="Logo" style="width: 70%;">
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
