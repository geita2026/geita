<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Discover Exhibitor</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
        type="text/css">
    <title>{{ config('app.name', 'Login') }}</title>
    @include('WelcomePageLayouts.welcomeCss')
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
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
                <div class="panel table-responsive">

                    <div class=" panel-body ">

                        <div class=" virtual-content col-md-12 form-group">
                            <!-- == Exhibition Time Line == -->
                        @include('layouts.ExhibitionTime')
                        <!-- == Exhibition Time Line == -->
                            <p style="font-size: 2vmin;text-align:justify">
                                To enhance Tanzania's economic performance through trade development and promotion of
                                goods and services for both local and foreign markets, TanTrade organizes different
                                Exhibition events,
                                as listed in the bellow table.
                            </p>
                        </div>



                        <div class=" col-md-12 form-group  zoom">
                            <table class=" table table-bordered table-striped ">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class=" text-center text-bold">EXHIBITION NAME</th>
                                        <th class=" text-center text-bold">EXHIBITION DATE</th>
                                        <th class=" text-center text-bold">LOCAL COMPANIES</th>
                                        <th class=" text-center text-bold">FOREIGN COMPANIES</th>
                                        <th class=" text-center text-bold">GOVERNMENT INSTITUTIONS</th>
                                        <th class=" text-center text-bold">TOTAL COUNTRIES</th>
                                        <th class=" text-center text-bold">EXHIBITION STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>47th Dar es salaam International Trade Fair</td>
                                        <td>28 Jun - 13 July 2023</td>
                                        <td class=" text-center">{{ number_format(3200) }}</td>
                                        <td class=" text-center">{{ number_format(213) }}</td>
                                        <td class=" text-center">{{ number_format(30) }}</td>
                                        <td class=" text-center">{{ number_format(26) }}</td>
                                        <td class=" text-center">
                                            <label class=" label label-success">COMPLETED</label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td>46th Dar es salaam International Trade Fair</td>
                                        <td>28 Jun - 13 July 2022</td>
                                        <td class=" text-center">{{ number_format(2710) }}</td>
                                        <td class=" text-center">{{ number_format(104) }}</td>
                                        <td class=" text-center">{{ number_format(26) }}</td>
                                        <td class=" text-center">{{ number_format(17) }}</td>
                                        <td class=" text-center">
                                            <label class=" label label-success">COMPLETED</label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>45th Dar es salaam International Trade Fair</td>
                                        <td>28 Jun - 13 July 2022</td>
                                        <td class=" text-center">{{ number_format(2031) }}</td>
                                        <td class=" text-center">{{ number_format(72) }}</td>
                                        <td class=" text-center">{{ number_format(19) }}</td>
                                        <td class=" text-center">{{ number_format(14) }}</td>
                                        <td class=" text-center">
                                            <label class=" label label-success">COMPLETED</label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row form-group">
                            <div class=" card col-md-4 zoom">
                                <div class=" card-body">
                                    <iframe width="560" height="315"
                                        src="https://www.youtube.com/embed/fOXlRGlrWfA?si=Ypt1_VIBz3ydSmiO&autoplay=1"
                                        title="YouTube video player" frameborder="0"
                                        allow="accelerometer;clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>


                                </div>
                                <div class=" card-footer" style="font-size:2vmin;text-align:justify">
                                    The 47<sup>th</sup> Dar es salaam International Trade Fair
                                </div>
                            </div>

                            <div class=" card col-md-4 zoom">
                                <div class=" card-body">
                                    <iframe width="560" height="315"
                                        src="https://www.youtube.com/embed/DZi5thbgRh4?si=qiolI0n0GPVXqroS"
                                        title="YouTube video player" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                                <div class=" card-footer" style="font-size:2vmin;text-align:justify">
                                    The 46<sup>th</sup> Dar es salaam International Trade Fair
                                </div>
                            </div>

                            <div class=" card col-md-4 zoom">
                                <div class=" card-body">
                                    <iframe width="560" height="315"
                                        src="https://www.youtube.com/embed/LswD5T7FPzI?si=Sowj6dyb_4w1WT33"
                                        title="YouTube video player" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                                <div class=" card-footer" style="font-size:2vmin;text-align:justify">
                                    The 46<sup>th</sup> Dar es salaam International Trade Fair
                                </div>
                            </div>
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

