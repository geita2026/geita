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
                
                <div class=" row">
                    <div class=" form-group col-md-6">
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
                                    <h5 class=" text-center text-uppercase">LIST OF PENDING BOOKING</h5>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold">ITEM NAME</th>
                                <th class=" text-bold">Company Name</th>
                                <th class=" text-bold">Booking Date</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{--  booked booths fetched here  --}}
                            @foreach ($bookedBooths as $item)
                                <tr>
                                    <td>{{ $item->boothNo ?? $item->pavilionName }}</td>
                                    <td class=" text-center">{{ $item->companyName  }}</td>
                                    <td class=" text-right">{{ date('M d, Y',strtotime($item->created_at)) }}</td>
                                    {{-- calculate sub total --}}
                                    
                                    <td class=" text-center">
                                        <a href=""
                                            onclick=" event.preventDefault();document.getElementById('DeleteBooking{{ $item->bookingID }}').submit();"
                                            class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                        <form action="{{ route('booking.destroy', ['booking' => $item->bookingID]) }}"
                                            method="post" id="DeleteBooking{{ $item->bookingID }}">@csrf
                                            @method('DELETE')
                                        </form>
                                    </td>

                                </tr>
                                {{--  calculate total space cost  --}}
                            @endforeach

                            
                            {{--  fetching for services being offered  --}}
                            @foreach ($serviceCharges as $serv)
                                <tr>
                                    <td>{{ $serv->serviceName }}</td>
                                    <td class=" text-center">{{ $serv->companyName }}</td>
                                    {{-- cost for unit service   --}}
                                    <td class=" text-right">{{ date('M d, Y',strtotime($serv->created_at)) }}</td>
                                    <td class=" text-center">
                                            <a href=""
                                                onclick=" event.preventDefault();document.getElementById('DeleteService{{ $serv->service_charge_id }}').submit();"
                                                class="text-danger">Remove <i class=" icon-cross2"></i></a>
                                        <form
                                            action="{{ route('booking.destroy', ['booking' => $serv->service_charge_id]) }}"
                                            method="post" id="DeleteService{{ $serv->service_charge_id }}">@csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                               
                            @endforeach
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
