<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobSease - The Global Home of Employment</title>
    <meta name="description" content="Premium career solutions crafted by global experts with uncompromising quality standards to empower professionals worldwide.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #1E293B;
            background: #FFFFFF;
            overflow-x: hidden;
        }

        img { max-width: 100%; height: auto; display: block; }
        a { text-decoration: none; color: inherit; }
        button { font-family: inherit; cursor: pointer; border: none; outline: none; }

        /* ===== HEADER / NAVIGATION ===== */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.25rem 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        header.scrolled {
            padding: 0.875rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        nav {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: #475569;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s ease;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #3B82F6;
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: #3B82F6;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-buttons {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn {
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-outline {
            border: 2px solid #E2E8F0;
            color: #475569;
            background: transparent;
        }

        .btn-outline:hover {
            border-color: #3B82F6;
            color: #3B82F6;
            background: rgba(59, 130, 246, 0.05);
        }

        .btn-primary {
            background: #3B82F6;
            color: white;
            border: 2px solid #3B82F6;
        }

        .btn-primary:hover {
            background: #2563EB;
            border-color: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-apply {
            position: fixed;
            top: 6rem;
            right: 2rem;
            background: #10B981;
            color: white;
            font-weight: 600;
            padding: 0.875rem 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            z-index: 999;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .btn-apply:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        /* ===== HERO SECTION ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            background: linear-gradient(135deg, #F8FAFC 0%, #FFFFFF 100%);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-content {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            color: #0F172A;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .hero-text h1 span {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-text p {
            font-size: 1.125rem;
            line-height: 1.7;
            color: #64748B;
            margin-bottom: 2.5rem;
            max-width: 540px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-buttons .btn {
            padding: 1rem 2rem;
            font-size: 16px;
            border-radius: 10px;
        }

        .hero-illustration {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .device-mockup {
            position: relative;
            width: 100%;
            max-width: 520px;
            aspect-ratio: 1;
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            animation: float 6s ease-in-out infinite;
            overflow: hidden;
        }

        .device-mockup-inner {
            position: absolute;
            inset: 8%;
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mockup-bar {
            height: 10px;
            border-radius: 5px;
        }

        .mockup-bar.primary { background: #3B82F6; width: 75%; }
        .mockup-bar.gray { background: #E2E8F0; width: 90%; }
        .mockup-bar.green { background: #10B981; width: 60%; }
        .mockup-bar.gray-2 { background: #E2E8F0; width: 80%; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            padding: 6rem 2rem;
            background: white;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 4rem;
        }

        .section-header h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.75rem;
            letter-spacing: -0.01em;
        }

        .section-underline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .section-underline .line {
            height: 4px;
            background: linear-gradient(90deg, #3B82F6 0%, #8B5CF6 100%);
            border-radius: 10px;
        }

        .section-underline .line.main {
            width: 120px;
        }

        .section-underline .line.accent {
            width: 30px;
            background: #FBBF24;
        }

        .features-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            padding: 2.5rem 2rem;
            background: #F8FAFC;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3B82F6, #10B981);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border-color: #3B82F6;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.08);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.35);
        }

        .feature-card h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.375rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 0.875rem;
            line-height: 1.3;
        }

        .feature-card p {
            color: #64748B;
            line-height: 1.7;
            font-size: 15px;
        }

        /* ===== UPLOAD OPTIONS SECTION ===== */
        .upload-section {
            padding: 6rem 2rem;
            background: #F8FAFC;
        }

        .upload-options {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 2rem;
        }

        .upload-card {
            background: white;
            padding: 3rem 2.5rem;
            border-radius: 20px;
            border: 2px solid #E2E8F0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .upload-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border-color: #3B82F6;
        }

        .upload-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #10B981, #34D399);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.75rem;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
            transition: all 0.3s ease;
        }

        .upload-card:hover .upload-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.35);
        }

        .upload-card h3 {
            font-size: 1.375rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .upload-card p {
            color: #64748B;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-size: 15px;
        }

        .upload-card .btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            font-size: 15px;
            border-radius: 10px;
        }

        .upload-note {
            text-align: center;
            margin-top: 2rem;
            color: #94A3B8;
            font-size: 14px;
        }

        /* ===== JOBS SECTION ===== */
        .jobs-section {
            padding: 6rem 2rem;
            background: white;
        }

        .jobs-header {
            max-width: 1280px;
            margin: 0 auto 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .jobs-stats {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .jobs-stats h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #0F172A;
        }

        .jobs-count {
            background: #3B82F6;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .jobs-filter {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .jobs-filter-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748B;
            font-size: 14px;
        }

        .jobs-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            gap: 1.5rem;
        }

        .job-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            border: 2px solid #E2E8F0;
            transition: all 0.3s ease;
            display: flex;
            gap: 1.75rem;
            align-items: start;
            cursor: pointer;
        }

        .job-card:hover {
            border-color: #3B82F6;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .job-logo {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            overflow: hidden;
        }

        .job-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .job-info {
            flex: 1;
        }

        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .job-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 0.375rem;
            line-height: 1.3;
        }

        .job-company {
            color: #64748B;
            font-size: 15px;
        }

        .job-time {
            color: #94A3B8;
            font-size: 14px;
        }

        .job-badge {
            padding: 0.375rem 0.875rem;
            background: #10B981;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
        }

        .job-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748B;
            margin-bottom: 1rem;
            font-size: 14px;
        }

        .job-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
        }

        .tag {
            padding: 0.375rem 0.875rem;
            background: #F1F5F9;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s ease;
        }

        .tag:hover {
            background: #E2E8F0;
        }

        .tag.featured {
            background: #FEF3C7;
            color: #92400E;
        }

        .tag.pro {
            background: #DBEAFE;
            color: #1E40AF;
        }

        /* ===== BOOK SESSION CTA ===== */
        .book-session {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            color: white;
            padding: 6rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .book-session::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .book-session-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .book-session h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 2.5rem;
            letter-spacing: -0.01em;
        }

        .btn-large {
            background: white;
            color: #3B82F6;
            padding: 1.25rem 3.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            display: inline-block;
            text-transform: uppercase;
        }

        .btn-large:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .book-session-contact {
            margin-top: 2rem;
            font-size: 1.0625rem;
            opacity: 0.95;
        }

        /* ===== CTA SECTIONS ===== */
        .cta-section {
            padding: 6rem 2rem;
            text-align: center;
        }

        .cta-section.bg-gray {
            background: #F8FAFC;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 1.5rem;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .cta-content p {
            color: #64748B;
            font-size: 1.125rem;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        .cta-content .btn {
            padding: 1rem 2.5rem;
            font-size: 16px;
            border-radius: 10px;
        }

        /* ===== FOOTER ===== */
        footer {
            background: #0F172A;
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr 1.2fr 1.5fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand {
            max-width: 320px;
        }

        .footer-logo {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.25rem;
            font-family: 'Poppins', sans-serif;
        }

        .footer-brand p {
            color: #94A3B8;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }

        .social-links {
            display: flex;
            gap: 0.75rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .footer-section h3 {
            margin-bottom: 1.25rem;
            font-size: 1.0625rem;
            font-weight: 600;
            color: white;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.875rem;
        }

        .footer-section a {
            color: #94A3B8;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .footer-section a:hover {
            color: white;
        }

        .newsletter-form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .newsletter-input {
            padding: 0.875rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 14px;
        }

        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-subscribe {
            padding: 0.875rem 1.5rem;
            background: #3B82F6;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-subscribe:hover {
            background: #2563EB;
            transform: translateY(-1px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 1.75rem;
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.75rem 2rem 0;
        }

        .footer-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom-content p {
            color: #64748B;
            margin: 0;
            font-size: 14px;
        }

        .footer-badges {
            display: flex;
            gap: 1.25rem;
            align-items: center;
        }

        .badge {
            color: #64748B;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text {
                max-width: 100%;
            }

            .hero-text p {
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .features, .upload-section, .jobs-section, .cta-section, .book-session {
                padding: 4rem 1.5rem;
            }

            .upload-options {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .job-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-apply {
                top: auto;
                bottom: 2rem;
                right: 1.5rem;
                padding: 0.75rem 1.5rem;
                font-size: 14px;
            }

            .book-session h2 {
                font-size: 2rem;
            }

            .cta-content h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header / Navigation -->
    <header id="header">
        <nav>
            <div class="logo">
                <a href="https://www.jobsease.com">
                    <img src="https://www.jobsease.com/assets/img/logo.png" alt="JobSease">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="https://jobsease.com">About</a></li>
                <li><a href="{{route('user.resumes')}}">Create CV</a></li>
                <li><a href="{{route('user.jobs.recommended')}}">Upload CV</a></li>
                <li><a href="{{route('user.interview.prep')}}">Prepare Interview</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="{{route('login')}}" class="btn btn-outline">Login</a>
                <a href="{{route('register')}}" class="btn btn-primary">Sign up</a>
            </div>
        </nav>
    </header>

    <!-- Apply Now Button -->
    <a href="#jobs" class="btn-apply">Apply Now</a>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>The global home<br>of <span>employment</span></h1>
                <p>At JobSease, we build innovative digital solutions designed around your career needs from creating a standout CV to finding the right job and preparing with expert interview coaching from our global high-tech team.</p>
                <div class="hero-buttons">
                    <a href="{{route('register')}}" class="btn btn-primary">Get Started Free</a>
                    <a href="#create-cv" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-illustration">
                <div class="device-mockup">
                    <div class="device-mockup-inner">
                        <div class="mockup-bar primary"></div>
                        <div class="mockup-bar gray"></div>
                        <div class="mockup-bar green"></div>
                        <div class="mockup-bar gray-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Create CV Section -->
    <section class="features" id="create-cv">
        <div class="section-header">
            <h2>Create a CV that gets results</h2>
            <div class="section-underline">
                <div class="line main"></div>
                <div class="line accent"></div>
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <h3>Recruiter-Approved Resume</h3>
                <p>We work with recruiters to design resume templates that format automatically.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Finish Your CV in 15 Minutes</h3>
                <p>Resume Now helps you tackle your work experience by reminding you what you did at your job.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>Land an Interview</h3>
                <p>We suggest the skills you should add. It helped over a million people get interviews.</p>
            </div>
        </div>
    </section>

    <!-- Upload Options Section -->
    <section class="upload-section" id="upload-cv">
        <div class="section-header">
            <h2>Choose Your Path</h2>
            <div class="section-underline">
                <div class="line main"></div>
                <div class="line accent"></div>
            </div>
        </div>

        <div class="upload-options">
            <div class="upload-card">
                <div class="upload-icon">📤</div>
                <h3>Are you uploading an existing resume?</h3>
                <p>We'll give you expert guidance to fill out your info and enhance your resume, from start to finish</p>
                <a href="{{route('user.jobs.recommended')}}" class="btn btn-primary">Upload CV</a>
            </div>

            <div class="upload-card">
                <div class="upload-icon">✨</div>
                <h3>No, start from scratch</h3>
                <p>We'll guide you through the whole process so your skills can shine</p>
                <a href="{{route('user.resumes')}}" class="btn btn-primary">Create CV</a>
            </div>
        </div>

        <p class="upload-note">[File size: 2MB pdf, jpeg, png]</p>
    </section>

    <!-- Jobs Section -->
    <section class="jobs-section" id="jobs">
        <div class="jobs-header">
            <div class="jobs-stats">
                <h2>Jobs</h2>
                <div class="jobs-count">
                    <span style="font-size: 0.875rem;">...</span>
                </div>
            </div>

            <div class="jobs-filter">
                <div class="jobs-filter-info">
                    <span>🔄</span>
                    <span>Live from 3 job boards</span>
                </div>
            </div>
        </div>

        <div class="jobs-grid">
            <!-- Loading placeholder -->
            <div style="text-align: center; padding: 4rem 2rem; color: #94A3B8; grid-column: 1 / -1;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
                <p style="font-size: 1.125rem;">Loading fresh jobs from Remotive, RemoteOK & Arbeitnow...</p>
            </div>
        </div>
    </section>

    <!-- Book Session CTA -->
    <section class="book-session">
        <div class="book-session-content">
            <h2>Let's talk about the idea that's been<br>sitting in your Mind for months</h2>
            <a href="{{route('user.interview.prep')}}" class="btn-large">BOOK SESSION</a>
            <p class="book-session-contact">or reach out to us at hello@jobsease.com</p>
        </div>
    </section>

    <!-- Prepare Interview CTA -->
    <section class="cta-section" id="prepare-interview" style="background: #F8FAFC; padding: 6rem 2rem;">
        <div style="max-width: 1280px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div style="text-align: left;">
                <h2 style="font-family: 'Poppins', sans-serif; font-size: 2.75rem; font-weight: 700; color: #0F172A; margin-bottom: 1.5rem; letter-spacing: -0.01em; line-height: 1.2;">Prepare Interview With Our Experts</h2>
                <div class="section-underline" style="justify-content: flex-start; margin-bottom: 1.5rem;">
                    <div class="line main"></div>
                    <div class="line accent"></div>
                </div>
                <p style="color: #64748B; font-size: 1.125rem; line-height: 1.7; margin-bottom: 2.5rem;">Get personalized coaching and practice sessions to ace your next interview</p>
                <a href="{{route('user.interview.prep')}}" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 16px;">Book Now</a>
            </div>
            <div style="display: flex; align-items: center; justify-content: center;">
                <div style="width: 100%; max-width: 400px; aspect-ratio: 1; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 5rem; box-shadow: 0 25px 50px rgba(59, 130, 246, 0.2);">🎯</div>
            </div>
        </div>
    </section>

    <!-- Find Jobs CTA -->
    <section class="cta-section" id="find-jobs" style="background: white; padding: 6rem 2rem;">
        <div style="max-width: 1280px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div style="display: flex; align-items: center; justify-content: center;">
                <div style="width: 100%; max-width: 400px; aspect-ratio: 1; background: linear-gradient(135deg, #10B981 0%, #34D399 100%); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 5rem; box-shadow: 0 25px 50px rgba(16, 185, 129, 0.2);">💼</div>
            </div>
            <div style="text-align: left;">
                <h2 style="font-family: 'Poppins', sans-serif; font-size: 2.75rem; font-weight: 700; color: #0F172A; margin-bottom: 1.5rem; letter-spacing: -0.01em; line-height: 1.2;">Find Jobs with CV to get results</h2>
                <div class="section-underline" style="justify-content: flex-start; margin-bottom: 1.5rem;">
                    <div class="line main"></div>
                    <div class="line accent"></div>
                </div>
                <p style="color: #64748B; font-size: 1.125rem; line-height: 1.7; margin-bottom: 2.5rem;">Browse thousands of job opportunities tailored to your skills and experience</p>
                <a href="{{route('user.jobs.recommended')}}" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 16px;">Browse Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">JOBSEASE</div>
                <p>Premium career solutions crafted by global experts with uncompromising quality standards to empower professionals worldwide.</p>
                <div class="social-links">
                    <a href="#" class="social-icon">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M0 3v18h24v-18h-24zm6.623 7.929l-4.623 5.712v-9.458l4.623 3.746zm-4.141-5.929h19.035l-9.517 7.713-9.518-7.713zm5.694 7.188l3.824 3.099 3.83-3.104 5.612 6.817h-18.779l5.513-6.812zm9.208-1.264l4.616-3.741v9.348l-4.616-5.607z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="https://jobsease.com">About</a></li>
                    <li><a href="{{route('user.resumes')}}">Create CV</a></li>
                    <li><a href="{{route('user.jobs.recommended')}}">Upload CV</a></li>
                    <li><a href="{{route('user.interview.prep')}}">Prepare Interview</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Legal & Policies</h3>
                <ul>
                    <li><a href="#terms">Terms of Service</a></li>
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#shipping">Shipping Policy</a></li>
                    <li><a href="#refund">Refund Policy</a></li>
                    <li><a href="#compliance">Research Compliance</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Research Updates</h3>
                <p style="color: #94A3B8; font-size: 14px; margin-bottom: 1rem;">Subscribe for product updates and research insights</p>
                <form class="newsletter-form">
                    <input type="email" class="newsletter-input" placeholder="Your email address">
                    <button type="submit" class="btn-subscribe">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>© 2025 Jobsease. All rights reserved.</p>
                    <!-- badges removed -->
            </div>
        </div>
    </footer>

    <!-- Job Loader Script -->
    <script src="frontend/assets/js/jobs-loader.js"></script>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Apply fade-in effect to elements
        document.querySelectorAll('.feature-card, .upload-card, .job-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>
