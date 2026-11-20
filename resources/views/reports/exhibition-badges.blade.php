

<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <div class="page-header">
      <div class="page-header-content">
       @include('layouts.heading-elements')
      </div>
      <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>EXHIBITOR BADGES REPORTS</a></li>
           <li class="active">View Exhibitor Badges Reports</li>
       </ul>
       <ul class="breadcrumb-elements">
           @include('layouts.help')
       </ul>
   </div>
</div>

<div class=" content">
   <div class="panel panel-white">
       <div class="panel-heading">
           <h4 class="panel-title text-bold text-uppercase text-center">EXHIBITOR Badges REPORTS FOR THE <b class=" text-warning"><q>{{ $event_name }}</q></b></h6>
           <div class="heading-elements">
               <ul class="icons-list">
                   <li><a data-action="collapse"></a></li>
                   <li><a data-action="reload"></a></li>
                   <li><a data-action="close"></a></li>
               </ul>
           </div>
       </div>
       <div class=" panel-body">


        <div class=" row col-md-12">

            <form action="{{ route('exhibition_badges.index') }}" method="POST">
                @csrf
                @method('GET')
                <div class=" row">
                <div class=" form-group col-md-5">
                    <label>Select Event<i class=" text-danger">*</i></label>
                 <select name="event_name" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                    @foreach ($events as $item)
                        <option value="{{ $item->event_id }}">{{ $item->event_name }}</option>
                        @endforeach
                 </select>
                 <span class="text-danger"> @error('event_name'){{ $message }}@enderror</span>
                </div>


                 <div class=" form-group col-md-5">
                    <label>Select service<i class=" text-danger">*</i></label>
                    <select name="service_name" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                       @foreach ($services as $serv)
                           <option value="{{ $serv->serviceID }}">{{ $serv->serviceName }}</option>
                           @endforeach
                    </select>
                    <span class="text-danger"> @error('service_name'){{ $message }}@enderror</span>
                   </div>



                 <div class=" form-group col-md-2 text-right">
                    <button type="submit" class=" btn btn-primary border-blue btn-ladda btn-ladda-spinner">Load New Data <i class=" icon-database-refresh"></i></button>
                </div>
                </div>
            </form>

        </div>


<!-- Main charts -->
<div class="row">

    @php $j=0; @endphp
    @foreach($exhibitors as $value)
        @php $quantity=0; @endphp
        @foreach ($booked_services as $data)
        @php $quantity=$quantity+$data->service_quantity; @endphp
        @endforeach
    @php  $j++; @endphp
    @endforeach

    <div class=" form-group col-md-6">
        <table class=" table table-bordered bg-teal-400">
            <thead>
                <tr>
                    <th colspan="3" class=" text-center text-bold text-uppercase">SERVICE OCCUPATION SUMMARY</th>
                </tr>
                <tr>
                    <th class=" text-center text-bold text-uppercase">Service Name</th>
                    <th class=" text-center text-bold text-uppercase">Service Quantity</th>
                    <th class=" text-center text-bold text-uppercase">Total Exhibitors</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data->serviceName ?? '' }}</td>
                    <td class=" text-center">{{ number_format($quantity) }}</td>
                    <td class=" text-center">{{ number_format($j) }}</td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="col-md-12">
        <!-- Traffic sources -->
            <div class="panel panel-flat">
                <div class="panel-body">
                   <table class=" datatable-button-html5-columns table table-bordered table-striped">
                    <thead class="">

                        <tr>
                            <th colspan="3" class=" text-bold text-center text-uppercase">LIST OF <q class=" text-primary"> @foreach ($booked_services as $data) {{ $item->serviceName }} @endforeach</q> FOR THE <q class="text-primary">{{ $event_name }}</q> AS PER {{ date('M d, Y') }}.</th>
                        </tr>
                        <tr>
                            <th class=" text-bold text-center">COMPANY NAME</th>
                            <th class=" text-bold text-center">SERVICE QUANTITY</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php $j=1; @endphp
                    @foreach($exhibitors as $value)
                    <tr>
                        <td>{{ $value->companyName }}</td>

                        @php $quantity=0; @endphp
                        @foreach ($booked_services as $data)
                        @if ($value->companyID==$data->company_id)
                        @php $quantity=$quantity+$data->service_quantity; @endphp
                        @endif
                        @endforeach

                        <td class=" text-center">{{ $quantity }}</td>
                    </tr>
                    @php  $j++; @endphp

                  @endforeach
                    </tbody>
                   </table>
                </div>
            </div>



        {{-- another 7-col-md here --}}

    </div>



</div>
<!-- /main charts -->





       </div>
    </div>
</div>
</x-app-layout>

