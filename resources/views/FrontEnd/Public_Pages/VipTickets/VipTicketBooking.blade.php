<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | VIP Tickets</title>
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

                        @include('FrontEnd.Public_Pages.VipTickets.TicketMenues')
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
                                <img src="{{ asset('images/ticket1.png') }}" style="width: 100%">
                            </div>
                            <div class=" col-md-8 zoom">
                                <p>
                                    <b>VIP Tickets:- </b>Is a special category of ticket that offers enhanced privileges
                                    and exclusive perks to the ticket holder during the 48<sup>th</sup> Dar es salaam
                                    International Trade Fair.
                                    This package contain the following benefits:-
                                </p>
                                <p>
                                    @foreach ($vipBenefits as $item)
                                        <i class=" icon-check text-success"></i> {{ $item->benefit_name }}<br>
                                    @endforeach
                                </p>
                                <p>
                                    Ticket it costs only <b>1,500,000/= TZS.</b>
                                </p>
                            </div>

                            
                        </div>
                        <div style="text-align: justify">
                            <h5>
                                <b>How To book a Ticket ?</b>
                                
                                <li>
                                    Fill all required customer details, select all tickets of your interest while specifying it's quantity then request Control Number by submiting your
                                    request.
                                </li>
                                <li>
                                    Once we receive your request we process it in two to three minutes, you will receive control Number for making payments through your phone number
                                </li>
                                <li>Process payments, either through mobile money or banks in respect to the given Control Number.</li>
                            <li>Instantly once we receive your payment we send you instraction to download your Ticket(s).</li>
                            
                        <p class=" text-danger"><b>NOTE!</b></p>
                        <p class=" text-danger"> <i class=" icon-arrow-right13"></i> Do not share Instractions about your ticket to any body.</p>
                        <p class=" text-danger"> <i class=" icon-arrow-right13"></i> Customer is allowed to bring printed or the softcopy of the ticket during Gate entry at Mwl J.k Nyrere International Fair Ground(SabaSaba).</p>
                        <p class=" text-danger"><i class=" icon-arrow-right13"></i> Customer must show his or her ticket during gate entrance at Mwl J.k Nyrere International Fair Ground(SabaSaba).</p>
                        </h5>
                        </div>
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
