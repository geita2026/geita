<!-- ===================== GeMEMIS GOLDEN FOOTER ===================== -->
<footer class="gememis-footer mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Brand & Mission -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" style="height:56px;">
                </div>
                <h6 style="color:#FFBF00;font-weight:700;letter-spacing:0.04em;">Geita Mineral Exhibition Secretariat</h6>
                <p style="color:rgba(255,255,255,0.7);font-size:13.5px;line-height:1.7;">
                    GeMEMIS runs the Geita mineral exhibitions end to end. Registration, applications, the floor
                    plan, invoicing, payment and check-in &mdash; one system, one record of every space on the
                    grounds.
                </p>
            </div>

            <!-- Contact Information -->
            {{-- Secretariat contact details: replace the e-mail and hours with the official ones. --}}
            <div class="col-lg-3 col-md-6">
                <h5>Get in Touch</h5>
                <ul class="list-unstyled" style="font-size:13.5px;line-height:2.1;color:rgba(255,255,255,0.75);">
                    <li>
                        <i class="fa fa-map-marker text-warning me-2"></i>
                        <span>Geita Mineral Exhibition Grounds, Geita, Tanzania</span>
                    </li>
                    <li>
                        <i class="fa fa-envelope text-warning me-2"></i>
                        <a href="mailto:info@gememis.go.tz">info@gememis.go.tz</a>
                    </li>
                    <li>
                        <i class="fa fa-clock-o text-warning me-2"></i>
                        <span>EAT (UTC+3) 08:00 - 18:00</span>
                    </li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5>Quick Links</h5>
                <ul class="list-unstyled" style="font-size:13.5px;line-height:2.1;">
                    <li><a href="{{ route('index') }}"><i class="fa fa-angle-right text-warning me-2"></i> Home</a></li>
                    <li><a href="{{ route('About_ditf') }}"><i class="fa fa-angle-right text-warning me-2"></i> About the exhibition</a></li>
                    <li><a href="{{ route('ditf_map') }}"><i class="fa fa-angle-right text-warning me-2"></i> Map of Geita</a></li>
                    <li><a href="{{ route('exhibition_space_booking.index') }}"><i class="fa fa-angle-right text-warning me-2"></i> Book space</a></li>
                    <li><a href="{{ route('discover_exhibitor.index') }}"><i class="fa fa-angle-right text-warning me-2"></i> Discover exhibitors</a></li>
                </ul>
            </div>

            <!-- Digital Services -->
            <div class="col-lg-3 col-md-6">
                <h5>Digital Services</h5>
                <ul class="list-unstyled" style="font-size:13.5px;line-height:2.1;">
                    <li><a href="{{ route('login') }}"><i class="fa fa-lock text-warning me-2"></i> Exhibitor sign in</a></li>
                    <li><a href="{{ route('registration') }}"><i class="fa fa-user-plus text-warning me-2"></i> Create an account</a></li>
                    <li><a href="{{ route('sponsorship_package_groups') }}"><i class="fa fa-trophy text-warning me-2"></i> Sponsorship packages</a></li>
                    <li><a href="{{ route('terms_and_condition.index') }}"><i class="fa fa-shield text-warning me-2"></i> Terms &amp; conditions</a></li>
                </ul>
            </div>
        </div>

        @if (empty($simpleFooter))
            <div style="height:1px;background:linear-gradient(90deg, transparent, rgba(255, 191, 0,0.35), transparent);margin:35px 0 25px;"></div>
        @else
            <hr class="geita-footer-divider">
        @endif

        <div class="text-center">
            <p style="font-size:12px;color:rgba(255,255,255,0.45);margin:0;">
                &copy; {{ date('Y') }} Geita Mineral Exhibition Secretariat. All Rights Reserved.
                Geita Mineral Exhibition Management Information System (GeMEMIS).
            </p>
        </div>
    </div>
</footer>
