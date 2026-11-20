<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Sabasaba Special</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <!-- Global stylesheets -->
    @include('WelcomePageLayouts.welcomeCss')
    <style>
        .specialLeft {
            position: relative;
            border-bottom-right-radius: 1000px;
            border-top-right-radius: 1000px;
            box-shadow: 10px 10px 30px grey;
            box-sizing: content-box;
            border: 1px solid rgb(114, 113, 113);
            background-image: radial-gradient(#ecf0f0, rgb(255, 255, 255), rgba(207, 219, 30, 0.479));
        }

        .specialLeft:hover {
            background-color: rgb(255, 255, 255);
        }


        .specialRight {
            position: relative;
            border-bottom-left-radius: 1000px;
            border-top-left-radius: 1000px;
            box-shadow: 10px 10px 30px grey;
            box-sizing: content-box;
            /* text-align: right; */

            border: 1px solid rgb(114, 113, 113);
            background-image: radial-gradient(#ecf0f0, rgb(255, 255, 255), rgba(207, 219, 30, 0.479));
        }

        .specialRight:hover {
            background-color: rgb(255, 255, 255);
        }
    </style>
</head>

<body>
    <!-- Page container -->
    <div class="page-container ">
        <!-- Page content -->
        <div class="page-content">
            <!-- Main content -->
            <div class="content-wrapper">
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- Search field -->
                <div class="panel table-responsive ">

                    <div class=" panel-body">
                        <div class=" row form-group col-md-12">

                            <!-- == Exhibition Time Line == -->
                        @include('layouts.ExhibitionTime')
                        <!-- == Exhibition Time Line == -->

                            <div class=" headerBackground form-group">
                                <p style="font-size:3vmax; text-align:center">
                                    <b>SabaSaba Special is Here . . .! <i class=" icon-point-down"></i></b>
                                </p>
                            </div>

                            <div class=" row col-md-12">
                                <div class=" col-md-8 form-group">
                                    <div class=" zoom" style="font-size:2.5vmin">

                                        <p style="font-size:2.5vmin;">
                                            <b
                                                style="background-color: blue;color:white;border-radius:10px;padding:0.5%">1</b>
                                            <b
                                                style=" font-size:3.5vmin;font-family:Cambria;color:blue;">Networking.</b>

                                        <div style="font-size:2.5vmin;text-align:justify">
                                            Dar es salaam International Trade Fair, provide a platform for networking
                                            with industry professionals,
                                            potential clients, suppliers, and partners. Building and maintaining
                                            relationships can lead to collaborations, business partnerships, and
                                            increased visibility within the industry.
                                        </div>
                                        </p>

                                    </div>
                                </div>

                                <div class=" col-md-4 zoom">
                                    <a href="{{ route('space_booking_inquiries.index') }}">
                                        <img src="{{ asset('images/Networking.jpg') }}" style="width: 100%; border-radius:2%">
                                    </a>
                                </div>
                              
                            </div>

                                <div class=" row">
                                    <div class=" col-md-4 zoom">
                                        <a href="{{ route('space_booking_inquiries.index') }}">
                                            <img src="{{ asset('images/B2B.png') }}" style="width: 100%; border-radius:2%">
                                        </a>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <div class="  zoom ">
                                            <p style="font-size:2.5vmin;">
                                                <b
                                                    style="background-color: blue;color:white;border-radius:10px;padding:0.5%">2</b>
                                                <b style=" font-size:3.5vmin;font-family:Cambria;color:blue;">Business-to-Business
                                                    (B2B) meetings.</b>
    
                                            <div style="font-size:2.5vmin;text-align:justify">
                                                Business-to-Business (B2B) meetings are crucial for fostering partnerships,
                                                networking, and collaboration. Whether you're organizing or attending B2B
                                                meetings, Prepare for B2B meeting during Dar es salaam International Trade
                                                Fair.
                                            </div>
                                            </p>
                                        </div>
                                    </div>
                                    
                                </div>


                                <div class=" row">
                                    <div class="col-md-8 form-group">
                                        <div class="   zoom">
                                            <p style="font-size:2.5vmin;padding:1%;">
                                                <b
                                                    style="background-color: blue;color:white;border-radius:10px;padding:0.5%">3</b>
                                                <b style=" font-size:3.5vmin;font-family:Cambria;color:blue;">Business
                                                    Clinic.</b>
    
                                            <div style="font-size:2.5vmin;text-align:justify">
                                                All Government Authorities in business chain at one point,
                                                Many exhibitors gets opportunity to attend seminars, workshops, and
                                                presentations.This provides opportunity to learn about industry trends,
                                                advancements, and best practices,Tax and Investment issues. This knowledge
                                                can be beneficial for
                                                both personal and professional development.
                                            </div>
                                            </p>
                                        </div>
                                    </div>
                                    <div class=" col-md-4 zoom">
                                        <a href="{{ route('space_booking_inquiries.index') }}">
                                            <img src="{{ asset('images/businessClinic.png') }}" style="width: 100%; border-radius:2%">
                                        </a>
                                    </div>
                                </div>


                              <div class=" row">
                                <div class=" col-md-4 zoom">
                                    <a href="{{ route('space_booking_inquiries.index') }}">
                                        <img src="{{ asset('images/Brand.png') }}" style="width: 100%; border-radius:2%">
                                    </a>
                                </div>
                                <div class="col-md-8 form-group">
                                    <div class="   zoom">
                                        <p style="font-size:2.5vmin;padding:1%;">
                                            <b
                                                style="background-color: blue;color:white;border-radius:10px;padding:0.5%">4</b>
                                            <b style=" font-size:3.5vmin;font-family:Cambria;color:blue;">Brand
                                                Exposure.</b>

                                        <div style="font-size:2.5vmin;text-align:justify;">
                                            Dar es salaam International Trade Fair, Brings together More than 2,000
                                            companies, 40,000 visitors at one point at a time, This allows businesses to showcase
                                            their products or services to a targeted audience. This exposure can help in
                                            building brand awareness and recognition among potential customers
                                        </div>
                                        </p>
                                    </div>
                                </div>

                              </div>
                            </div>

                        </div>
                    </div>

                </div>


            </div>
            <!-- /page content -->
            @include('layouts.footer')

        </div>
        <!-- /page container -->

</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}
