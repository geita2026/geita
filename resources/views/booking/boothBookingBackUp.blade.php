<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
    {{-- contents on dashboards --}}
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-home position-left"></i>Home</a></li>
                <li class="active">Make booking</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <!-- Media library -->
        <div class="panel panel-white">
            <div class=" panel-body">

                @foreach ($pavilions as $item)
                @endforeach
                {{-- Error or success messages --}}
                <div class=" form-group">
                    {{-- summary top bar  --}}
                    <div id="FlowPlan" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-full">
                            <div class="modal-content">
                                <div class="modal-header bg-blue">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title text-center text-bold">{{ $item->pavilionName }} FLOOR PLAN
                                        AS
                                        PER 48<sup>th</sup> DITF - 2024</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="row">

                                        @if ($item->floor_plan != null)
                                            <img src="{{ asset('floorPlanFiles/' . $item->floor_plan) }}"
                                                style="width: 100%;" height="800px">
                                        @else
                                            <h1 class="text-danger">No floor Plan Uploaded!</h1>
                                        @endif
                                    </div>
                                </div>

                                <div class=" modal-footer" style="text-align: left;font-size:2vmin">
                                    <p>
                                        <b class=" text-warning">NOTE!</b> The booth Number from the floor plan,
                                        signifies the Number after three Alphabert in the system booths.
                                        example in Karume Pavilion floor plan,<br>
                                        Booth No 1 = KRM-001.<br>
                                        Booth No 10 = KRM-010 e.t.c
                                    </p>
                                </div>



                            </div>
                        </div>
                    </div>
                    {{-- END FLOW PLAN --}}

                    <div class="row">
                        <div class="form-group col-md-6 text-right p-20">
                            @if ($number_of_booked_space ?? 0 > 0)
                                <a href="{{ route('booking.edit', ['booking']) }}"
                                    class=" btn btn-warning btn-rounded"><i
                                        class=" icon-cart2"></i>{{ $number_of_booked_space ?? 0 }}</a>
                            @endif
                        </div>
                    </div>


                    @if ($number_of_booths > 0)
                        <div class=" bg-teal-400 p-10 mb-20 row">
                            @php $a=0; @endphp
                            @foreach ($booths as $booth)
                                @if ($booth->bookingStatus != 1)
                                    @php $a++; @endphp
                                @endif
                            @endforeach
                            <div class=" col-md-12">
                                <h2>Remained Booths: <b
                                        style="font-family:Sans-serif; font-size:120%">{{ $a }}/{{ $number_of_booths }}</b>
                                    <b class=" ml-20 text-uppercase">{{ $item->pavilionName }} EXHIBITION SPACE
                                        BOOKING</b>
                                </h2>
                                <div class=" text-right">
                                    <a data-toggle="modal" data-target="#FlowPlan"
                                        class="text-bold btn btn-danger">Pavilion Floor Plan <i
                                            class=" icon-file-eye"></i> </a>
                                </div>
                            </div>

                        </div>
                        {{-- //end summary top bar --}}

                        <form action="{{ route('booking.store') }}" method="POST">
                            @csrf
                            <div class=" row">

                                <div class=" col-md-6 mb-20">
                                    <label>Company Name <i class=" text-danger">*</i></label>
                                    <select name="company_name" class="bootstrap-select " data-live-search="true"
                                        data-width="100%">
                                        <option value="">- - - - -Select company - - - - </option>
                                        <optgroup>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->companyID }}">
                                                    {{ $company->companyName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

                                <div class=" form-group col-md-4 mb-20">
                                    <label>Product / service exhibited <i class=" text-danger">*</i></label>
                                    <div class="multi-select-full">
                                        <select name="product[]" class="multiselect-filtering" multiple="multiple"
                                            required>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->product_name }}">
                                                    {{ $product->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="space" value="booth">
                                <div class=" col-md-2 mt-20 text-right">
                                    <button type=" submit" class=" btn btn-primary btn-ladda btn-ladda-spinner">Book
                                        Selected Booths</button>
                                </div>
                            </div>


                            {{--  BOOTHS --}}
                            <div class=" form-group">
                                @foreach ($pavilions as $key => $item)
                                    @if (empty($item->boothID))
                                        {{ 'Not Booths' }}
                                    @else
                                        @if (!@empty($item->pavilionID) && $item->exhibition_year == session('liveEvent'))
                                            @if (Auth::user()->account_type == 1)
                                                <a
                                                    href="@if ($item->advice_note_id != null) {{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }} @endif">
                                                    <div class="col-xl-1 col-md-2   mt-10 text-center"
                                                        data-placement="top" data-popup="popover"
                                                        title="SPACE BOOKED BY:" data-trigger="hover"
                                                        data-content="{{ $item->companyName }}">
                                                        <div class="thumbnail bg-grey-300">
                                                            <div class="thumb">

                                                                @if ($item->balance <= 0)
                                                                    <b class=" form-control"
                                                                        style="border-bottom: 10px solid green;">
                                                                        {{ $item->boothNo }}

                                                                    </b>
                                                                @elseif ($item->balance >= 0)
                                                                    <b class=" form-control"
                                                                        style="border-bottom: 10px solid red;">
                                                                        {{ $item->boothNo }}
                                                                        ({{ number_format($item->balance, 2) }}
                                                                        {{ $item->currency }})

                                                                    </b>
                                                                @endif
                                                                <br>

                                                                <b
                                                                    class="  text-center">{{ Illuminate\Support\Str::limit($item->companyName, 10) }}</b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @elseif (Auth::user()->account_type == 2)
                                                <a
                                                    href="@if ($item->advice_note_id != null && Auth::user()->user_uuid == $item->companyID) {{ route('adviceNotes.show', ['adviceNote' => $item->advice_note_id]) }} @else # @endif">
                                                    <div class="col-xl-1 col-md-2   mt-10 text-center"
                                                        data-placement="top" data-popup="popover"
                                                        title="SPACE BOOKED BY:" data-trigger="hover"
                                                        data-content="{{ $item->companyName }}">
                                                        <div class="thumbnail bg-grey-300">
                                                            <div class="thumb">

                                                                @if ($item->balance <= 0)
                                                                    <b class=" form-control text-success">
                                                                        {{ $item->boothNo }} <label
                                                                            class=" label label-success"></label>
                                                                    </b>
                                                                @elseif ($item->balance > 0)
                                                                    <b class=" form-control text-warning"
                                                                        style="border-bottom: 10px solid red;">
                                                                        {{ $item->boothNo }}
                                                                        @if (Auth::user()->user_uuid == $item->companyID)
                                                                            ({{ number_format($item->balance, 2) }})
                                                                        @else
                                                                            xxxxx
                                                                        @endif
                                                                        {{ $item->currency }}

                                                                    </b>
                                                                @endif
                                                                <br>

                                                                <b
                                                                    class="  text-center">{{ Illuminate\Support\Str::limit($item->companyName, 10) }}</b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif
                                        @else
                                            <a>
                                                <div class="col-xl-1 col-md-2   mt-10 text-center " data-placement="top"
                                                    data-popup="popover" title="Booth Details" data-trigger="hover"
                                                    data-content="Size: {{ number_format($item->boothSize, 1) }} msq.  Cost: {{ number_format($item->category_cost, 1) }}{{ $item->type_of_currency }} @ 1 msq.">
                                                    <div class="thumbnail {{ $item->categoryCode }}">
                                                        <div class="thumb">
                                                            <b>{{ $item->boothNo }}</b><br>
                                                            <input type="checkbox" name="spaceName[]"
                                                                class=" form-control" value="{{ $item->boothID }}">
                                                            <b class="  text-center">
                                                                @if ($item->booth_category_id == '94bff583-954c-4889-b1d7-03706f63808c')
                                                                    CONNER
                                                                @else
                                                                    NORMAL
                                                                @endif
                                                            </b>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endif
                                @endforeach

                            </div>
                            {{-- END BOOTHS --}}
                        </form>
                    @else
                        {{-- ########### WHOLE PAVILION BOOKING ############ --}}
                        <div class=" col-md-12">
                            <form action="{{ route('booking.store') }}" method="POST">
                                @csrf
                                @foreach ($pavilions as $space)
                                    <div class=" col-md-12">
                                        <h1 style="font-size: 4vmin">{{ $space->pavilionName }}</h1>
                                        <input type="hidden" name="spaceName[]" value="{{ $id }}"
                                            class=" form-check">
                                    </div>
                                @endforeach



                                @php $i=0; @endphp
                                @foreach ($bookingStatus as $value)
                                    @php $i++; @endphp
                                @endforeach

                                @if ($i == 0)
                                    <div class=" row">

                                        <div class=" col-md-6 mb-20">
                                            <label>Company Name <i class=" text-danger">*</i></label>
                                            <select name="company_name" class="bootstrap-select "
                                                data-live-search="true" data-width="100%">
                                                <option value="">Select company . . . . . . .</option>
                                                <optgroup>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company->companyID }}">
                                                            {{ $company->companyName }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>

                                        <div class=" form-group col-md-4 mb-20">
                                            <label>Product / service exhibited <i class=" text-danger">*</i></label>
                                            <div class="multi-select-full">
                                                <select name="product[]" class="multiselect-filtering"
                                                    multiple="multiple" required>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->product_name }}">
                                                            {{ $product->product_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="space" value="booth">
                                        <div class=" col-md-12 mt-20 text-right">
                                            <button type=" submit"
                                                class=" btn btn-primary btn-ladda btn-ladda-spinner">Book
                                                Space</button>
                                        </div>
                                    </div>
                                @else
                                    @if (Auth::user()->account_type == 1)
                                        <h2>BOOKED By: {{ $value->companyName }}</h2>
                                        <h2>Invoice No: <a
                                                href="{{ route('adviceNotes.show', ['adviceNote' => $value->advice_note_id]) }}">{{ $value->advice_note_id }}</a>
                                        </h2>
                                        <h2>Invoice Amount: {{ number_format($value->total_amount, 2) }}
                                            {{ $value->currency }}</h2>
                                        <h2>Balance Amount: <b
                                                class=" text-warning">{{ number_format($value->balance, 2) }}
                                                {{ $value->currency }}</b></h2>
                                    @elseif (Auth::user()->account_type == 2)
                                        {{-- IF IS THE ONE BOOKED SHOW DETAILS --}}

                                        @if (Auth::user()->user_uuid == $value->companyID)
                                            <h2>BOOKED By: {{ $value->companyName }}</h2>
                                            <h2>Invoice No: <a
                                                    href="{{ route('adviceNotes.show', ['adviceNote' => $value->advice_note_id]) }}">{{ $value->advice_note_id }}</a>
                                            </h2>
                                            <h2>Invoice Amount: {{ number_format($value->total_amount, 2) }}
                                                {{ $value->currency }}</h2>
                                            <h2>Balance Amount: <b
                                                    class=" text-warning">{{ number_format($value->balance, 2) }}
                                                    {{ $value->currency }}</b></h2>
                                            @if ($value->balance > 0)
                                                <div
                                                    style="border:10px solid rgb(255, 0, 0);text-align:center;font-size:3vmin;font-family:monospace">
                                                    <b>UNVERIFIED EXHIBITOR</b>
                                                </div>
                                            @elseif ($value->balance <= 0)
                                                <div
                                                    style="border:10px solid green;text-align:center;font-size:3vmin;font-family:monospace">
                                                    <b>VERIFIED EXHIBITOR</b>
                                                </div>
                                            @endif
                                        @else
                                            <h1 style="font-size: 3vmin; color:red">
                                                Space is already Booked Please select other Space.
                                            </h1>
                                        @endif

                                    @endif


                                @endif

                            </form>
                        </div>
                    @endif

                </div>
            </div>



        </div>

    </div>

    <!-- /media library -->
    </div>
    </div>

    </div>

    <!-- /content area -->

    </div>
    <!-- /main content -->
</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>


data-placement="top" data-popup="popover"
title="SPACE BOOKED BY:" data-trigger="hover"
data-content="{{ $item->companyName }}"



<div class=" col-md-10 mb-20">
    <label>Company Name <i class=" text-danger">*</i></label>
    <select id="search-select" name="company_name" required>
    </select>
</div>
<input type="hidden" name="space" value="booth">
<div class=" col-md-2 mt-20 text-right">
    <button type=" submit" class=" btn btn-primary btn-ladda btn-ladda-spinner">Book
        Selected Booths</button>
</div>