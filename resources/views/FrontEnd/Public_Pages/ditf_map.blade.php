<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="GeMEMIS — the Geita Mineral Exhibition Management Information System. Exhibitor registration, space allocation, invoicing, payment and check-in for the Geita mineral exhibitions.">
    <meta name="keywords"
        content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="author" content="Geita Mineral Exhibition Secretariat">

    <title>{{ env('APP_NAME') }} | SabaSaba Fair Ground</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    @include('WelcomePageLayouts.welcomeCss')
</head>

<body>
    <!-- Page container -->
    <div class="page-container">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">
                <!-- HEADER MENU-->
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- END HEADER MENU-->


                <!-- Search results -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-body">
                            <div class=" panel-heading">
                                <!-- == Exhibition Time Line == -->
                                @include('layouts.ExhibitionTime')
                                <!-- == Exhibition Time Line == -->
                                <h4 class=" text-center  text-bold" style="font-size:1.5vmax">
                                    Dar es salaam International Trade Fair (DITF), Ground Map - {{ date('Y') }}.
                                </h4>
                                <p style="text-align: justify;font-size:2vmin">
                                    The Mwalimu J.K.Nyerere Trade Fair Grounds,It is located along Kilwa Road, 8 km
                                    south east of Dar es Salaam in Tanzania.
                                    The Trade Fair Grounds which is also known as Mwl. J.K.Nyerere Trade Fair Grounds
                                    were inaugurated in 1962, one year after Tanzania gained her independence on 9
                                    December 1961.
                                </p>
                            </div>
                            <div class=" panel-body">

                                <div class=" col-md-12 zoom">
                                    {{-- <iframe
                                        src="{{ asset('TANTRADE GROUND PAVILIONS AND OPEN SPACES final-min.png') }}"
                                        style="width: 100%;" height="600">
                                    </iframe> --}}
                                    <div class="col-md-12">
                                        <img src="{{ asset('TANTRADE GROUND PAVILIONS AND OPEN SPACES final-min.png') }}" style="width: 100%">
                                    </div>
                                    

                                    {{-- <iframe
                                    src="https://www.google.com/maps/d/u/0/embed?mid=1S6NVzbFb0z4oBdRtfDFtCNBl-F81ftY&ehbc=2E312F"
                                    style="width: 100%;" height="600">
                                </iframe> --}}

                                    <div class="col-md-12">
                                        <p><b>KEY:-</b></p><br>
                                        <b class=" bg-orange p-10"><i class=" icon-car"></i></b> <b
                                            class=" ml-10">Packing Area</b><br><br>
                                        <b class=" bg-green-800 p-10"><i class="icon-quill4"></i></b> <b
                                            class=" ml-10">Zoo Area</b><br><br>
                                        <b class=" p-10" style="background-color: #baeef1"><i
                                                class="icon-quill4"></i></b> <b class=" ml-10">Pavilions</b><br><br>
                                        <b class=" p-10" style="background-color: #ecd792"><i
                                                class=" icon-road text-muted"></i></b> <b class=" ml-10">Main
                                            Road</b><br><br>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /search results -->

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->
        @include('layouts.footer')

    </div>
    <!-- /page container -->

</body>

</html>

<!-- Core JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>






