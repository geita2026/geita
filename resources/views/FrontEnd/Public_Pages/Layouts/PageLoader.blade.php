<!-- ================= GeMEMIS GOLDEN PRELOADER ================= -->
<style>
    #preloader {
        position: fixed;
        inset: 0;
        background: #070506;
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
    .preloader-content {
        text-align: center;
    }
    .preloader-logo {
        height: 92px;
        width: auto;
        margin-bottom: 20px;
        filter: drop-shadow(0 10px 28px rgba(255, 191, 0, 0.28));
    }
    .preloader-spinner {
        width: 52px;
        height: 52px;
        margin: 0 auto 16px auto;
        border: 3.5px solid rgba(255, 191, 0, 0.18);
        border-top-color: #FFBF00;
        border-radius: 50%;
        animation: gem-spin 0.9s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
        box-shadow: 0 0 20px rgba(255, 191, 0, 0.25);
    }
    .preloader-text {
        font-size: 0.92rem;
        color: #FFBF00;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        font-weight: 600;
        font-family: "Montserrat", sans-serif;
    }
    @keyframes gem-spin {
        to { transform: rotate(360deg); }
    }
</style>

<div id="preloader">
    <div class="preloader-content">
        <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" class="preloader-logo">
        <div class="preloader-spinner"></div>
        <div class="preloader-text">GeMEMIS · Geita Mineral Exhibition</div>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.add('hidden');
            setTimeout(() => { preloader.style.display = 'none'; }, 400);
        }
    });
    // Fallback dismiss after 3 seconds
    setTimeout(function() {
        const preloader = document.getElementById('preloader');
        if (preloader && !preloader.classList.contains('hidden')) {
            preloader.classList.add('hidden');
            setTimeout(() => { preloader.style.display = 'none'; }, 400);
        }
    }, 3000);
</script>
