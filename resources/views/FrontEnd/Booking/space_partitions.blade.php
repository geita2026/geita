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
           <li><a href="#"><i class="icon-stack-check position-left text-uppercase"></i>Customer Space BOOKING</a></li>
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
    <div class="panel-heading">
        <h6 class="panel-title text-semibold">
            @foreach ($pavilions as $item)
            @endforeach
            <a href="{{ route('space-categories.show',['space_category'=>session('event_id')]) }}"
            class=" btn btn-default border-primary text-primary"><i class=" icon-arrow-left12"></i> Back</a>

            </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
    <div class=" panel-body">

            <div class=" form-group">
                 {{--summary top bar  --}}
                 <div id="new_product" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-blue" >
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title text-center text-bold text-bold text-uppercase">HOW TO BOOK SPACE ?</h5>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Space Booking it passess in three important procedures.
                                    <li>Check your prefered spaces for booking</li>
                                    <li>Select service or products to be exhibited</li>
                                </p>
                            </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row">

                <div class="form-group col-md-6 text-right p-15">
                    @if ($number_of_booked_space>0)
                    <a href="{{ route('customer_spaces.edit',['customer_space']) }}" class=" btn btn-warning btn-rounded"><i class=" icon-cart2"></i>{{$number_of_booked_space}}</a>
                    @endif
                </div>
            </div>


            @if($number_of_booths>0)
            <div class=" bg-teal-400 p-10 mb-20 row" >
                @php $a=0; @endphp
                @foreach ($booths as $booth)
                @if ($booth->booking_status=='Canceled')

                @endif

                @if ($booth->bookingStatus!=1)
                @php $a++; @endphp
                @endif
                @endforeach
                <div class=" col-md-12">
                    <h2>Remained Booths:  <b style="font-family:Sans-serif; font-size:120%">{{ $a }} / {{ $number_of_booths }}</b>  <b class=" ml-20 text-uppercase">For <q>{{ $pavilionName }}</q> EXHIBITION SPACE</b> </h2>
                    <div class=" text-right">
                        <a data-toggle="modal" data-target="#new_product" class=" text-white text-bold">How To Book Space ?</a>
                    </div>
                </div>

            </div>
        {{-- //end summary top bar --}}

          {{-- Error or success messages --}}
          @if ($errors->any())
          @foreach ($errors->all() as $error)
          <span class=" text-danger ml-10">{{ $error }}</span>
          @endforeach
          @endif
          {{-- //error messages --}}

            <form action="{{ route('customer_spaces.store') }}" method="POST">
                @csrf

                <div class=" row form-group">
                    <div class=" form-group col-md-10 mb-20">
                        <label> Select Product or service to be exhibited <i class=" text-danger">*</i></label>
                        <div class="multi-select-full">
                        <select name="product[]" class="multiselect-filtering" multiple="multiple" id="product">
                                @foreach ($products as $product)
                                <option value="{{ $product->product_name }}">{{ $product->product_name }}</option>
                                @endforeach
                        </select>
                        </div>
                    </div>
                    <div class=" col-md-2">
                    <input type="hidden" name="booth" value="booth">

                    <div class="text-right">
                        <label>.</label>
                        <button type=" submit" class=" btn btn-primary btn-block btn-ladda btn-ladda-spinner" >Book Selected Booth</button>
                    </div>
                    </div>
                </div>

            {{-- booked booths --}}

            @php
            $i=0;
            @endphp
            @foreach ($booths as $booth)
            {{-- checking if booth is arleady booked change color--}}
            @if ($booth->bookingStatus==1)
            <a>
                <div class="col-xl-1 col-md-2  mt-10 text-center" data-popup="tooltip-custom" title="booked by: {{ $booth->companyName }}." >
                    {{-- <input type="checkbox"> --}}
                    <div class="thumbnail bg-slate-300" >
                        <div class="thumb">
                            <b>{{ $booth->boothNo }}</b><br>
                            {{-- <img src="{{ asset('images/b3.JPG') }}" alt=""> --}}
                            <input type="checkbox" name="booth_name[]" class=" form-control" value="{{ $booth->boothID }}" checked disabled>
                            <b class="  text-center">
                                @if($booth->booth_category_id=='94bff583-954c-4889-b1d7-03706f63808c')
                                CONNER
                                @else
                                NORMAL
                                @endif
                            </b>
                        </div>
                    </div>
                </div>
            </a>
            {{--  end of booked booths  --}}

            {{--  unbooked booths  --}}
            @else
            @php $category=$booth->booth_category_id @endphp
            {{-- foreign conner booths --}}
            @if ($category=='94bff583-954c-4889-b1d7-03706f63808c')
            <a>
                <div class="col-xl-1 col-md-2   mt-10 text-center" data-placement="top" data-popup="popover" title="Booth Details"
                data-trigger="hover" data-content="boothSize: {{  $booth->boothSize }} metre square.  BoothCost: {{ $booth->category_cost }}
                {{ $booth->type_of_currency }} @ 1 metre square.">
                    <div class="thumbnail bg-warning-400" >
                        <div class="thumb">
                            <b>{{ $booth->boothNo }}</b><br>
                            <input type="checkbox" name="booth_name[]" class=" form-control" value="{{ $booth->boothID }}">
                            <b class="  text-center">CONNER</b>
                        </div>
                    </div>
                </div>
            </a>
            {{-- end conner foreign booths booths --}}



            {{--  foreign normal booths  --}}
            @elseif ($category=='5b3ce4ef-4e50-4f65-918f-c2dd839922d2')
            <a>
                <div class="col-xl-1 col-md-2   mt-10 text-center" data-placement="top" data-popup="popover" title="Booth Details" data-trigger="hover" data-content="boothSize: {{  $booth->boothSize }} metre square.  BoothCost: {{ $booth->category_cost }} {{ $booth->type_of_currency }} @ 1 metre square.">
                    {{-- <input type="checkbox"> --}}
                    <div class="thumbnail bg-teal-400" >
                        <div class="thumb">
                            <b>{{ $booth->boothNo }}</b><br>
                            <input type="checkbox" name="booth_name[]" class=" form-control" value="{{ $booth->boothID }}">
                            <b class="  text-center">NORMAL</b>
                        </div>
                    </div>
                </div>
            </a>
            {{--  end foreign normal form  --}}

            {{--  local normal booths  --}}
            @elseif($category=='24d1b21b-7ef4-4ee2-97e3-bb0195b441d9')
            <a >
                <div class="col-xl-1 col-md-2   mt-10 text-center" data-popup="popover" title="Booth Details" data-trigger="hover" data-content="boothSize: {{  $booth->boothSize }} metre square.  BoothCost: {{ number_format($booth->category_cost) }} {{ $booth->type_of_currency }} @ 1 metre square." data-placement="top">
                    <div class="thumbnail bg-teal-400" >
                        <div class="thumb">
                            <b>{{ $booth->boothNo }}</b><br>
                            <input type="checkbox" name="booth_name[]" class=" form-control" value="{{ $booth->boothID }}">
                            <b class="  text-center">NORMAL</b>
                        </div>
                    </div>
                </div>
            </a>
            {{--  local normal booths  --}}

            @else
            <a >
                <div class="col-xl-1 col-md-2   mt-10 text-center" data-popup="popover" title="Booth Details" data-trigger="hover" data-content="boothSize: {{  $booth->boothSize }} metre square.  BoothCost: {{ number_format($booth->category_cost) }} {{ $booth->type_of_currency }} @ 1 metre square." data-placement="top">
                    <div class="thumbnail bg-teal-400" >
                        <div class="thumb">
                            <b>{{ $booth->boothNo }}</b><br>
                            <input type="checkbox" name="booth_name[]" class=" form-control" value="{{ $booth->boothID }}">
                            <b class="  text-center">NORMAL</b>
                        </div>
                    </div>
                </div>
            </a>
            {{--  local normal booths  --}}

            @endif
            @endif
            @php $i++; @endphp
            @endforeach
        </form>

        @else

        <div class=" row p-15" style=" border: 1px solid rgb(106, 190, 113);">

            <div class=" col-md-12 bg-teal-400 form-group">
               <div class=" col-md-8">
                <h2><b class=" text-uppercase">{{ $pavilionName }} EXHIBITION SPACE</b> </h2>
               </div>
                {{-- show booking details for booked spaces --}}
               <div class=" col-md-4">
               @foreach ($pavilions as $pavilion)
               @if ($pavilion->bookingStatus==1)
               <h4>Booked By: <b>{{ $pavilion->companyName }}</b></h4>
               <p>ADVICE NOTE ID: <b>{{ $pavilion->advice_note_id }}</b></p>
               <p>BOOKING DATE: {{ date('d-m-Y',strtotime($pavilion->created_at)) }} </p>
               <p>Booking Status: <b class=" text-uppercase">
                @if($pavilion->booking_status==null)
                BOOKED
                @else
                {{ $pavilion->booking_status }}
                </b>
                @endif
               </p>
               <hr>
               @endif
               @endforeach
               </div>
               {{-- end of showing booking details --}}

            </div>

            {{-- if space not booked allow user to book it  --}}
            @if ($item->bookingStatus==0)
            <div class=" form-group col-12">
                <form action="{{ route('customer_spaces.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pavilion_name" value="pavilion_name">
                    <input type="hidden" name="booked_space" value="{{ $pavilion->pavilionID }}">
                    <div class=" form-group col-md-4 mb-20">
                        <label>Product / service exhibited <i class=" text-danger">*</i></label>
                        <div class="multi-select-full">
                        <select name="product[]" class="multiselect-filtering" multiple="multiple" id="product" required>
                                @foreach ($products as $product)
                                <option value="{{ $product->product_name }}">{{ $product->product_name }}</option>
                                @endforeach
                        </select>
                        </div>
                    </div>

                    <input type="hidden" name="space" value="pavilion">
                    <div class=" col-xs-12 mt-20 text-right">
                        <button type=" submit" class=" btn btn-primary btn-ladda btn-ladda-spinner" >Book Space</button>
                    </div>
                </form>
            </div>
            @endif
            {{-- end of unbooked space --}}

        </div>

        @endif

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


