<!-- ================= TANTRADE PRELOADER ================= -->
<style>
    /* ===== PRELOADER CONTAINER ===== */
    #preloader {
        position: fixed;
        inset: 0;
        background: #ffffff;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    #preloader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    /* ===== CONTENT ===== */
    .preloader-content {
        text-align: center;
    }

    .preloader-logo {
        width: 72px;
        height: 72px;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        margin-bottom: 18px;
    }

    /* ===== SPINNER ===== */
    .preloader-spinner {
        width: 52px;
        height: 52px;
        margin: 0 auto 14px auto;
        border: 4px solid rgba(13, 110, 253, 0.2);
        border-top-color: #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* ===== TEXT ===== */
    .preloader-text {
        font-size: 0.95rem;
        color: #6c757d;
        letter-spacing: 0.4px;
        font-weight: 500;
    }

    /* ===== ANIMATION ===== */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ===== ACCESSIBILITY ===== */
    @media (prefers-reduced-motion: reduce) {
        .preloader-spinner {
            animation: none;
        }
    }
</style>

<div id="preloader">
    <div class="preloader-content">
        <img
            src="{{ asset('images/logo.jpg') }}"
            alt="TanTrade Logo"
            class="preloader-logo"
        >

        <div class="preloader-spinner"></div>

        <div class="preloader-text">
            Loading TanTrade Digital Services…
        </div>
    </div>
</div>

<script>
    // Hide preloader after full page load
    window.addEventListener('load', function () {
        const preloader = document.getElementById('preloader');
        if (!preloader) return;

        preloader.classList.add('hidden');

        setTimeout(function () {
            preloader.remove();
        }, 450);
    });
</script>
<!-- ================= END PRELOADER ================= -->
