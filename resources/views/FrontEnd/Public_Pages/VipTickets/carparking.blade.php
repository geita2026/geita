<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Car Parking Tickets</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <meta name="description"
        content="Trade Fair is the web application system wich manages and control all largest trade
Fair in Tanzania, such as Dar es salaam International Trade Fair (DITF), Tanzania industirial exhibition, e.t.c ">
    <meta name="keywords"
        content="trade fair, sabasaba, DITF, maonesho, viwanja vya maonesho, fair grounds, tradefair,Tanzania,">
    <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">

    <title>{{ config('app.name', 'Login') }}</title>
    @include('WelcomePageLayouts.welcomeCss')
    <style>
        .video-container {
            /* height: 300px;
    width: 300px; */
            position: relative;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: cover;
            z-index: 0;
        }

        /* Just styling the content of the div, the *magic* in the previous rules */
        .video-container .caption {
            z-index: 1;
            position: relative;
            text-align: center;
            color: #dc0000;
            padding: 10px;
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

                        <!-- == Exhibition Time Line == -->
                        @include('layouts.ExhibitionTime')
                        <!-- == Exhibition Time Line == -->

                        <div class=" row form-group col-md-12">
                            <div class=" form-group">
                                <h4 style=" text-align:justify">
                                    A heartfelt welcome awaits you! We are delighted that you'll be gracing us with your
                                    presence. Your visit brings a sense of joy and anticipation to our Exhibition
                                    Event. Whether
                                    it's your first time or a return visit, know that you are a cherished guest, and we
                                    look forward to creating unforgettable moments together.
                                    <b>Here's to a wonderful time shared and memories
                                        made during your visit!</b>
                                </h4>

                            </div>

                            <div class=" col-md-4 zoom">
                                <img src="{{ asset('images/carParking1.png') }}" style="width: 100%">
                            </div>
                            <div class=" col-md-8 zoom">
                                <p>
                                    <b>Electronic Car parking Ticket:- </b>Is a special ticket that offers access to
                                     parking area at the 48<sup>th</sup> Dar es salaam
                                    International Trade Fair ground.
                                   
                                </p>
                               
                                <p>
                                    Ticket it costs only <b>10,000/= TZS. for only one day parking</b>
                                </p>
                            </div>

                            
                        </div>
                        <div style="text-align: justify">
                            <h5>
                                <b>How To book a Ticket ?</b>
                                
                                <li>
                                    Fill all required customer details and request Control Number by submiting your
                                    information,
                                    Once we receive your request we send to you a verification link for your
                                    request,
                                    then click the link, at that time you will receive control Number for the payment
                                </li>
                                <li>Processing payments, using your control number provided,When we receive your
                                    payment
                                    payments, we will
                                    send Ticket to you through your email.
                                </li>
                            </h5>
                        </div>
                    </div>

                    <div class=" col-md-12 form-group">
                        @include('FrontEnd.Public_Pages.VipTickets.parkingBookingForm')
                    </div>

                </div>


                <!-- /main content -->

            </div>
            <!-- /page content -->
            @include('layouts.footer')

        </div>
        <!-- /page container -->

</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}
