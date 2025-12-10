@extends('frontend.layouts.app')

@section('title', 'Jobsease - AI-Powered Career Platform')

@section('content')

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

        .btn-primary {
            background: #3B82F6;
            color: white;
            border: 2px solid #3B82F6;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #2563EB;
            border-color: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* ===== CV SECTION ===== */
        .cv-section {
            padding: 6rem 2rem;
            background: white;
        }

        .top-block {
            max-width: 1280px;
            margin: 0 auto 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .left-content {
            position: relative;
        }

        .left-content h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.75rem;
            letter-spacing: -0.01em;
        }

        .underline {
            display: block;
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #3B82F6 0%, #8B5CF6 100%);
            border-radius: 10px;
            margin-top: 1.5rem;
        }

        .arrow {
            display: none;
        }

        .arrow svg {
            width: 80px;
            height: auto;
        }

        /* ===== FEATURES GRID ===== */
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

        .bottom-block {
            max-width: 1280px;
            margin: 0 auto 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
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

        .upload-card .btn-primary {
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
            padding: 1.75rem;
            border-radius: 16px;
            border: 2px solid #E2E8F0;
            transition: all 0.3s ease;
            display: flex;
            gap: 1.5rem;
            align-items: start;
            cursor: pointer;
            max-width: 900px;
            margin: 0 auto;
        }

        .job-card:hover {
            border-color: #3B82F6;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .job-time {
            color: #94A3B8;
            font-size: 13px;
            font-weight: 500;
            min-width: 30px;
            flex-shrink: 0;
            padding-top: 0.25rem;
        }

        .job-logo {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            overflow: hidden;
        }

        .job-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .job-info {
            flex: 1;
            min-width: 0;
        }

        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.625rem;
            gap: 1rem;
        }

        .job-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }

        .job-company {
            color: #64748B;
            font-size: 14px;
            margin-bottom: 0.5rem;
        }

        .job-badge {
            padding: 0.25rem 0.75rem;
            background: #10B981;
            color: white;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .job-location {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: #64748B;
            margin-bottom: 0.875rem;
            font-size: 13px;
        }

        .job-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }

        .tag {
            padding: 0.375rem 0.75rem;
            background: #F1F5F9;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s ease;
            white-space: nowrap;
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

        /* ===== INTERVIEW SECTION ===== */
        .interview-section {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            min-height: 100vh;
            color: #fff;
            padding: 6rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .interview-section::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .top-header {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 3rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .title-wrap {
            position: relative;
            flex: 1;
            min-width: 300px;
        }

        .title-wrap h1 {
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 2.5rem;
            letter-spacing: -0.01em;
        }

        .title-wrap .underline {
            width: 30px;
            height: 4px;
            background: #FBBF24;
            border-radius: 10px;
            display: inline-block;
            margin-top: 1rem;
        }

        .book-btn {
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

        .book-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .bottom-text {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
            margin-top: 4rem;
        }

        .bottom-text h6 {
            font-size: 1.0625rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            font-style: italic;
            font-weight: 400;
            opacity: 0.95;
        }

        .session-btn {
            display: inline-block;
            background: #fff;
            color: #3B82F6;
            margin-bottom: 2rem;
            padding: 1.25rem 3.5rem;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            font-size: 1.125rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .session-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        .email-line {
            margin-top: 2rem;
            font-size: 1.0625rem;
            color: #fff;
        }

        .email-line a {
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            border-bottom: 1px solid #fff;
        }

        /* ===== MOBILE RESPONSIVE ===== */
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

            .top-block,
            .bottom-block {
                flex-direction: column;
                align-items: center;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .upload-options {
                grid-template-columns: 1fr;
            }

            .top-header {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .left-content h1 {
                font-size: 2rem;
            }

            .features, .upload-section, .jobs-section, .interview-section {
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
                padding: 1.25rem;
                gap: 1rem;
            }

            .job-time {
                min-width: 25px;
                font-size: 12px;
            }

            .job-logo {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }

            .job-title {
                font-size: 1rem;
            }

            .job-company {
                font-size: 13px;
            }

            .job-location {
                font-size: 12px;
            }

            .tag {
                font-size: 11px;
                padding: 0.25rem 0.625rem;
            }

            .title-wrap h1 {
                font-size: 2rem;
            }

            .book-btn {
                padding: 1rem 2rem;
                font-size: 1rem;
            }

            .session-btn {
                padding: 0.875rem 2rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 5rem 1rem 2rem;
            }

            .hero-text h1 {
                font-size: 1.5rem;
            }

            .features, .upload-section, .jobs-section, .interview-section {
                padding: 2rem 1rem;
            }

            .left-content h1 {
                font-size: 1.5rem;
            }

            .features-grid {
                gap: 1rem;
            }

            .feature-card {
                padding: 1.5rem 1rem;
            }

            .upload-card {
                padding: 1.5rem 1rem;
            }

            .title-wrap h1 {
                font-size: 1.5rem;
            }

            .jobs-stats h2 {
                font-size: 1.25rem;
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>The global home<br>of <span>employment</span></h1>
                <p>At JobSease, we build innovative digital solutions designed around your career needs from creating a standout CV to finding the right job and preparing with expert interview coaching from our global high-tech team.</p>
                <div class="hero-buttons">
                    <a href="{{route('register')}}" class="btn-primary">Apply Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CV Section -->
    <section class="cv-section">
        <div class="top-block">
            <div class="left-content">
                <h1>Create a CV<br>that gets results</h1>
                <svg class="arrow" width="176" height="67" viewBox="0 0 176 67" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_37_729" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="176" height="65">
                        <path d="M175.67 0H0V64.2376H175.67V0Z" fill="white"/>
                    </mask>
                    <g mask="url(#mask0_37_729)">
                        <path d="M8.98926 53.0432C34.0655 35.568 47.4912 30.155 82.5832 35.6322C93.3922 37.3194 90.7545 45.6742 86.3732 48.8586C69.8419 60.871 65.812 52.3838 67.376 43.4561C71.5317 19.7367 128.003 12.2143 164.937 18.2907C166.45 18.5398 160.637 13.857 156.61 8.72846C155.225 6.96521 156.213 8.77697 165.156 18.1701C167.067 18.5162 155.549 25.3568 155.78 25.4093" stroke="black" stroke-width="2.62194" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>
            <a href="{{route('user.resumes.create')}}" class="btn-primary">Create CV</a>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">📄</span>
                <h3>Recruiter-Approved Resume</h3>
                <p>We work with recruiters to design resume templates that format automatically.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">⚡</span>
                <h3>Finish Your CV in 15 Minutes</h3>
                <p>Resume Now helps you tackle your work experience by reminding you what you did at your job.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🎯</span>
                <h3>Land an Interview</h3>
                <p>We suggest the skills you should add. It helped over a million people get interviews.</p>
            </div>
        </div>
    </section>

    <!-- Upload Options Section -->
    <section class="upload-section">
        <div class="bottom-block">
            <div class="left-content">
                <h1>Find Jobs with<br>CV to get results</h1>
                <svg class="arrow" width="176" height="67" viewBox="0 0 176 67" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_37_729" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="176" height="65">
                        <path d="M175.67 0H0V64.2376H175.67V0Z" fill="white"/>
                    </mask>
                    <g mask="url(#mask0_37_729)">
                        <path d="M8.98926 53.0432C34.0655 35.568 47.4912 30.155 82.5832 35.6322C93.3922 37.3194 90.7545 45.6742 86.3732 48.8586C69.8419 60.871 65.812 52.3838 67.376 43.4561C71.5317 19.7367 128.003 12.2143 164.937 18.2907C166.45 18.5398 160.637 13.857 156.61 8.72846C155.225 6.96521 156.213 8.77697 165.156 18.1701C167.067 18.5162 155.549 25.3568 155.78 25.4093" stroke="black" stroke-width="2.62194" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>
            <a href="{{route('user.resumes')}}" class="btn-primary">Browse Now</a>
        </div>

        <div class="upload-options">
            <div class="upload-card">
                <span class="upload-icon">📤</span>
                <h3>Are you uploading an existing resume?</h3>
                <p>We'll give you expert guidance to fill out your info and enhance your resume, from start to finish</p>
                <a href="{{route('user.jobs.recommended')}}" class="btn-primary">Upload CV</a>
            </div>

            <div class="upload-card">
                <span class="upload-icon">✨</span>
                <h3>No, start from scratch</h3>
                <p>We'll guide you through the whole process so your skills can shine</p>
                <a href="{{route('user.resumes')}}" class="btn-primary">Create CV</a>
            </div>
        </div>
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
            <div style="text-align: center; padding: 4rem 2rem; color: #94A3B8; width: 100%;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
                <p style="font-size: 1.125rem;">Loading fresh jobs from Remotive, RemoteOK & Arbeitnow...</p>
            </div>
        </div>
    </section>

    <!-- Interview Section -->
    <section class="interview-section">
        <div class="top-header">
            <div class="title-wrap">
                <h1>Prepare Interview<br>With Our Experts</h1>
                <svg class="arrow" width="176" height="67" viewBox="0 0 176 67" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_37_729" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="176" height="65">
                        <path d="M175.67 0H0V64.2376H175.67V0Z" fill="white"/>
                    </mask>
                    <g mask="url(#mask0_37_729)">
                        <path d="M8.98926 53.0432C34.0655 35.568 47.4912 30.155 82.5832 35.6322C93.3922 37.3194 90.7545 45.6742 86.3732 48.8586C69.8419 60.871 65.812 52.3838 67.376 43.4561C71.5317 19.7367 128.003 12.2143 164.937 18.2907C166.45 18.5398 160.637 13.857 156.61 8.72846C155.225 6.96521 156.213 8.77697 165.156 18.1701C167.067 18.5162 155.549 25.3568 155.78 25.4093" stroke="white" stroke-width="2.62194" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>
            <a href="{{route('user.interview.expert')}}" class="book-btn">Book Now</a>
        </div>

        <div class="bottom-text">
            <h6>Let's talk about the idea that's been<br>sitting in your mind for months</h6>
            <a href="{{route('user.interview.prep')}}" class="session-btn">BOOK SESSION</a>
            <p class="email-line">
                or reach out to us at
                <a href="mailto:hello@jobsease.com">hello@jobsease.com</a>
            </p>
        </div>
    </section>

    <!-- Job Loader Script -->
    <script src="{{asset('frontend/assets/js/jobs-loader.js')}}"></script>

    <script>
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

@endsection
