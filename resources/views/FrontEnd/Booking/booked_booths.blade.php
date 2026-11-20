<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('booking.edit','booking') }}"><i class=" icon-users2 position-left"></i>Booked-Spaces</a></li>
            <li class="active">View-All Booked space</li>
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

    <div class="panel-heading">
        <h6 class="panel-title text-semibold">
            BOOKED BOOTHS
            </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>


<div class=" panel-body table-responsive">
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <span class="text-danger"><li class=" alert">{{ $error }}</li></span>
    @endforeach
    @endif


     {{-- </div> --}}
                <div class=" row">
                    <div class=" form-group col-md-6">
                        <a href="{{ route('space-categories.show',['space_category'=>session('event_id')]) }}" class=" btn btn-default"><i class=" icon-arrow-left12"></i> Back</a>
                    </div>

                    <div class=" form-group text-right col-md-6">
                        <a data-toggle="modal" data-target="#additional-service" class=" btn btn-success">Add Service <i class=" icon-add"></i></a>
                    </div>

                </div>

                <div class=" form-group">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="5"><h5 class=" text-center"><b>{{ $company ?? '' }}, </b> BOOKING</h5></th>
                            </tr>
                            <tr>
                                <th class=" text-bold">ITEM NAME</th>
                                <th class=" text-bold">QUANTITY or SIZE</th>
                                <th class=" text-bold">COST FOR ONE UNIT</th>
                                <th class=" text-center text-bold">SUB TOTAL</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>


                             {{-- for spaces --}}
                             @php $i=0;$total=0;@endphp
                             @foreach ($booked_pavilions as $item)
                             <tr>
                                 <td>{{ $item->pavilionName }}</td>
                                 <td class=" text-center">{{ $item->pavilion_size }}</td>
                                 <td class=" text-right">{{ number_format($item->cost_one_msq,1) }}</td>
                                 <td class=" text-right">{{ number_format($subtotal=$item->pavilion_size*$item->cost_one_msq,1) }} {{ $item->type_of_currency }}</td>
                                 <td class=" text-center">
                                     <a href="" data-toggle="modal" data-target="#delete_pavilion{{ $item->bookingID }}"  class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                 </td>
                                     {{-- delete modal --}}
                                     <div id="delete_pavilion{{ $item->bookingID }}" class="modal fade" aria-modal="true">
                                         <div class="modal-dialog modal-md">
                                             <div class="modal-content">
                                                 <div class="modal-header bg-danger">
                                                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                     <h5 class="modal-title">Are you sure you whant to Cancel this booking ?</h5>
                                                 </div>
                                                 <div class="modal-body">
                                                     <form action="{{ route('booking.destroy', ['booking' => $item->bookingID]) }}" method="POST">
                                                         @csrf
                                                         @method('DELETE')
                                                         <input type="hidden" name="space" value="pavilion">
                                                         <input type="hidden" name="pavilionID" value="{{ $item->pavilionID }}">
                                                        <div class=" text-right">
                                                         <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                         <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                                        </div>
                                                     </form>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                     {{-- delete modal --}}
                             </tr>
                             @php $i++; $total=$total+$subtotal; @endphp
                             @endforeach


                            @php$i=1; $total=0; @endphp
                            {{--  booked booths fetched here  --}}
                            @foreach ($bookedBooths as  $item)
                           <tr>
                            <td>{{ $item->boothNo }}</td>
                            <td class=" text-center">{{$item->boothSize}}</td>
                            <td class=" text-right">{{ number_format($item->category_cost) }}</td>
                            {{-- calculate sub total --}}
                            <td class=" text-right">
                               @php
                               $subtotal=$item->boothSize*$item->category_cost;
                               @endphp
                               {{number_format($subtotal)}} {{ $item->type_of_currency }}

                            </td>
                            <td class=" text-center">
                                <a href="" data-toggle="modal" data-target="#delete_booth{{ $item->bookingID }}"  class="text-danger">Remove <i class=" icon-cross2"></i></a>
                            </td>
                                {{-- delete modal --}}
                                <div id="delete_booth{{ $item->bookingID }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to Cancel this booking ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('customer_spaces.destroy', ['customer_space' => $item->bookingID]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="booth" value="booth">
                                                    <input type="hidden" name="boothID" value="{{ $item->boothID }}">
                                                   <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                                   </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- delete modal --}}
                           </tr>
                           {{--  calculate total space cost  --}}
                           @php

                               $i++;
                               $total=$total+$subtotal;
                           @endphp
                            @endforeach

                            @php
                                $a=$i;
                                $service=0;
                            @endphp
                            {{--  fetching for services being offered  --}}
                            @foreach ($serviceCharges as $serv)
                            <tr>

                                <td>{{ $serv->serviceName }}</td>
                                <td class=" text-center">{{ $serv->service_quantity }}

                                </td>
                                {{-- cost for unit service   --}}
                                <td class=" text-right" >{{number_format($serv->serviceCost,2)}} </td>
                                <td class=" text-right">
                                    @php
                                    //cost for each services
                                        $subt=$serv->serviceCost*$serv->service_quantity;
                                    @endphp
                                    {{ number_format($subt,2 )}} {{ $serv->type_of_currency }}
                                </td>
                                <td class=" text-center">
                                    <a href="" data-toggle="modal" data-target="#delete_booth{{ $serv->service_charge_id }}"  class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                </td>
                            </tr>

                            @php
                                $a++;
                                $service=$service+$subt;
                            @endphp


                      {{--service modal--}}

                      <div id="delete_booth{{ $serv->service_charge_id }}" class="modal fade" aria-modal="true">

                        <div class="modal-dialog modal-md">

                            <div class="modal-content">

                                <div class="modal-header bg-danger">

                                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                                    <h5 class="modal-title">Are you sure you whant to Cancel this service ?</h5>

                                </div>

                                <div class="modal-body">

                                    <form action="{{ route('customer_spaces.destroy', ['customer_space' => $serv->service_charge_id]) }}" method="POST">

                                        @csrf

                                        @method('DELETE')

                                        {{-- <input type="hidden" name="service_charge_id" value="$serv->service_charge_id"> --}}

                                        <input type="hidden" name="service_charge_id" value="{{$serv->service_charge_id }}">

                                        <div class=" text-right">

                                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>

                                            <button type="submit" class="btn btn-danger ml-20" >Yes, Cancel</button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- service modal --}}

                           @endforeach
                            <tr >


                                <td colspan="5">
                                {{-- confirming booking --}}
                                <form action="{{ route('my_invoices.store') }}" method="POST">
                                    <input type="hidden" name="company_data" value="company_data">
                                    @csrf
                                    @foreach ($booked_pavilions as  $space)  @endforeach
                                    <input type="hidden" name="company_id" value="{{ $item->companyID ?? $space->companyID }}">
                                    @foreach ($serviceCharges as $sv)
                                    @endforeach
                                    <input type="hidden" name="advice_note" value="advice_note">
                                    <input type="hidden" name="item[]" value="Participation fee">
                                    <input type="hidden" name="amount[]" value="{{number_format($total,2)}}">
                                    <input type="hidden" name="item[]" value="additional service">
                                    <input type="hidden" name="amount[]" value="{{ $service }}">
                                    <input type="hidden" name="event" value="{{ $item->event  ?? '' }}">
                                    @if($total!=0)
                                    <div class=" text-right">
                                        <button type="submit" class=" btn btn-primary">Confirm Booking</button>
                                    </div>
                                    @endif

                                {{-- confirm booking --}}

                            </td>
                            </tr>
                        </form>
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
                        <form action="{{ route('customer_spaces.store') }}" method="POST">
                            @csrf
                            <div class=" form-group col-md-6">
                                <label>Service offered <i class=" text-danger">*</i></label>
                                <select name="service_name" class="bootstrap-select " data-live-search="true" data-width="100%">
                                    <option value="">Select service . . . .</option>
                                    @foreach ($services as $service)
                                    <option value="{{ $service->serviceID }}">{{ $service->serviceName }} ____ {{ number_format($service->serviceCost,2) }} {{ $service->type_of_currency }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="company_id" value="@if($total!=0){{ $item->companyID }}@endif ">
                            <input type="hidden" name="exhibition_year" value="{{ $item->exhibition_year ?? '' }}">
                            <input type="hidden" name="additional_service" value="additional_service">
                            <div class=" form-group col-md-6">
                                <label>Quantity of Service <i class=" text-danger">*</i></label>
                                <input type="number" name="quantity" placeholder=" quantity" class=" form-control" value="1">
                            </div>

                           <div class=" text-right">
                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-success ml-20" >Add-service</button>
                           </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- additional services --}}
</x-app-layout>

