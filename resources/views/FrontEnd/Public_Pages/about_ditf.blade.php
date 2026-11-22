<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | About DITF</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <style>
        .animate-charcter {
            text-transform: capitalize;
            background-image: linear-gradient(-225deg,
                    #3700ff 0%,
                    #53ff03 29%,
                    #0026fc 67%,
                    #09ff00 100%);
            background-size: auto auto;
            background-clip: border-box;
            background-size: 200% auto;
            color: #fff;
            background-clip: text;
            text-fill-color: transparent;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textclip 5s linear infinite;
            display: inline-block;
            /* font-size: 600%; */
        }

        @keyframes textclip {
            to {
                background-position: 200% center;
            }
        }
    </style>
    @include('WelcomePageLayouts.welcomeCss')
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
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

                <div class=" panel table-responsive">
                    <div class=" panel-body">
                        <!-- == Exhibition Time Line == -->
                        @include('layouts.ExhibitionTime')
                        <!-- == Exhibition Time Line == -->
                        <div class=" row form-group col-md-12 virtual-content">

                            <div class="">
                                <div class=" col-md-8 zoom">
                                    <h2 class="media-heading text-bold" id="h2"><a href="#"><img
                                                src="{{ asset('images/ditf.jpeg') }}" class=" img-circle" height="50"
                                                width="50" style="border:5px solid rgb(2, 60, 252);"> Dar es salaam
                                            International Trade Fair (DITF) - {{ date('Y') }}</a></h2>
                                    <p style="text-align: justify;">
                                        The Dar es Salaam International Trade Fair (DITF) also known as <q>SabaSaba</q>
                                        takes
                                        place annually on the 28<sup>th</sup> of June at the <a
                                            href="{{ route('ditf_map') }}">Mwalimu J.K.Nyerere Trade Fair Grounds</a>.
                                        It is located along Kilwa Road, 8 km south east of Dar es Salaam in Tanzania.
                                        The Dar es Salaam International Trade Fair is an annual major promotional event
                                        organised by <a href="https://www.tantrade.go.tz/" target="_newtab">Tanzania
                                            Trade Development Authority (TanTrade)</a>. Tanzania Trade Development
                                        Authority (TanTrade) is a
                                        government institution, which was established under the Act No. 4 of 2009 that
                                        repealed the Board of External Trade Act No. 5 of 1978 and the Board of Internal
                                        Trade Act No. 15 of 1973. The first Trade fair was held in 1963. It was
                                        organised by an expert from UKin the
                                        Ministry of Trade and Cooperative Unions by the name Mr. Lucas, with the
                                        assistance
                                        of Mr. Mashamba who was an official of the Ministry.
                                    </p>

                                    <p>
                                        The Tanzania Trade Development Authority (TanTrade), dedicated to establishing
                                        global business
                                        partnership through organizing and managing international and specialised trade
                                        fairs, solo exhibitions, product and market research, prospect development,
                                        Trade
                                        missions, buyer-seller meetings and contact marketing programmes. TanTrade gives
                                        out
                                        regular trade information and offers consultancy services to producers,
                                        exporters
                                        and importers to enable them to effectively participate in the global market
                                        place.
                                    </p>

                                    <p>
                                        The Dar es Salaam International Trade Fair has established itself over the years
                                        as the
                                        shop window for Tanzanian products as well as the East, Central, and the
                                        Southern
                                        African Region. Supported by the services of the Dar es Salaam harbor which
                                        serves
                                        the region effectively, the fair acts as a one-stop center for reaching
                                        countries such
                                        as Kenya, Uganda, Rwanda, Burundi, Democratic Republic of Congo, Zambia, Malawi,
                                        Zimbabwe and Botswana.

                                        The fairs enjoy patronage of the Tanzanian business community both exhibit and
                                        use it as a forum for business exchange.

                                        Participation has ever been on the increase from a mere over 100 companies in
                                        the
                                        the late eighties to over 1,041 companies in 1999, In 2006 a total of 1,526
                                        exhibitors
                                        exhibited at the fair and came from overseas representing over 26 countries,
                                        Tanzania inclusive. Currently, The exhibition is the largest exhibition in
                                        Africa that collects
                                        more than 3,500 local companies, 200 foreign companies, and 26 countries have
                                        attended the 2023 exhibition.
                                    </p>

                                </div>
                                {{-- <a href="#"> --}}
                                <div class="col-md-4 p-20">
                                    <div class=" col-md-12 zoom">
                                        <img src="{{ asset('images/sabasaba1.jpg') }}"
                                            style="width:100%; border-radius:2%">
                                    </div>
                                </div>
                                {{-- </a> --}}

                                <div class=" col-md-12 zoom" style="text-align: justify">
                                    <div class=" col-md-12 text-center text-primary">
                                        <h3>Sectors that participated in the exhibition for the past five years</h3>
                                    </div>
                                    @foreach ($sectors as $item)
                                        <div class=" col-md-4 text-uppercase"><i class=" icon-arrow-right13"></i>
                                            {{ $item->sectorName }}</div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <div class=" row  col-md-12 virtual-content">

                            <div class=" form-group col-md-4 zoom">
                                <a href="{{ route('ditf_map') }}">
                                    <img src="{{ asset('images/sabasaba.jpg') }}" class=" h-25 col-md-12"
                                        style=" border-radius:5%;width:100%;">
                                </a>
                            </div>
                            <div class=" col-md-8 zoom">
                                <h1 id="h2" class=" text-warning"> Why DITF ?</h1>
                                <ul>
                                    <li>It bring product and service producers, suppliers, Sellers and Consumers at one
                                        point,
                                        It's Mwl. Nyerere International Fair Grounds.</li>
                                    <li>We facilitate B2B Meetings for large and medium companies.</li>
                                    <li>Point of cultural excelence for Tanzania and other participant countries.</li>
                                    <li>Business Clinic, Tanzania Authorities officials at one point to save Business
                                        problems.
                                    </li>
                                    <li>Home of enjoyment and refreshment.</li>
                                </ul>
                                <a href="{{ route('space_booking_inquiries.index') }}"
                                    class=" btn btn-block btn-warning">Request Space for Exhibition
                                </a>
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

@include('FrontEnd.Public_Pages.Layouts.scripts')

