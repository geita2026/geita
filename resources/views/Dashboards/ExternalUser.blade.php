<div class="panel">
    <div class="panel-body">

        {{-- Event Context --}}
        @include('layouts.EventControl')

        <style>
            .services-wrapper {
                margin-top: 20px;
            }

            .services-title {
                font-size: 15px;
                font-weight: 700;
                color: #059669;
                letter-spacing: .06em;
                text-transform: uppercase;
                margin-bottom: 20px;
            }

            .service-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 22px 18px;
                height: 100%;
                box-shadow: 0 6px 18px rgba(0,0,0,.06);
                text-align: center;
                transition: all .25s ease;
                border: 1px solid #e5e7eb;
            }

            .service-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 30px rgba(0,0,0,.08);
            }

            .service-icon {
                width: 56px;
                height: 56px;
                border-radius: 14px;
                margin: 0 auto 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                color: #ffffff;
                background: linear-gradient(135deg, #4f46e5, #6366f1);
            }

            .service-title {
                font-size: 13px;
                font-weight: 700;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #111827;
                margin-bottom: 8px;
            }

            .service-desc {
                font-size: 13px;
                color: #6b7280;
                line-height: 1.6;
            }
        </style>

        <div class="services-wrapper">
            <div class="services-title">Available Services</div>

            <div class="row g-4">

                {{-- BOOK SPACE --}}
                @can('BOOK_SPACE')
                <div class="col-md-3">
                    <a href="{{ route('booking.index') }}" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="icon-cart-add"></i>
                            </div>
                            <div class="service-title">Book Space</div>
                            <div class="service-desc">
                                Select and reserve your preferred exhibition space.
                            </div>
                        </div>
                    </a>
                </div>
                @endcan

                {{-- MY INVOICES --}}
                <div class="col-md-3">
                    <a href="{{ route('adviceNotes.create') }}" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="icon-cash"></i>
                            </div>
                            <div class="service-title">My Invoices</div>
                            <div class="service-desc">
                                View and track all issued invoices.
                            </div>
                        </div>
                    </a>
                </div>

                {{-- MY PAYMENTS --}}
                @can('MANAGE_PAYMENT')
                <div class="col-md-3">
                    <a href="{{ route('payments.index') }}" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="icon-cash3"></i>
                            </div>
                            <div class="service-title">My Payments</div>
                            <div class="service-desc">
                                Access payment history and receipts.
                            </div>
                        </div>
                    </a>
                </div>
                @endcan

                {{-- SERVICE BOOKING --}}
                @can('BOOK_SERVICES')
                <div class="col-md-3">
                    <a href="{{ route('book-services.index') }}" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="icon-cart-add"></i>
                            </div>
                            <div class="service-title">Service Booking</div>
                            <div class="service-desc">
                                Book additional exhibition services ie Extra badges.
                            </div>
                        </div>
                    </a>
                </div>
                @endcan

                {{-- EXHIBITION BADGES --}}
                @can('MANAGE_BADGES_MENU')
                <div class="col-md-3">
                    <a href="{{ route('exhibitors_list.index') }}" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="icon-profile"></i>
                            </div>
                            <div class="service-title">Exhibition Badges</div>
                            <div class="service-desc">
                                Generate and manage exhibitor badges.
                            </div>
                        </div>
                    </a>
                </div>
                @endcan

                {{-- JUDGING PROCESS --}}
                @can('CONFIRM_JUDGING')
                <div class="col-md-3">
                    <a href="{{ route('exhibitor_judging.index') }}" class="text-decoration-none">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="icon-profile"></i>
                            </div>
                            <div class="service-title">Judging Process</div>
                            <div class="service-desc">
                                Confirm and manage judging participation.
                            </div>
                        </div>
                    </a>
                </div>
                @endcan

            </div>
        </div>

    </div>
</div>

{{-- external users --}}
{{-- ********************* JUDGING ********************* --}}
