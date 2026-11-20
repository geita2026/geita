<div class="header-inner" style="border-bottom: 5px solid rgb(50, 172, 209)">
    <div class="container">
        <div class="inner">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-12">
                    <!-- Start Logo -->
                    <div class="logo">
                        <a href="{{ route('expo2025') }}"><img src="{{ asset('images/logo.jpg') }}" alt="#"
                                height="70" width="100"></a>
                    </div>
                    <!-- End Logo -->

                    <!-- Mobile Nav -->
                    <div class="mobile-nav">

                    </div>
                    <!-- End Mobile Nav -->

                </div>
                <div class="col-lg-10 col-md-9 col-12">
                    <!-- Main Menu -->
                    <div class="main-menu">
                        <nav class="navigation">
                            <ul class="nav menu">
                                <li class="{{ Route::is('expo2025') ? 'active' : '' }}"><a href="{{ route('expo2025') }}">Tanzania In Expo2025 Osaka Japan
                                    </a></li>

                                    {{-- <li class="{{ Route::is('aboutExpo') ? 'active' : '' }}"><a href="{{ route('aboutExpo') }}">Kuhusu Expo
                                    </a></li> --}}
                               
                                {{-- <li class="{{ Route::is('tanzania_pavilion') ? 'active' : '' }}"><a href="{{ route('tanzania_pavilion') }}">
                                        Banda la Tanzania </a></li>
                                        
                                        <li class="{{ Route::is('expo_registration') ? 'active' : '' }}"><a href="{{ route('expo_registration') }}">
                                            Jisajili Kushiriki </a></li> --}}
                                        
                            </ul>
                        </nav>
                    </div>
                    <!--/ End Main Menu -->
                    
                </div>
                {{-- <div class="col-lg-2 col-12">
                    <div class="get-quote">
                        <a href="{{ route('login') }}" class="btn">Login</a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
