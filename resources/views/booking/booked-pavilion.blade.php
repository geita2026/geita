<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
          @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a><i class="icon-home2 position-left"></i> BOOKED SPACES</a></li>
           <li class="active"> Booked spaces</li>
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
        <h6 class="panel-title text-bold">BOOKED SPACES</h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
<div class=" panel-body table-responsive">
    <div class=" row">
        <div class=" form-group col-md-6">
            @foreach ($booked_pavilions as $value)
            @endforeach
            <a href="{{ route('event-booking-spaces.show',['event_booking_space'=>$value->event ?? 1]) }}" class=" btn btn-primary"><i class=" icon-arrow-left12"></i> Book-Again</a>
        </div>
        <div class=" form-group text-right col-md-6">
            <a data-toggle="modal" data-target="#additional-service" class=" btn btn-success">Add Services <i class=" icon-add"></i></a>
        </div>
    </div>

   {{--  error messages  --}}
   @if ($errors->any())
   @foreach ($errors->all() as $error)
   <span class=" text-danger">{{ $error }} ,</span>
   @endforeach
   @endif
   {{--  //end  --}}


    <div class=" form-group">
         {{--  booked pavilions   table--}}
     <table class="table table-bordered table-striped">
        <thead >

            <tr>
                <th colspan="5">
                    @foreach ($booked_pavilions as $data)
                    @endforeach
                    <h4 class=" text-bold">
                        <b class=" text-primary">{{ $data->companyName ?? '.......' }}</b> BOOKINGS
                    </h4>
                </th>
            </tr>
            <tr >
                {{-- <th class=" text-bold text-center">S/N</th> --}}
                <th class=" text-bold text-center">ITEM NAME</th>
                <th class=" text-bold text-center">ITEM QUANTITY (MSQ / No.)</th>
                <th class=" text-bold text-center">@ UNIT COST</th>
                <th class=" text-bold text-center">SUB TOTAL COST</th>
                <th class=" text-bold text-center">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            {{--  display booked pavilions  --}}
            @php
                $i=1;
                $subtotal=0;
            @endphp
            @foreach ($booked_pavilions as $item)

            <tr>

                <td>{{ $item->pavilionName }} <i class=" text-warning">(Space name)</i></td>
                <td class=" text-right">{{ $item->pavilion_size }}</td>
                <td class=" text-right">{{ number_format($item->cost_one_msq,2 )}}</td>
                <td class=" text-right">{{ number_format($item->cost_one_msq*$item->pavilion_size) ,2}}</td>
                <td class=" text-center">
                    <a data-toggle="modal" data-target="#cancel{{ $item->bookingID }}"  class=" text-danger text-bold" > remove <i class="  icon-diff-removed"></i></a>
                </td>
                {{--  modal to cancel bookin  --}}
                <div id="cancel{{ $item->bookingID }}" class="modal fade" aria-modal="true">
                   <div class="modal-dialog modal-md">
                       <div class="modal-content">
                           <div class="modal-header bg-danger">
                               <button type="button" class="close" data-dismiss="modal">&times;</button>
                               <h5 class="modal-title">Are you sure you whant to Cancel this Booking ?</h5>
                           </div>
                           <div class="modal-body">
                               <form action="{{ route('bookedPavilions.destroy',['bookedPavilion'=>$item->bookingID]) }}" method="POST">
                                   @csrf
                                   @method('DELETE')
                                   <input type="hidden" name="pavilion" value="pavilion">
                                  <div class=" text-right">
                                   <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                   <button type="submit" class="btn btn-danger ml-20" >Yes, Cancel</button>
                                  </div>
                               </form>
                           </div>
                       </div>
                   </div>
               </div>
               {{--  end of modal  --}}
            </tr>
            @php
            $i++;
            $subtotal=$subtotal+($item->cost_one_msq*$item->pavilion_size);
        @endphp
        @endforeach

        @php $x=2;
        $service_subtotal=0;
        @endphp
        @foreach ($service_charges as $service)
        <tr>
            {{-- <td>{{ $x }}</td> --}}
            <td>{{ $service->serviceName }}</td>
            <td class=" text-right">{{ $service->service_quantity }}</td>
            <td class=" text-right">{{ number_format($service->serviceCost )}}</td>
            @php
            $service_unique=($service->serviceCost*$service->service_quantity);
            @endphp
            <td class=" text-right"> {{ number_format($service_unique) }}</td>
            <td class=" text-center">
               <a data-toggle="modal" data-target="#cancel{{ $service->service_charge_id }}"  class=" text-danger text-bold" >remove <i class="icon-diff-removed"></i></a>
           </td>

           <div id="cancel{{ $service->service_charge_id }}" class="modal fade" aria-modal="true">
               <div class="modal-dialog modal-md">
                   <div class="modal-content">
                       <div class="modal-header bg-danger">
                           <button type="button" class="close" data-dismiss="modal">&times;</button>
                           <h5 class="modal-title">Are you sure you whant to Cancel this service ?</h5>
                       </div>
                       <div class="modal-body">
                           <form action="{{ route('bookedPavilions.destroy',['bookedPavilion'=>$service->service_charge_id]) }}" method="POST">
                               @csrf
                               @method('DELETE')
                               <input type="hidden" name="service" value="service">
                              <div class=" text-right">
                               <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                               <button type="submit" class="btn btn-danger ml-20" >Yes, Cancel</button>
                              </div>
                           </form>
                       </div>
                   </div>
               </div>
           </div>

        </tr>

        @php $x++;
        $service_subtotal=$service_subtotal+$service_unique;
        @endphp
        @endforeach
           @if($i!=1)
           <tr>
               <td colspan="3" class=" text-right"><h4>Total Cost (VAT Exclusive)</h4></td>
                <td class=" text-right"> <h4>{{ number_format( $subtotal+$service_subtotal) }} {{ $item->type_of_currency }}</h4></td>
                <td>
                   <form action="{{ route('adviceNotes.store') }}" method="POST">
                       @csrf
                    @foreach ($booked_pavilions as $item)
                    <input type="hidden" name="event" value="{{ $item->event }}">
                    <input type="hidden" name="exhibition_year" value="{{ $item->exhibition_year }}">
                    <input type="hidden" name="company_id" value="{{ $item->companyID }}">
                    <input type="hidden" name="advice_note" value="create_advice_note">
                    @endforeach
                    <div class=" text-right">
                        <button type=" submit" class=" btn btn-primary">Confirm Booking</button>
                    </div>
                   </form>
                 </form>
                </td>
            </tr>
            @else
           @endif
        </tbody>
    </table>
    {{--  end booked pavilion Table  --}}

    </div>

    </div>
    <!-- /content area -->
</div>

</div>

<!-- /main content -->

{{-- additional services --}}
<div id="additional-service" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">ADD ADDITIONAL SERVICES TO YOUR BOOKING</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('bookedPavilions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="optional_service" value="optional_service">
                    @foreach ($booked_pavilions as $item)
                    <input type="hidden" name="exhibition_year" value="{{ $item->exhibition_year }}">
                    <input type="hidden" name="company_name" value="{{ $item->companyID }}" >
                    @endforeach
                    <div class=" form-group col-md-6">
                        <label>Service offered <i class=" text-danger">*</i></label>
                        <select name="service_name" class="bootstrap-select" data-live-search="true" data-width="100%">
                            <option value="">Select service . . . .</option>
                            @foreach ($services as $service)
                            <option value="{{ $service->serviceID }}">{{ $service->serviceName }} ____ {{ number_format($service->serviceCost,2) }} {{ $service->type_of_currency }}</option>
                            @endforeach
                        </select>
                    </div>

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
