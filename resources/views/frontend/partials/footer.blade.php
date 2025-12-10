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
                    <p>© 2025 Jobsease. All rights reserved.</p>
                    <div class="footer-badges">
                        <span class="badge">🏛️ FDA Registered Facility</span>
                        <span class="badge">🇺🇸 Made in USA</span>
                    </div>
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


/* ================= FOOTER MOBILE FIX ================= */

@media (max-width: 768px) {

    footer,
    .site-footer {
        width: 100%;
        max-width: 100%;
        text-align: center !important;
        padding-left: 20px;
        padding-right: 20px;
        box-sizing: border-box;
    }

    footer * {
        text-align: center !important;
        margin-left: auto !important;
        margin-right: auto !important;
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
    }

    /* Center footer text */
    footer p,
    footer span,
    footer a {
        text-align: center !important;
        display: block;
    }
}


</style>