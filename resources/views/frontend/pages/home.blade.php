
@extends('frontend.layouts.app')

@section('title', 'Jobsease | Create CV, Practice Interview & Apply for Jobs Easily')
@section('meta_description', 'Create a professional CV, practice interviews with AI, and apply for jobs faster. Jobsease helps you get job-ready in minutes. Start free today.')

@section('content')


    <style>


        .upload-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }


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
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #1E293B;
            background: #FFFFFF;
            overflow-x: hidden !important;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        img { max-width: 100%; height: auto; display: block; }
        a { text-decoration: none; color: inherit; }
        button { font-family: inherit; cursor: pointer; border: none; outline: none; }


        /* ===== HERO SECTION ===== */
        .hero {
            /*min-height: 100vh;*/
            display: flex;
            align-items: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            background: linear-gradient(135deg, #F8FAFC 0%, #FFFFFF 100%);
            overflow: hidden;
            width: 100%;
        }

        .hero {
            background-image: url('frontend/assets/images/jobsease.png'); /* ✅ change path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
            width: 100%;
            padding: 0 2rem;
            box-sizing: border-box;
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
            color: #000;
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
    grid-template-columns: repeat(3, 1fr);  /* Changed this line */
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
            margin: 0 auto 1.75rem auto;
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
            color:#fff;
            margin-top: 2rem;
            font-size: 1.0625rem;
            opacity: 0.95;
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

<style>

.interview-section {
    background: #0075ff;
    /*min-height: 100vh;*/
    color: #fff;
    padding: 40px 60px;
    position: relative;
    overflow: hidden;
}

/* Real Keyboard Image */
.keyboard-bg {
    position: absolute;
    inset: 0;
    background-image: url('frontend/assets/images/keyboard.webp'); /* ✅ change path */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0.4;
    z-index: 1;
}

/* Blur Effect Layer */
.blur-overlay {
    /*position: absolute;*/
    inset: 0;
    backdrop-filter: blur(12px);
    background: rgba(0, 117, 255, 0.25);
    z-index: 2;
}

/* Content Layer */
.top-header,
.bottom-text {
    position: relative;
    z-index: 3;
}

/* Top Section */

.top-header {
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.title-wrap {
    position: relative;
}

.top-header h1 {
    color:#fff;
    font-size: 46px;
    font-weight: 800;
    line-height: 1.2;
}

.underline {
    display: block;
    width: 200px;
    height: 5px;
    background: #fff;
    margin-top: 6px;
    border-radius: 10px;
}

.arrow {
    position: absolute;
    right: -30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 24px;
}

/* Buttons */

.book-btn {
    background: #000;
    color: #fff;
    padding: 12px 35px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
}

.session-btn {
    display: inline-block;
    background: #fff;
    color: #000;
    margin-top: 20px;
    padding: 14px 60px;
    border-radius: 10px;
    font-weight: 800;
    text-decoration: none;
    font-size: 14px;
}

/* Bottom Section */

.bottom-text {
    text-align: center;
    margin-top: 150px;
}

.bottom-text p {
    font-size: 16px;
    line-height: 1.6;
}

.email-line {
    margin-top: 20px;
    font-size: 14px;
    font-style: italic;
    color:#fff;
}

.email-line a {
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    border-bottom: 1px solid #fff;
}

/* Mobile */

@media (max-width: 768px) {
    .top-header {
        flex-direction: column;
        gap: 20px;
    }

    .top-header h1 {
        font-size: 34px;
    }

    .bottom-text {
        margin-top: 200px;
    }
}


    .cv-section {
    /*max-width: 1000px;*/
    /*margin: auto;*/
    /*margin:10%;*/
    padding: 40px 20px;
}

.top-block,
.bottom-block {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 50px;
}

.left-content {
    position: relative;
}

.left-content h1 {
    font-size: 56px;
    font-weight: 800;
    line-height: 1.1;
    color: #000;
    position: relative;
    display: inline-block;
}

.underline {
    display: block;
    width: 200px;
    height: 3px;
    background: #4ade80;
    position: absolute;
    left: 0;
    /*bottom: -5px;*/
    border-radius: 10px;
}

.arrow {
    font-size: 24px;
    position: absolute;
    right: -30px;
    top: 50%;
    transform: translateY(-50%);
}

.btn-primary {
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    padding: 14px 30px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 16px;
}

.features {
    display: grid;
    /*grid-template-columns: repeat(3, 1fr);*/
    gap: 50px;
    margin-bottom: 60px;
    text-align: center;
}

.feature-box img {
    width: 50px;
    margin-bottom: 12px;
}

.feature-box h3 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 10px;
}

.feature-box p {
    font-size: 13px;
    line-height: 1.6;
    color: #333;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .top-block,
    .bottom-block {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }

    .features {
        /*grid-template-columns: 1fr;*/
        gap: 30px;
    }

    .left-content h1 {
        font-size: 34px;
    }
}

@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
}

</style>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Create Your CV. Practice Interviews.<br>Apply for Jobs — Faster.</h1>
                <p>AI-powered platform to help you get job-ready in minutes.</p>
                <div class="hero-buttons">
                    <a href="{{route('register')}}" class="btn btn-primary">Get Started Free</a>
                    <a href="#how-it-works" class="btn btn-outline">See How It Works</a>
                </div>
            </div>
            <!--<div class="hero-illustration">-->
            <!--    <div class="device-mockup">-->
            <!--        <div class="device-mockup-inner">-->
            <!--            <div class="mockup-bar primary"></div>-->
            <!--            <div class="mockup-bar gray"></div>-->
            <!--            <div class="mockup-bar green"></div>-->
            <!--            <div class="mockup-bar gray-2"></div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
    </section>


<section class="cv-section" id="how-it-works">
    <!-- How It Works Section -->
    <div class="top-block">
        <div class="left-content">
            <h2>
                How Jobsease Helps<br>
                You Get Hired
                <span class="underline u1"></span>
            </h2>
            <!--<span class="arrow">➜</span>-->
<svg width="176" height="67" viewBox="0 0 176 67" fill="none" xmlns="http://www.w3.org/2000/svg">
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

    <!-- How It Works - 4 Steps -->

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">1️⃣</div>
                <h3>Create CV</h3>
                <p>Easy CV builder with professional templates</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">2️⃣</div>
                <h3>Find Jobs</h3>
                <p>Jobs matched by your skills and location</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">3️⃣</div>
                <h3>Practice Interview</h3>
                <p>AI interview practice with instant feedback</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">4️⃣</div>
                <h3>Get Hired</h3>
                <p>Apply confidently and faster</p>
            </div>
        </div>


</section>



    <!-- Core Features Section -->
    <section class="upload-section" id="features">
        <div class="bottom-block">
        <div class="left-content">
            <h2>
                Everything You Need<br>
                in One Platform
                <span class="underline u2"></span>
            </h2>

            <svg width="176" height="67" viewBox="0 0 176 67" fill="none" xmlns="http://www.w3.org/2000/svg">
<mask id="mask0_37_729" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="176" height="65">
<path d="M175.67 0H0V64.2376H175.67V0Z" fill="white"/>
</mask>
<g mask="url(#mask0_37_729)">
<path d="M8.98926 53.0432C34.0655 35.568 47.4912 30.155 82.5832 35.6322C93.3922 37.3194 90.7545 45.6742 86.3732 48.8586C69.8419 60.871 65.812 52.3838 67.376 43.4561C71.5317 19.7367 128.003 12.2143 164.937 18.2907C166.45 18.5398 160.637 13.857 156.61 8.72846C155.225 6.96521 156.213 8.77697 165.156 18.1701C167.067 18.5162 155.549 25.3568 155.78 25.4093" stroke="black" stroke-width="2.62194" stroke-linecap="round" stroke-linejoin="round"/>
</g>
</svg>

            <!--<span class="arrow">➜</span>-->
        </div>
        <a href="{{route('user.resumes')}}" class="btn-primary">Browse Now</a>
    </div>

        <div class="upload-options">
            <div class="upload-card">
                <div class="upload-icon">�</div>
                <h3>Easy CV Builder</h3>
                <p>Create professional CVs in minutes</p>
                <a href="{{route('user.resumes.create')}}" class="btn btn-primary">Create CV</a>
            </div>

            <div class="upload-card">
                <div class="upload-icon">🤖</div>
                <h3>AI Interview Practice</h3>
                <p>Practice real interview questions</p>
                <a href="{{route('user.interview.prep')}}" class="btn btn-primary">Practice Now</a>
            </div>

            <div class="upload-card">
                <div class="upload-icon">🔎</div>
                <h3>Smart Job Matching</h3>
                <p>Find jobs near you that fit your profile</p>
                <a href="{{route('user.jobs.recommended')}}" class="btn btn-primary">Find Jobs</a>
            </div>

            <div class="upload-card">
                <div class="upload-icon">⚡</div>
                <h3>One-Click Apply</h3>
                <p>Apply faster with your saved CV</p>
                <a href="{{route('user.jobs.recommended')}}" class="btn btn-primary">Apply Now</a>
            </div>
        </div>
    </section>

    <!-- Jobs Section -->
    <!--<section class="jobs-section" id="jobs">-->
    <!--    <div class="jobs-header">-->
    <!--        <div class="jobs-stats">-->
    <!--            <h2>Jobs</h2>-->
    <!--            <div class="jobs-count">-->
    <!--                <span style="font-size: 0.875rem;">...</span>-->
    <!--            </div>-->
    <!--        </div>-->

    <!--        <div class="jobs-filter">-->
    <!--            <div class="jobs-filter-info">-->
    <!--                <span>🔄</span>-->
    <!--                <span>Live from 3 job boards</span>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="jobs-grid">-->
            <!-- Loading placeholder -->
    <!--        <div style="text-align: center; padding: 4rem 2rem; color: #94A3B8; grid-column: 1 / -1;">-->
    <!--            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>-->
    <!--            <p style="font-size: 1.125rem;">Loading fresh jobs from Remotive, RemoteOK & Arbeitnow...</p>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->


<!-- Jobs Section -->
    <style>
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
            width: 100%;
            overflow: visible;
        }

        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.625rem;
            gap: 1rem;
            width: 100%;
            overflow: visible;
        }

        .job-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 0.25rem;
            line-height: 1.3;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .job-company {
            color: #64748B;
            font-size: 14px;
            margin-bottom: 0.5rem;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .job-badge {
            padding: 0.25rem 0.75rem;
            background: #10B981;
            color: white;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            white-space: normal;
            flex-shrink: 0;
        }

        .job-location {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: #64748B;
            margin-bottom: 0.875rem;
            font-size: 13px;
            flex-wrap: wrap;
            width: 100%;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .job-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            width: 100%;
            overflow: visible;
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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .jobs-section {
                padding: 4rem 1.5rem;
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
        }

        /* ================= JOBS SECTION FIX ================= */

.jobs-section {
    padding: clamp(70px, 10vh, 120px) clamp(16px, 5vw, 80px);
    background: #fff;
}

.jobs-header {
    max-width: 1100px;
    margin: 0 auto 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.jobs-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

/* All cards same width */
.job-card {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    background: #fff;
    border: 2px solid #E2E8F0;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    transition: 0.25s ease;
}

.job-card:hover {
    border-color: #3B82F6;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.job-logo {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    flex-shrink: 0;
    overflow: hidden;
    background: #F1F5F9;
}

/* FLEXIBLE INFO */
.job-info {
    flex: 1;
    min-width: 0;
}

/* TITLE + BADGE ALIGN */
.job-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

/* TAG WRAPPING FIX */
.job-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* ================= MOBILE FIX ================= */

@media (max-width: 768px) {
    .jobs-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .jobs-grid {
        gap: 16px;
    }

    .job-card {
        flex-direction: column;
        width: 100%;
        padding: 20px;
        gap: 16px;
    }

    .job-logo {
        width: 48px;
        height: 48px;
    }

    .job-header {
        flex-direction: column;
        gap: 6px;
    }

    .job-time {
        font-size: 12px;
    }

    .job-title {
        font-size: 16px;
    }

    .tag {
        font-size: 11px;
        padding: 4px 10px;
    }
}

/* ================= SMALL MOBILE FIX ================= */
@media (max-width: 480px) {
    .job-card {
        padding: 18px;
    }
}

/* ================= COMPREHENSIVE MOBILE RESPONSIVE FIX ================= */
@media (max-width: 768px) {
    * {
        box-sizing: border-box !important;
    }

    html,
    body {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden !important;
    }

    .hero-content {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
        padding: 0 1rem !important;
    }

    .hero {
        padding: 4rem 1rem 2rem !important;
        width: 100% !important;
    }

    section {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .container {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        margin: 0 !important;
    }

    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }

    main {
        width: 100% !important;
        overflow-x: hidden !important;
    }

    .content {
        width: 100% !important;
        overflow-x: hidden !important;
    }
}


    </style>


    <!-- Pricing Preview Section -->
    <section class="jobs-section" id="pricing">
        <div class="jobs-header">
            <div class="jobs-stats">
                <h2>Simple Pricing for Everyone</h2>
            </div>
        </div>

        <div class="jobs-grid" style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
            <!-- Free Plan -->
            <div style="background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 16px; padding: 2rem; min-width: 280px; max-width: 320px;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🆓</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Free</h3>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0; color: #64748B;">
                    <li style="padding: 0.5rem 0;">✓ 1 CV</li>
                    <li style="padding: 0.5rem 0;">✓ 5 job views</li>
                    <li style="padding: 0.5rem 0;">✓ Basic interview questions</li>
                </ul>
            </div>

            <!-- Pro Plan -->
            <div style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); border-radius: 16px; padding: 2rem; min-width: 280px; max-width: 320px; color: white;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⭐</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem;">Pro</h3>
                <span style="background: #FBBF24; color: #000; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Recommended</span>
                <ul style="list-style: none; padding: 0; margin: 1rem 0 1.5rem 0;">
                    <li style="padding: 0.5rem 0;">✓ Unlimited CVs</li>
                    <li style="padding: 0.5rem 0;">✓ AI interview practice</li>
                    <li style="padding: 0.5rem 0;">✓ Unlimited job apply</li>
                    <li style="padding: 0.5rem 0;">✓ No ads</li>
                </ul>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{route('packages')}}" class="btn btn-primary" style="padding: 1rem 2rem;">View Full Pricing</a>
        </div>
    </section>



    <!-- Book Session CTA -->
    <!--<section class="book-session">-->

    <!--      <div class="top-block">-->
    <!--    <div class="left-content">-->
    <!--        <h1>-->
    <!--           Prepare Interview With <br>-->
    <!--            Our Experts-->
    <!--        <span class="underline"></span>-->
    <!--        </h1>-->
    <!--        <span class="arrow">➜</span>-->
    <!--    </div>-->
    <!--    <a href="{{route('user.interview.prep')}}" class="btn-primary">Book Now</a>-->
    <!--</div>-->

    <!--    <div id="contact" class="book-session-content">-->
    <!--        <h2>Let's talk about the idea that's been<br>sitting in your Mind for months</h2>-->
    <!--        <a href="{{route('user.interview.prep')}}" class="btn-large">BOOK SESSION</a>-->
    <!--        <p class="book-session-contact">or reach out to us at hello@jobsease.com</p>-->
    <!--    </div>-->
    <!--</section>-->


    <!-- Trust Section -->
    <section class="interview-section" style="padding: 4rem 2rem;">

    <!-- Blur Background Layer -->
    <div class="keyboard-bg"></div>
    <div class="blur-overlay"></div>

    <!-- Trust Content -->
    <div class="top-header" style="flex-direction: column; text-align: center;">
        <h2 style="color: #fff; font-size: 2rem; margin-bottom: 2rem;">Why Jobsease?</h2>

        <div style="display: flex; gap: 2rem; flex-wrap: wrap; justify-content: center; margin-bottom: 2rem;">
            <div style="text-align: center; min-width: 200px;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🇮🇩</div>
                <p style="color: #fff; font-weight: 600;">Built for job seekers in Indonesia</p>
            </div>
            <div style="text-align: center; min-width: 200px;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🤖</div>
                <p style="color: #fff; font-weight: 600;">AI-powered & easy to use</p>
            </div>
            <div style="text-align: center; min-width: 200px;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔒</div>
                <p style="color: #fff; font-weight: 600;">Secure payments & privacy protected</p>
            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="bottom-text" style="margin-top: 3rem;">
        <h2 style="color:#fff; font-size: 2rem; margin-bottom: 1rem;">Ready to Get Hired Faster?</h2>
        <p style="color: #fff; opacity: 0.9; margin-bottom: 1.5rem;">Create your CV, practice interviews, and apply for jobs — all in one place.</p>

        <a href="{{route('register')}}" class="session-btn">Start Free Now</a>

        <p class="email-line">
            or reach out to us at
            <a href="mailto:hello@jobsease.com">hello@jobsease.com</a>
        </p>
    </div>

</section>

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

@endsection


<style>
/* ================= FULL MOBILE RESPONSIVENESS FIX ================= */

/* Center all major headings on mobile */
@media (max-width: 768px) {
    h1, h2, h3, h4, h5, h6 {
        text-align: center !important;
    }

    /* Center hero content */
    .hero-content {
        grid-template-columns: 1fr !important;
        text-align: center;
    }

    .hero-text p {
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }

    .hero-buttons {
        justify-content: center;
    }

    /* Center CV Section */
    .top-block,
    .bottom-block {
        flex-direction: column;
        align-items: center !important;
        text-align: center;
    }

    .left-content {
        text-align: center;
        width: 100%;
    }

    .left-content .underline {
        left: 50%;
        transform: translateX(-50%);
    }

    /* Center features grid */
    .features-grid {
        grid-template-columns: 1fr !important;
        justify-items: center;
    }

    /* Center Upload Section */
    .upload-options {
        grid-template-columns: 1fr !important;
    }

    .upload-card {
        text-align: center;
    }

    /* Jobs section fixes */
    .jobs-header {
        flex-direction: column;
        align-items: center !important;
        text-align: center;
    }

    .jobs-stats {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .jobs-filter {
        justify-content: center;
    }

    /* Interview Section */
    .top-header {
        flex-direction: column;
        align-items: center !important;
        text-align: center;
    }

    .bottom-text {
        text-align: center;
        margin-top: 100px;
    }

    .session-btn {
        width: auto;
        text-align: center;
    }
}

/* ================= EXTRA SMALL PHONES ================= */
@media (max-width: 480px) {
    .hero-text h1 {
        font-size: 2rem !important;
        line-height: 1.2;
    }

    .top-header h1 {
        font-size: 2rem !important;
    }

    .left-content h1 {
        font-size: 2rem !important;
    }
}

/* ================= JOB SECTION MOBILE WIDTH FIX ================= */

@media (max-width: 768px) {

    .jobs-section {
        width: 100vw !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box;
        margin: 0 !important;
    }

    .jobs-grid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }

    /* Job card - completely redesigned for mobile */
    .job-card {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        padding: 12px !important;
        display: block !important;
        overflow: visible !important;
        flex-direction: unset !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background: white !important;
    }

    /* Hide logo and time on mobile */
    .job-logo {
        display: none !important;
    }

    .job-time {
        display: none !important;
    }

    /* Job info takes full width */
    .job-info {
        width: 100% !important;
        min-width: unset !important;
        flex: unset !important;
        display: block !important;
    }

    /* Job header - single column */
    .job-header {
        display: block !important;
        width: 100% !important;
        margin-bottom: 8px !important;
        overflow: visible !important;
    }

    /* Job title - full width, no truncation */
    .job-title {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #0F172A !important;
        margin: 0 0 4px 0 !important;
        line-height: 1.4 !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        hyphens: auto !important;
        white-space: normal !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Company name - full width */
    .job-company {
        color: #64748B !important;
        font-size: 12px !important;
        margin: 0 0 6px 0 !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        white-space: normal !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Location - full width */
    .job-location {
        display: block !important;
        width: 100% !important;
        color: #64748B !important;
        font-size: 11px !important;
        margin: 0 0 8px 0 !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        white-space: normal !important;
    }

    /* Badge/Pro indicator - inline */
    .job-badge {
        display: inline-block !important;
        padding: 2px 6px !important;
        font-size: 10px !important;
        margin: 0 4px 0 0 !important;
        white-space: normal !important;
        flex-shrink: unset !important;
    }

    /* Tags - full width, wrapped */
    .job-tags {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
        width: 100% !important;
        margin: 8px 0 0 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    .tag {
        display: inline-block !important;
        font-size: 10px !important;
        padding: 3px 6px !important;
        white-space: normal !important;
        word-break: break-word !important;
        flex: 0 1 auto !important;
        max-width: 100% !important;
    }

}

</style>
