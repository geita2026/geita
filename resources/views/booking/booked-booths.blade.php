<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
    <style>
        .timer {
            text-align: center;
            background: #3b96e0;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .timer span {
            display: block;
            font-size: 2vmax;
        }
    </style>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-home position-left"></i>Home</a></li>
                <li class="active">View All Booked space</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body table-responsive">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class="text-danger">
                            <li class=" alert">{{ $error }}</li>
                        </span>
                    @endforeach
                @endif

                <div class=" row">
                    <div class=" form-group col-md-6">
                        {{-- <div class="timer">
                            <span id="countdown"> 00:00:00 </span>
                        </div> --}}
                    </div>
                    <div class=" form-group text-right col-md-6">
                        <a data-toggle="modal" data-target="#additional-service" class=" btn btn-success">Add Services
                            <i class=" icon-plus2"></i></a>
                    </div>
                </div>


                <div class=" form-group">
                    <table class="table table-bordered">
                        <thead>
                            <tr class=" table-header">
                                <th colspan="5">
                                    <h5 class=" text-center text-uppercase"><b>{{ $company ?? '' }} </b> BOOKINGS MADE
                                        ON {{ date('M d, Y') }}</h5>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold">ITEM NAME</th>
                                <th class=" text-bold">Qty</th>
                                <th class=" text-bold">UNIT Price</th>
                                <th class=" text-center text-bold">SUB TOTAL</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- for spaces --}}
                            @php
                                $i = 0;
                                $pavilionTotal = 0;
                            @endphp
                            @foreach ($booked_pavilions as $item)
                                <tr>
                                    <td>{{ $item->pavilionName }} </td>
                                    <td class=" text-center">{{ $item->pavilion_size }}</td>
                                    <td class=" text-right">{{ number_format($item->cost_one_msq, 1) }}</td>
                                    <td class=" text-right">
                                        {{ number_format($subtotal = $item->pavilion_size * $item->cost_one_msq, 1) }}
                                        {{ $item->type_of_currency }}</td>
                                    <td class=" text-center">
                                        <a href="" data-toggle="modal"
                                            data-target="#delete_pavilion{{ $item->bookingID }}"
                                            class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                    </td>
                                    {{-- delete modal --}}
                                    <div id="delete_pavilion{{ $item->bookingID }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to Cancel this
                                                        booking ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('booking.destroy', ['booking' => $item->bookingID]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="space" value="pavilion">
                                                        <input type="hidden" name="pavilionID"
                                                            value="{{ $item->pavilionID }}">
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-danger ml-20">Yes,
                                                                Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- delete modal --}}
                                </tr>
                                @php
                                    $i++;
                                    $pavilionTotal = $pavilionTotal + $subtotal;
                                @endphp
                            @endforeach

                            @php
                                $i = 0;
                                $spaceTotal = 0;
                            @endphp



                            {{--  booked booths fetched here  --}}
                            @foreach ($bookedBooths as $item)
                                <tr>

                                    <td>{{ $item->boothNo }}</td>
                                    <td class=" text-center">{{ $item->boothSize }}</td>
                                    <td class=" text-right">{{ number_format($item->category_cost, 1) }}</td>
                                    {{-- calculate sub total --}}
                                    <td class=" text-right">
                                        @php $subtotal=$item->boothSize*$item->category_cost;@endphp
                                        {{ number_format($subtotal, 1) }} {{ $item->type_of_currency }}

                                    </td>
                                    <td class=" text-center">
                                        <a href=""
                                            onclick=" event.preventDefault();document.getElementById('DeleteBooking').submit();"
                                            class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                        <form action="{{ route('booking.destroy', ['booking' => $item->bookingID]) }}"
                                            method="post" id="DeleteBooking">@csrf
                                            @method('DELETE')
                                        </form>
                                    </td>

                                </tr>
                                {{--  calculate total space cost  --}}
                                @php
                                    $i++;
                                    $spaceTotal = $spaceTotal + $subtotal;
                                @endphp
                            @endforeach

                            @php
                                $a = $i;
                                $service = 0;
                            @endphp
                            {{--  fetching for services being offered  --}}
                            @foreach ($serviceCharges as $serv)
                                <tr>

                                    <td>{{ $serv->serviceName }}</td>
                                    <td class=" text-center">{{ $serv->service_quantity }}</td>
                                    {{-- cost for unit service   --}}
                                    <td class=" text-right">{{ number_format($serv->serviceCost, 1) }} </td>
                                    <td class=" text-right">
                                        @php
                                            //cost for each services
                                            $subt = $serv->serviceCost * $serv->service_quantity;
                                        @endphp
                                        {{ number_format($subt, 1) }} {{ $serv->type_of_currency }}
                                    </td>
                                    <td class=" text-center">
                                        @if (
                                            ($serv->service_priolity == 'Optional' && Auth::user()->account_type == 2) ||
                                                $spaceTotal + $pavilionTotal == 0 ||
                                                Auth::user()->account_type == 1)
                                            <a href=""
                                                onclick=" event.preventDefault();document.getElementById('DeleteService').submit();"
                                                class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                        @else
                                            <label class=" label label-success">Mandatory Service</label>
                                        @endif
                                        <form
                                            action="{{ route('booking.destroy', ['booking' => $serv->service_charge_id]) }}"
                                            method="post" id="DeleteService">@csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                @php
                                    $a++;
                                    $service = $service + $subt;
                                @endphp
                            @endforeach

                            @php
                                $vat = 0;
                                $TotalSubCost = $spaceTotal + $service + $pavilionTotal;
                                $vat = $TotalSubCost * 0.18;

                                $total = $TotalSubCost + $vat;
                            @endphp

                            <form action="{{ route('adviceNotes.store') }}" method="POST">
                                <tr>
                                    <td colspan="2">
                                        <h6 class=" text-right">Value Added Tax (VAT) <i class="text-danger">Note; When
                                                checked VAT is EXCLUSIVE</i></h6>
                                    </td>
                                    @if (Auth::user()->account_type == 2)
                                        <td>
                                            <input type="hidden" name="vat" value="1">
                                            <input type="checkbox" checked class=" form-control" value="1"
                                                title='VAT is mandatory' disabled>
                                        </td>
                                    @elseif (Auth::user()->account_type == 1)
                                        <td>
                                            <input type="checkbox" checked name="vat" class=" form-control"
                                                value="1" title='VAT is mandatory'>
                                        </td>
                                    @endif
                                    <td class=" text-right">

                                        {{ number_format($vat, 2) }} {{ $serv->type_of_currency ?? '' }}
                                    </td>
                                </tr>

                                <tr class=" text-bold">
                                    <td colspan="3" class=" text-right">
                                        <h5>Grand Total Cost</h5>
                                    </td>
                                    <td class=" text-right text-bold">
                                        <h5>{{ number_format($total, 2) }} <b id="vat"></b>
                                            {{ session('currency') }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">

                                        <div class=" col-md-12 row form-group">
                                            <input type="hidden" name="companyId" value="{{ $item->companyID }}">
                                            @if ($item->type_of_currency == 'USD')
                                                <div class="form-group">
                                                    <label class="display-block text-semibold">
                                                        What currency do you expect to use during payment ? <i class=" text-danger">*</i>
                                                        <span class=" text-muted">You might not be allowed to change it depending on position booked.</span> </label>
                                                    <select name="currencyRequested" class=" form-control" required>
                                                        @if (session('originarity') == 'Tanzania')
                                                            <option value="USD">USD</option>
                                                            <option value="TZS">TZS</option>
                                                        @else
                                                            <option value="{{ session('currency') }}">
                                                                {{ session('currency') }}</option>
                                                        @endif
                                                    </select>
                                                    <span class=" text-danger"><b>Note</b> This will not be changed
                                                        after booking confirmation. Please be sure of your
                                                        selection.</span>
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label class="display-block text-semibold">Do you expect to make sales
                                                    during the
                                                    exhibition ?<i class=" text-danger">*</i></label>
                                                <label class="radio-inline radio-right">
                                                    <input type="radio" name="makeSales" value="Yes"
                                                        class="styled" checked>
                                                    YES
                                                </label>
                                                <label class="radio-inline radio-right">
                                                    <input type="radio" name="makeSales" value="No"
                                                        class="styled">
                                                    NO
                                                </label>
                                            </div>
                                            <input type="hidden" name="exhibitionSpace"
                                                value="@foreach ($bookedBooths as $exh) {{ $exh->pavilionName }} ({{ $exh->boothNo }} {{ $exh->boothSize ?? $exh->pavilion_size }} Msq/Unit ), @endforeach">
                                            <div class=" form-group">
                                                <label>What are the products or service you expect to exhibits<i
                                                        class=" text-danger">*</i></label>
                                                <textarea name="exhibitedProducts" class=" form-control" placeholder=" Product or Servces to be exhibited" required>{{ old('exhibitedProducts') }}</textarea>
                                                <span class=" text-muted">
                                                    Create a list of the product categories you will showcase along with
                                                    the list of
                                                    products in each respective category; (for example: 1. Agricultural
                                                    products ….
                                                    Fertilizer, 2. Electrical products…..Electrical equipment,).
                                                </span>
                                            </div>
                                        </div>

                                        <div class=" col-md-6">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="isAcceptedTerms" class="styled"
                                                        required style=" width: 20px;height: 20px;"
                                                        @if (Auth::user()->account_type == 1) checked @endif>
                                                    By checking the check box, you agree to comply with and be bound by
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#termsAdCondition">Rules
                                                        and regulations</a>.

                                                </label>
                                            </div>


                                        </div>



                                        {{-- confirming booking --}}

                                        <input type="hidden" name="company_data" value="company_data">
                                        @csrf
                                        <input type="hidden" name="company_id" value="{{ $company_id ?? '0' }}">
                                        <input type="hidden" name="advice_note" value="advice_note">
                                        <div class=" text-right">
                                            <button type="submit"
                                                class=" btn btn-primary btn-ladda btn-ladda-spinner">Confirm
                                                Booking</button>
                                        </div>
                                        {{-- confirm booking --}}
                            </form>

                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- additional services --}}
    <div id="additional-service" class="modal fade" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">ADD ADDITIONAL SERVICES TO BOOKING</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <div class=" form-group col-md-6">
                            <label>Service offered <i class=" text-danger">*</i></label>
                            <select name="service_name" class="bootstrap-select " data-live-search="true"
                                data-width="100%">
                                <option value="">Select service . . . .</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->serviceID }}">{{ $service->serviceName }} ____
                                        {{ number_format($service->serviceCost, 2) }} {{ $service->type_of_currency }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="company_name" value="{{ $item->companyID ?? '' }}">
                        <input type="hidden" name="exhibition_year" value="{{ $item->exhibition_year ?? '' }}">
                        <input type="hidden" name="additional_service" value="additional_service">
                        <div class=" form-group col-md-6">
                            <label>Quantity of Service <i class=" text-danger">*</i></label>
                            <input type="number" name="quantity" placeholder=" quantity" class=" form-control"
                                value="1">
                        </div>

                        <div class=" text-right">
                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-success ml-20">Add-service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- additional services --}}
</x-app-layout>


<script>
    // Set the target date and time


    const targetDate = new Date("{{ session('bookingLastTime') }}").getTime();

    // Update the countdown every second
    const countdownTimer = setInterval(() => {
        const now = new Date().getTime();
        const timeLeft = targetDate - now;

        if (timeLeft < 0) {
            clearInterval(countdownTimer);
            document.getElementById("countdown").innerText = "00:00:00";
            location.reload();
            return;
        }

        // Calculate time components
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        // Display the countdown
        document.getElementById("countdown").innerText =
            `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
    }, 1000);

    function disableButton() {
        // Disable the button to prevent multiple submissions  
        document.getElementById("submitButton").disabled = true;

        // Optionally, you can change the button text to indicate it's processing  
        document.getElementById("submitButton").innerText = "Submitting...";
    }
</script>
