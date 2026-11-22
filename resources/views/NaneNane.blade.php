<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Farmerâ€™s Day dates back to the early 1980s when the Tanzanian Government initiated the celebration to recognize and appreciate the contributions of farmers to the national economy. The Nanenane exhibition is an annual event held in Tanzania to commemorate the founding of Farmers' Day, celebrating achievements in agriculture, livestock, and fisheries sectors. It takes place from August 1st to 8th each year and features displays and demonstrations from various sectors related to Agriculture, Livestock and Fisheries, including innovations, technologies, and practices aimed at improving productivity and sustainability.">
    <meta name="keywords"
        content="trade fair, sabasaba, DITF, maonesho, viwanja vya maonesho, fair grounds, tradefair,Tanzania,Exhibition">
    <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">
    <title>{{ env('APP_NAME') }} | HOME</title>
    <style>
       
    </style>
    @include('WelcomePageLayouts.welcomeCss')
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body>

    {{-- Event Info --}}
    @foreach ($event as $exhibition)
    @endforeach
    {{-- End Event Info --}}
    {{-- <img src="{{ asset('images/tz-flag.jpg') }}" style="position:absolute; width:100%; height:100%; opacity:10%;"> --}}
    <!-- Page container -->
    <div class="page-container">
        <!-- Page content -->
        <div class="page-content">
            <!-- Main content -->
            <div class="content-wrapper">
                <div class="">
                    @include('WelcomePageLayouts.welcomeHeaderMenu')
                </div>
                {{-- end nav menu --}}
                <div class="col-md-12">
                    
                    {{-- Video --}}
                    <div class=" row col-md-12  ">

                        <div class="col-md-12  form-group ">
                            <div class=" card ">
                                <div class=" card-body">
                                    @include('Support.suport')
                                    <!-- Indicators -->
                                    <div class=" col-md-12 zoom">

                                       
                                            <div class=" text-primary" style="font-size:4vmin;">
                                              Karibu Kwenye Maonesho ya
                                               kimataifa ya kilimo - 2024.<br>
                                              <q style="font-size: 3vmin">Chagua Viongozi Bora wa Serikali za Mitaa kwa Maendeleo ya  
                                                Kilimo Mifugo na Uvuvi</q>
                                              </div>
                                              
                                              
                                            

                                        {{-- <b class="animate-charcter"
                                        style="font-size:4vmin;font-family: monospace;color:black;">
                                            <b>
                                               Chagua Viongozi Bora wa Serikali za Mitaa kwa Maendeleo ya Kilimo Mifugo
                                                na Uvuvi
                                            </b>
                                        </b><br> --}}
                                        <div class="text-left form-group">
                                            <b style="font-size:2.5vmin;color:rgb(98, 99, 99)">
                                                <i class=" icon-star-empty3"></i> 50+ Foreign Companies from more
                                                than 26 Countries.<br>
                                                <i class=" icon-star-empty3"></i> 1,500+ Local companies.<br>
                                                <i class=" icon-users4"></i> Over 100,000 Visitors at one point<br>
                                                <i class=" icon-location3"></i> <a href="#">At
                                                    NaneNane Fair
                                                    Ground in Dodoma, Tanzania</a> Do
                                                not plan to miss.<br>
                                                <i class=" icon-calendar"></i> From 1<sup>st</sup> August To
                                                8<sup>th</sup> August 2024.
                                            </b><br>

                                        </div>




                                        <div class=" col-md-12" style="color:green">
                                            <h4 class=" text-left">
                                                <b style="color:black">For any question or help
                                                    feel
                                                    free to comunicate to us via <i class=" icon-point-down"></i></b>
                                                <br>
                                                <b>For How to get space Only: +255 784 994 615</b><br>
                                                <b>For Badges Only: +255 753 427 137 / +255 714 569 149 (Whatsapp Only)</b><br>
                                                
                                            </h4>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            {{-- end wrapper for slide --}}
                        </div>
                    </div>

                    {{-- sponsors --}}
                    <div class="form-group col-md-12 p-20">

                        
                        <div class="text-primary">
                            <h1 style="font-size:2vmin">A heartfelt thank you to our amazing sponsors. Your generosity and commitment are inspiring, and we are incredibly thankful for your support.</h1>
                        <hr>
                        </div>
                        @include('WelcomePageLayouts.sponsors')
                    </div>
                    {{--  End sponsors --}}




                    {{-- footer --}}
                    <div class="text-center">
                        @include('layouts.footer')
                    </div>
                    {{-- end footer --}}
                </div>



            </div>
        </div>
    </div>
</body>

</html>




{{-- login --}}
@include('auth.login')
{{-- end login --}}

{{-- forget password --}}
@include('auth.forget_password')
{{-- forget password --}}

{{-- create new Account --}}
{{-- @include('auth.register') --}}
{{-- Create new Account --}}
@include('WelcomePageLayouts.welcomeJs')


<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>

   
