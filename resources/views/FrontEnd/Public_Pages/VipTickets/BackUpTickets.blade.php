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

                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="tabbable">
                                        <div class="tab-content">
                                            @include('FrontEnd.Public_Pages.VipTickets.TicketMenues')

                                            <div class="tab-pane fade in active" id="bookTicket">
                                                <!-- Timeline -->
                                                <div class="timeline timeline-left content-group">
                                                    <div class="timeline-container">

                                                        <!-- Sales stats -->
                                                        <div class="timeline-row">
                                                            <div class="timeline-icon">
                                                                <a href="#"><img
                                                                        src="{{ asset('images/logo.jpg') }}"
                                                                        alt=""></a>
                                                            </div>

                                                            <div
                                                                class="panel panel-flat timeline-content table-responsive">
                                                                <div class="panel-heading">
                                                                    <h6 class="panel-title">Online SabaSaba Tickets</h6>
                                                                    <div class="heading-elements">
                                                                        <span class="heading-text"><i
                                                                                class=" icon-calendar2 position-left text-warning"></i>
                                                                            48<sup>th</sup> Dar es salaam International
                                                                            Trade Fair (SabaSaba)</span>

                                                                        <ul class="icons-list">

                                                                            <li><a href="{{ route('online_tickets.edit', ['online_ticket' => session('ticketRequest') ?? date('isdymsiymdsismd')]) }}"
                                                                                    class=" text-primary text-bold">refresh
                                                                                    <i class=" icon-reload-alt"></i></a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <div class=" container p-10">
                                                                    <div class="alert alert-primary no-border">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="alert"><span>&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <span class="text-bold">Hello customer!</span> get your SabaSaba Entry Ticket
                                                                        in two steps i.e
                                                                        get control number, pay for it then you will
                                                                        receive your Ticket in one to two minutes through your phone number and email submited through the form bellow. Let's
                                                                        Book Now <i
                                                                            class=" icon-arrow-down-left3 text-primary"></i>.
                                                                    </div>
                                                                    <div class=" col-md-6 animate">
                                                                        @include('FrontEnd.Public_Pages.VipTickets.BookTicketForm')
                                                                    </div>


                                                                    @if (session('item'))
                                                                        <div class=" col-md-6">
                                                                            <table
                                                                                class=" table table-active table-bordered">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th
                                                                                            class=" text-center text-bold">
                                                                                            Ticket Name</th>
                                                                                        <th
                                                                                            class=" text-center text-bold">
                                                                                            Quantity</th>
                                                                                        <th
                                                                                            class=" text-center text-bold">
                                                                                            Sub Total</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @php
                                                                                        $total = 0;
                                                                                    @endphp
                                                                                    @foreach ($requests as $values)
                                                                                        <tr>
                                                                                            <td>{{ $values->package_discriptions }}
                                                                                                ({{ number_format($values->package_cost) }}
                                                                                                TZS)
                                                                                            </td>
                                                                                            <td class=" text-center">
                                                                                                {{ number_format($values->quantity) }}
                                                                                            </td>
                                                                                            <td class=" text-center">
                                                                                                {{ number_format($subtotal = $values->quantity * $values->package_cost) }}
                                                                                                TZS
                                                                                                <a href="{{ route('remove_ticket', ['id' => $values->requestId]) }}"
                                                                                                    class=" text-danger text-bold ml-20"
                                                                                                    title="Remove Item"><i
                                                                                                        class=" icon-bin"></i></a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        @php
                                                                                            $total = $total + $subtotal;
                                                                                        @endphp
                                                                                    @endforeach
                                                                                    <tr class=" text-bold">
                                                                                        <td colspan="2"
                                                                                            class=" text-right">Total
                                                                                            Cost
                                                                                        </td>
                                                                                        <td>{{ number_format($total) }}
                                                                                            TZS
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td colspan="2">
                                                                                            After confirming you will
                                                                                            receive Control Number in
                                                                                            one to thwo minutes
                                                                                        </td>
                                                                                        <td class=" text-right">
                                                                                            <button
                                                                                                class=" btn btn-primary"
                                                                                                data-toggle="modal"
                                                                                                data-target="#confirmTicket">
                                                                                                Confirm Ticket</button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <div id="confirmTicket" class="modal fade"
                                                                            aria-modal="true">
                                                                            <div class="modal-dialog modal-md">
                                                                                <div class="modal-content">
                                                                                    <div
                                                                                        class="modal-header bg-primary">
                                                                                        <button type="button"
                                                                                            class="close"
                                                                                            data-dismiss="modal">&times;</button>
                                                                                        <h5 class="modal-title">Je!
                                                                                            una uhakika unataka
                                                                                            kuthibitisha taarifa hizi ?</h5>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <form
                                                                                            action="{{ route('online_tickets.store') }}"
                                                                                            method="POST">
                                                                                            @csrf

                                                                                            <div class=" form-group">

                                                                                                <p
                                                                                                    class=" text-danger text-justify text-bold">
                                                                                                     Tutakutumia Control Numba na ticket baada ya malipo hivyo hakikisha namba ya simu ni Sahihi.
                                                                                                </p>
                                                                                                <h6>Full Name <i
                                                                                                        class=" text-danger">*</i>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        name="fullName"
                                                                                                        class=" form-control"
                                                                                                        value="{{ $values->requestedBy }}"
                                                                                                        required>
                                                                                                </h6>
                                                                                                {{-- <h6>Email<i
                                                                                                        class=" text-danger">*</i>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        name="email"
                                                                                                        class=" form-control"
                                                                                                        value="{{ $values->email }}">
                                                                                                </h6> --}}

                                                                                                <h6>PhoneNumber (<span
                                                                                                        class=" text-muted">Must
                                                                                                        start with
                                                                                                        255</span>) <i
                                                                                                        class=" text-danger">*</i>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        name="phoneNumber"
                                                                                                        class=" form-control"
                                                                                                        value="{{ $values->phoneNumber }}"
                                                                                                        required>
                                                                                                </h6>
                                                                                                @if ($values->plateNumber != null)
                                                                                                    <h6>Car Plate Number
                                                                                                        <i
                                                                                                            class=" text-danger">*</i>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            name="plateNumber"
                                                                                                            class=" form-control"
                                                                                                            value="{{ $values->plateNumber }}">
                                                                                                    </h6>
                                                                                                @endif
                                                                                                <h6>Ticket Total Cost
                                                                                                    (TZS) <i
                                                                                                        class=" text-danger">*</i>
                                                                                                    <input
                                                                                                        type="text"
                                                                                                        name="ticketCost"
                                                                                                        value="{{ number_format($total) }}"
                                                                                                        class=" form-control"
                                                                                                        readonly>
                                                                                                </h6>
                                                                                            </div>
                                                                                            <input type="hidden"
                                                                                                name="requestId"
                                                                                                value="{{ session('ticketRequest') }}">
                                                                                            <div class=" text-right">
                                                                                                <img src="{{ asset('images/loading2.gif') }}"
                                                                                                    id="loader"
                                                                                                    style="display: none;"
                                                                                                    height="100"
                                                                                                    width="100">
                                                                                                    
                                                                                                <a href="{{ route('remove_ticket',['id'=>session('requestNo')]) }}"  class=" btn btn-danger">Sitisha <i class=" icon-trash"></i></a>


                                                                                                <button type="submit"
                                                                                                    id="submitButton"
                                                                                                    class="btn btn-primary ml-20"
                                                                                                    onclick="showLoader()">thibitisha</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>

                                                                                   
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        @include('FrontEnd.Public_Pages.VipTickets.aboutSabasabaTickets')
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /sales stats -->
                                                    </div>
                                                </div>
                                                <!-- /timeline -->

                                            </div>


                                            <div class="tab-pane fade" id="howToBookTicket">
                                                <!-- Timeline -->
                                                <div class="timeline timeline-left content-group">
                                                    <div class="timeline-container">

                                                        <!-- Sales stats -->
                                                        <div class="timeline-row">
                                                            <div class="timeline-icon">
                                                                <a href="#"><img
                                                                        src="{{ asset('images/logo.jpg') }}"
                                                                        alt=""></a>
                                                            </div>

                                                            <div class="panel panel-flat timeline-content">
                                                                <div class="panel-heading">
                                                                    <h6 class="panel-title">How to buy Ticket ?</h6>
                                                                    <div class="heading-elements">
                                                                        <span class="heading-text"><i
                                                                                class="icon-history position-left text-success"></i>
                                                                            Updated 3 hours ago</span>

                                                                        <ul class="icons-list">
                                                                            <li><a data-action="reload"></a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <div class="panel-body">
                                                                    @include('FrontEnd.Public_Pages.VipTickets.howToBookTicket')
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /sales stats -->
                                                    </div>
                                                </div>
                                                <!-- /timeline -->

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
        </div>
    </div>

    <!-- /page container -->

</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}

@foreach ($carParking as $item)
    <script type="text/javascript">
        $("#plateNumber").hide();
        $("#ticketSelections").change(function() {
            if ($(this).val() == "{{ $item->vip_package_id }}") { //for functional Halls
                $("#plateNumber").show();
            } else {
                $("#plateNumber").hide();
            }
        });

        // Date Control
        $("#SabaSabaDay").hide();
        $("#dateValues").change(function() {
            if ($(this).val() == "07-07-2024") { //for functional Halls
                $("#SabaSabaDay").show();
                $("#NormalDay").hide();
            } else {
                $("#SabaSabaDay").hide();
                $("#NormalDay").show();
            }
        });
        // End Date Control
    </script>
@endforeach


<script type="text/javascript">
    function showLoader() {
        document.getElementById("loader").style.display = 'block';
        document.getElementById('submitButton').style.display = "none";
    }
</script>

