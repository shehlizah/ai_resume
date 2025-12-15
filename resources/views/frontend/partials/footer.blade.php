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
                    <p style="color: #fff; line-height: 1.8; font-size: 15px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">Premium career solutions crafted by global experts with uncompromising quality standards to empower professionals worldwide.</p>
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
                    <p>© 2025 Jobsease. All rights reserved.</p>
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

    .footer-bottom-content p {
        display: block;
        margin: 0;
        font-weight: 600;
        color: #fff;
        font-size: 14px;
    }

    .footer-badges {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .badge {
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #fff;
        font-size: 13px;
        font-weight: 500;
    }

    /* Footer content grid - 4 columns on desktop */
    .footer-content {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1.5fr;
        gap: 2rem;
        width: 100%;
    }

    /* Footer section styling */
    .footer-section {
        display: flex;
        flex-direction: column;
    }

    .footer-section h3 {
        color: #fff;
        font-size: 1.0625rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        font-family: 'Poppins', sans-serif;
    }

    .footer-section ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 1rem;
    }

    .footer-section ul li a {
        color: #94A3B8;
        font-size: 15px;
        text-decoration: none;
        transition: color 0.2s;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .footer-section ul li a:hover {
        color: #fff;
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

        /* Center footer content on mobile */
        .footer-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.5rem !important;
            align-items: center !important;
        }

        /* Center all footer sections on mobile */
        .footer-section {
            width: 100% !important;
            margin-bottom: 1.5rem !important;
            text-align: center !important;
            align-items: center !important;
        }

        /* Ensure footer brand stays centered */
        .footer-section.footer-brand {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            width: 100% !important;
        }

        .footer-brand .logo {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
        }

        .footer-brand p {
            text-align: center !important;
            margin: 0 auto 1rem auto !important;
        }

        /* Center logo */
        footer img {
            display: block !important;
            margin: 0 auto 15px auto !important;
        }

        /* Center social icons */
        footer .social-icons,
        footer .social-links,
        footer .footer-socials,
        .footer-brand .social-links {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 15px !important;
            width: 100% !important;
            margin: 0 auto !important;
        }

        /* Footer bottom layout */
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
    }

</style>
