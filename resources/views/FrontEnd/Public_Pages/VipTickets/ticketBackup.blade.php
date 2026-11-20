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
                            <div >
                              <h1><b>SabaSaba Online Ticket Booking</b></h1>
                            </div>
                            <div class="  col-md-12">
                                <div class=" col-md-4 zoom">
                                    <img src="{{ asset('images/ticketOnline.png') }}" style="width: 100%" >
                                </div>
                                <div class=" col-md-8 zoom">
                                    <h4 style="text-align: justify">
                                        Why online Ticket ?
                                        <li style="text-align: justify"><b>Time-Saving</b>, Quick and Efficient: Purchasing tickets online is a fast
                                            process, saving customers time that would be spent waiting in line at a physical
                                            ticket counter at Sabasaba area.
                                            Instant Confirmation: Customers receive immediate confirmation of their ticket.
                                        </li>
                                        <li style="text-align: justify">
                                            <b>Convenience</b>, Accessibility: Customers can purchase tickets from the
                                            comfort of their homes
                                            using a computer or mobile device. This eliminates the need to visit <a
                                                href="{{ route('ditf_map') }}">Mwl. Nyerere Fair Ground</a>.
    
                                        </li>
                                        <li style="text-align: justify">
                                            <b>24/7 Availability</b>: Online ticketing platforms are available around the
                                            clock,
                                            allowing customers to buy tickets at any time
                                        </li>
                                        <li style="text-align: justify">
                                            <b>Real-Time Updates:</b>
                                            All visitors with online Tickets, will be able to receive real-time updates and
                                            notifications about the event, such
                                            as changes in schedule or venue, enhancing their overall experience and time
                                            table.
                                        </li>
                                    </h4>
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
                            @include('FrontEnd.Public_Pages.VipTickets.BookTicketForm')
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

<script type="text/javascript">
    $("#sabasabaDay").hide();
    $("#dateValues").change(function() {
        if ($(this).val() == "07-07-2024") { //for functional Halls
            $("#sabasabaDay").show();
            $("#normalDay").hide();
        } else {
            $("#sabasabaDay").hide();
            $("#normalDay").show();
        }
    });

    $(document).ready(function() {
    // Event handler for the button click
    $("#submitButton").click(function() {
      // Get the entered data from the input field
      var enteredData = $("#myInput").val();

      // Do something with the entered data
      console.log("Entered data: " + enteredData);
    });
  });

</script>
