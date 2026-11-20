<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Online Tickets</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">

    <title>{{ config('app.name', 'Login') }}</title>
    @include('WelcomePageLayouts.welcomeCss')

</head>

<body>
    <div class="page-container content">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">
                <!-- Search field -->

                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <div class="panel table-responsive">

                    <div class=" panel-body">

                        <div class=" virtual-content col-md-12 form-group ">
                            
                            <!-- == Exhibition Time Line == -->
                           @include('layouts.ExhibitionTime')
                            <!-- == Exhibition Time Line == -->
                            @include('FrontEnd.Public_Pages.VipTickets.TicketMenues')
                            <div class="alert alert-info alert-styled-right alert-bordered">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Hello Dear customer!</span> Thank you for buying ticket online, If you didn't receive ticket after making payment, please follow bellow instraction to get your Ticket  <a href="#" class="alert-link">super important</a>.
                            </div>
                            <div class="row ">
                                <div class=" col-md-12">
                                  {{-- <label class=" label label-danger"><h5>AVailable Trials:  <b>{{ 5-session('requestCount') }}</b></h5></label> --}}
                                </div>
                                <div class=" col-md-12">
                                    @if (session('requestCount')>=5)
                                <div class="alert alert-danger no-border">
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                    <span class="text-semibold">Oops!</span> We regrate to inform you that, service is temporary unavailable for you due to many attempts, You can contact us via <a href="tel:+255 733-002-014">+255 733-002-014</a> for more support.
                                </div>  
                                @endif
                                </div>
                                <div class="col-lg-12">
                                    <form action="{{ route('myTicket.store') }}" method="POST">
                                        @csrf
                                        <div class=" form-group col-md-5">
                                            <label>Control Number<i class=" text-danger">*</i></label>
                                            <input type="text" name="controlNumber" class=" form-control" placeholder="Enter control NUmber" value="{{ old('controlNumber') }}">
                                            <span class=" text-danger">@error('controlNumber'){{ $message }}@enderror</span>
                                        </div>
                                        <div class=" col-md-5">
                                            <label>Phone Number<i class=" text-danger">*</i></label>
                                            <input type="text"  name="phoneNumber" class=" form-control" placeholder="Enter your phone number" value="{{ old('phoneNumber') }}">
                                            <span class=" text-small text-muted"><i class=" icon-info22"></i>This is phone used when requesting control number.</span>
                                            <span class=" text-danger">@error('phoneNumber'){{ $message }}@enderror</span>
                                        </div>
                                        <div class=" col-md-2">
                                            <label>.</label>
                                            <button class=" btn btn-block btn-primary" @if(session('requestCount')>=5) disabled @endif>Fetch Ticket</button>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- /page content -->
                @include('layouts.footer')

            </div>
        </div>
    </div>

    <!-- /page container -->

</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}



<script type="text/javascript">
    function showLoader() {
        document.getElementById("loader").style.display = 'block';
        document.getElementById('submitButton').style.display = "none";
    }
</script>
