<!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section footer-brand">
                    <div class="logo" style="color: white; margin-bottom: 20px;">
                       <div class="footer-logo-wrap">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Jobsease Logo">
                        </div>
                    </div>
                    <p style="color: #fff; line-height: 1.8; font-size: 14px;">Premium career solutions crafted by global experts with uncompromising quality standards to empower professionals worldwide.</p>
                    <div class="social-links">
                        <a href="#" class="social-icon">in</a>
                        <a href="#" class="social-icon">🐦</a>
                        <a href="#" class="social-icon">✉</a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="https://jobsease.com">Home</a></li>
                        <li><a href="{{route('user.resumes') }}">Create CV</a></li>
                        <li><a href="{{route('user.jobs.recommended') }}">Upload CV</a></li>
                        <li><a href="{{route('user.interview.prep') }}">Prepare Interview</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Legal & Policies</h3>
                    <ul>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Refund Policy</a></li>
                        <li><a href="#">Research Compliance</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Research Updates</h3>
                    <p style="color: #fff; margin-bottom: 15px; font-size: 14px;">Subscribe for product updates and research insights</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Your email address" class="newsletter-input">
                        <button class="btn-subscribe">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p class="footer-copyright">© 2025 Jobsease. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="#">Terms of Service</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Support Policy</a>
                    </div>
                    <p class="footer-credits">Designed and Developed by <a href="https://shehlizah.com" rel="nofollow">SZM</a></p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

<style>
    .site-footer {
        padding: 40px 20px;
        text-align: center;
        background: #fff;
    }

    .footer-logo-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .footer-logo-wrap img {
        height: 40px;
        width: auto;
    }

    /* Footer Brand Section - Center Logo, Description, and Social Icons */
    .footer-brand {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        margin-bottom: 2rem;
    }

    .footer-brand .logo {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .footer-brand p {
        text-align: center;
        max-width: 400px;
        margin: 0 auto 1rem;
    }

    .footer-brand .social-links {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Footer Bottom Styling */
    .footer-bottom-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        text-align: center;
    }

    .footer-copyright {
        display: block;
        margin: 0;
        font-weight: 600;
        color: #fff;
        font-size: 14px;
    }

    .footer-links {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin: 0.5rem 0;
    }

    .footer-links a {
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: #3b82f6;
    }

    .footer-credits {
        display: block;
        margin: 0;
        color: #fff;
        font-size: 14px;
    }

    .footer-credits a {
        color: #fff;
        text-decoration: none;
        font-weight: 600;
    }

    .footer-credits a:hover {
        color: #3b82f6;
    }

    /* ================= FOOTER MOBILE FIX ================= */

    @media (max-width: 768px) {

        footer,
        .site-footer {
            width: 100% !important;
            max-width: 100% !important;
            text-align: center !important;
            padding-left: 20px;
            padding-right: 20px;
            box-sizing: border-box;
        }

        footer * {
            box-sizing: border-box;
        }

        /* Center logo */
        footer img {
            display: block !important;
            margin: 0 auto 15px auto !important;
        }

        /* Center social icons */
        footer .social-icons,
        footer .social-links,
        footer .footer-socials {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 15px;
            width: 100%;
            margin: 0 auto !important;
        }

        /* Footer bottom layout - 3 lines on mobile */
        .footer-bottom-content {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 1rem !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .footer-copyright {
            display: block !important;
            width: 100% !important;
            margin-bottom: 0.5rem !important;
            text-align: center !important;
        }

        .footer-links {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 0.75rem !important;
            flex-wrap: wrap !important;
            width: 100% !important;
            margin: 0.5rem 0 !important;
        }

        .footer-links a {
            display: inline-block !important;
            margin: 0 4px !important;
            font-size: 13px !important;
        }

        .footer-credits {
            display: block !important;
            width: 100% !important;
            margin-top: 0.5rem !important;
            text-align: center !important;
        }

        /* Center footer text */
        footer p,
        footer span,
        footer a {
            text-align: center !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .footer-section {
            width: 100% !important;
            margin-bottom: 1.5rem !important;
        }

        .footer-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.5rem !important;
        }
    }

</style>