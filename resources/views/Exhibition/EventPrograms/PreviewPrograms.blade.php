<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Event Programs</title>
    <title>{{ config('app.name', 'Event Programs') }}</title>
    @include('WelcomePageLayouts.welcomeCss')
</head>

<body>

    <!-- Page container -->
    <div class="page-container ">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">
                <!-- HEADER MENU-->
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- END HEADER MENU-->
                <!-- Search field -->
                <div class="panel panel-flat table-responsive">

                    <div class=" panel-body">

                        {{-- @can('Download Programs') --}}
                        <div class=" form-group text-right">
                            <a href="{{ route('preview_programs.show', ['preview_program' => 'DITF_Programs.pdf']) }}"
                                class=" btn btn-default text-primary border-primary">Download Progrms <i
                                    class=" icon-download4"></i></a>
                        </div>
                        {{-- @endcan --}}


                        <div class="form-group table-responsive">
                            <table class=" table table-bordered table-striped">
                                <thead>
                                    <tr class="virtual-content ">
                                        <th colspan="7">
                                            <h5 class=" text-center text-bold text-uppercase">
                                                {{ $eventName->event_name }} PROGRAMS FROM
                                                {{ date('M d', strtotime($eventName->event_start_date)) }} TO
                                                {{ date('M d, Y', strtotime($eventName->event_end_date)) }} .</h2>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class=" text-center text-bold">S/N</th>
                                        <th class=" text-center text-bold">PROGRAM TITLE</th>
                                        <th class=" text-center text-bold">PROGRAM DESCRIPTION</th>
                                        <th class=" text-center text-bold">WAHUSIKA</th>
                                        <th class=" text-center text-bold">PRGRAM DATE</th>
                                        <th class=" text-center text-bold">PROGRAM STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($programs as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->program_title }}</td>
                                            <td>{{ $item->program_description }}</td>
                                            <td>{{ 'ALL' }}</td>
                                            <td>{{ date('M d, Y', strtotime($item->program_date)) }}</td>
                                            <td>
                                                @if ($item->program_date > date('Y-m-d'))
                                                    <b class=" label label-block bg-slate p-10">WAITING . . .</b>
                                                @elseif ($item->program_date == date('Y-m-d'))
                                                    <b class=" label label-block  bg-success p-10">INPROGRESS</b>
                                                @elseif ($item->program_date < date('Y-m-d'))
                                                    <b class=" label label-block bg-pink p-10">OVER <i
                                                            class=" icon-check"></i></b>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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
