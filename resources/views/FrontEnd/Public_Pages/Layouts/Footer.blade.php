<!-- ===================== Footer ===================== -->
<footer class="bg-dark text-light mt-5 wow fadeInUp" data-wow-delay="0.1s">

    <!-- Footer Top -->
    <div class="container py-5">
        <div class="row g-5">

            <!-- Contact Information -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-uppercase text-light mb-4 border-bottom pb-2">
                    Get in Touch
                </h5>

                <p class="mb-3 d-flex align-items-start">
                    <i class="bi bi-geo-alt text-primary me-3 fs-5"></i>
                    <span>P.O. Box 5402, Dar es Salaam, Tanzania</span>
                </p>

                <p class="mb-3 d-flex align-items-start">
                    <i class="bi bi-envelope text-primary me-3 fs-5"></i>
                    <a href="mailto:dtp@tantrade.go.tz" class="text-light text-decoration-none">
                        dtp@tantrade.go.tz
                    </a>
                </p>

                <p class="mb-4 d-flex align-items-start">
                    <i class="bi bi-telephone text-primary me-3 fs-5"></i>
                    <a href="tel:+255733-002-014" class="text-light text-decoration-none">
                        +255 733 002 014
                    </a>
                </p>

                <!-- Social Links -->
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-uppercase text-light mb-4 border-bottom pb-2">
                    Quick Links
                </h5>

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="https://www.tantrade.go.tz/"
                           class="text-light text-decoration-none d-flex align-items-center">
                            <i class="bi bi-chevron-right text-primary me-2"></i>
                            TanTrade Official Website
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('index') }}"
                           class="text-light text-decoration-none d-flex align-items-center">
                            <i class="bi bi-chevron-right text-primary me-2"></i>
                            Book Exhibition Space
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Popular Actions -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-uppercase text-light mb-4 border-bottom pb-2">
                    Popular Links
                </h5>

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#"
                           class="text-light text-decoration-none d-flex align-items-center">
                            <i class="bi bi-chevron-right text-primary me-2"></i>
                            Discover Exhibitors
                        </a>
                    </li>

                    <li>
                        <a href="#"
                           class="text-light text-decoration-none d-flex align-items-center">
                            <i class="bi bi-chevron-right text-primary me-2"></i>
                            Vote for Exhibitor
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="bg-primary-dark border-top border-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center py-3">
                    <small class="text-light">
                        &copy; {{ date('Y') }}
                        <a href="https://www.tantrade.go.tz/"
                           class="text-light text-decoration-underline">
                            TanTrade
                        </a>.
                        All Rights Reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>

</footer>
<!-- =================== End Footer =================== -->

<!-- Back to Top -->
<a href="#"
   class="btn btn-primary btn-lg rounded-circle back-to-top">
    <i class="bi bi-arrow-up"></i>
</a>

<!-- JS Libraries -->
<script src="FrontEnd/lib/wow/wow.min.js"></script>
<script src="FrontEnd/lib/easing/easing.min.js"></script>
<script src="FrontEnd/lib/waypoints/waypoints.min.js"></script>
<script src="FrontEnd/lib/counterup/counterup.min.js"></script>
<script src="FrontEnd/lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Main JS -->
<script src="FrontEnd/js/main.js"></script>
