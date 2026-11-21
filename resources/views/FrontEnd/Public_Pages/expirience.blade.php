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
                                The Geita Mineral Exhibition Secretariat organises the exhibitions listed below.
                                Each entry shows when it ran and who took part.
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
                                    {{-- Past Geita exhibitions go here, one row each. --}}
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="padding:28px 0;">
                                            Past exhibitions will be listed here.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
