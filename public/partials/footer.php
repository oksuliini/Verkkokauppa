<footer class="footer bg-pink text-white py-4">
    <div class="container d-flex justify-content-center align-items-center position-relative">
        <!-- Centered Text -->
        <div class="text-center">
            <p class="mb-2">&copy; <?php echo date("Y"); ?> Hello Kitty Web Store. All rights reserved.</p>
            <p class="mb-0">
                <a href="privacy.php" class="text-white text-decoration-none">Privacy policy</a> | 
                <a href="terms.php" class="text-white text-decoration-none">Terms of service</a>
            </p>
        </div>

        <!-- Social Media Icons -->
        <div class="social-icons position-absolute end-0">
            <a href="#" class="text-white me-3" aria-label="Facebook">
                <i class="fab fa-facebook"></i> <!-- Poistettu "fa-sm" alkuperäisen koon palauttamiseksi -->
            </a>
            <a href="#" class="text-white me-3" aria-label="Instagram">
                <i class="fab fa-instagram"></i> <!-- Poistettu "fa-sm" alkuperäisen koon palauttamiseksi -->
            </a>
            <a href="#" class="text-white" aria-label="Twitter">
                <i class="fab fa-twitter"></i> <!-- Poistettu "fa-sm" alkuperäisen koon palauttamiseksi -->
            </a>
        </div>
    </div>
</footer>

<style>
    /* Footer styles */
    .footer {
        background-color: #ffccd5;
        color: #ffffff;
        position: relative;
        bottom: 0;
        width: 100%;
    }

    .footer .social-icons a {
        font-size: 1.25rem; /* Palautettu alkuperäinen oletuskoko */
        transition: color 0.3s;
    }

    .footer .social-icons a:hover {
        color: #ff6f91;
    }

    /* Spacing adjustments */
    .footer p {
        margin: 0;
    }

    .footer p.mb-2 {
        margin-bottom: 0.5rem;
    }

    .footer p.mb-0 {
        margin-bottom: 0;
    }

    /* Ensure footer stays at the bottom */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }

    .footer {
        margin-top: auto;
    }

    /* Fix for unwanted line (if any) */
    .footer .social-icons a {
        text-decoration: none;
        border: none;
    }
</style>
