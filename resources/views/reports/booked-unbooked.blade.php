
<script>
    function printPageArea(areaID){
        var printContent = document.getElementById(areaID);
        var WinPrint = window.open('', '', 'width=900,height=650');
        WinPrint.document.write(printContent.innerHTML);
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
    }

    function printBooked(areaID){
        var printContent = document.getElementById(areaID);
        var WinPrint = window.open('', '', 'width=900,height=650');
        WinPrint.document.write(printContent.innerHTML);
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
    }
</script>
<title>{{ env('APP_NAME') }} | Space-Reports</title>
<x-app-layout>
     <div class="page-header">
       <div class="page-header-content">
        @include('layouts.heading-elements')
       </div>
       <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Booked Vs Unbooked</a></li>
            <li class="active">View booked and unbooked space</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
</div>

<!-- Content area -->
				<div class="content">
					<div class="panel">
						<div class="panel-body table-responsive">
                            <a href="{{route('reports.index')}}" class=" btn btn-default text-primary"> <i class=" icon-arrow-left12"></i> Back </a>
							<h4 class="text-center content-group-lg">
								WELCOME DITF GROUND INFORMATION KIT
								<small class="display-block">This is the greate information source for the
                                    Dar Es salaam International Trade Fair Grounds, this complises ability to
                                     filter information by date and category too.
                                    </small>
							</h4>
						<div class="col-md-12">
                            <form action="{{ route('bookedUnbookedSpaces.index') }}" method="POST">
                                @csrf
                                @method('GET')
                                <div class="form-group col-md-4">
                                    <label>Pavilion Category </label>
                                    <select name="pavilionCategory" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department">
                                        <option value="">--- All Spaces (Halls) Categories ---</option>
                                        <optgroup label="Pavilion Categories">
                                            @foreach ($space_categories as $item)
                                            <option value="{{$item->category_id}}">{{$item->category_name}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Space Name </label>
                                    <select name="pavilion_id" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department">
                                        <option value="">--- All Spaces (Halls) ---</option>
                                        <optgroup label="Pavilion Categories">
                                            @foreach ($spaces as $item)
                                            <option value="{{$item->pavilionID}}">{{$item->pavilionName}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

									<div class=" form-group col-md-2">
										<label>Exhibition Year</label>
										<select class=" form-control" name="exhibition_year">

											@php  $year=date('Y')-1976;  @endphp
											@for ($i =$year; $i <100 ; $i++)
											<option value="{{ $i }}">{{ $i }}</option>
											@endfor
										</select>
									</div>

									<div class=" form-group col-md-2 text-right mt-20">
										<button type="submit" class="btn btn-block btn-primary btn-ladda btn-ladda-spinner">Load Data <i class=" icon-database-refresh"></i></button>
									</div>
                            </form>
                        <div>
                           @can('export booking report')
                           <div class=" text-right p-20">
                            <a  href="/bookedUnbookedSpaces" download="{{date('d-m-Y')}}-ditf-booking-status.xlsx" class=" btn btn-default text-primary">Export To Excel <i class=" icon-file-excel"></i></a>
                        </div>
                           @endcan
                            <table class=" table table-striped table-bordered">
                                <thead>
                                    <tr class=" alert-info">
                                        <th class=" text-bold">Space (Hall) Name</th>
                                        {{-- <th class=" text-bold">Space Category</th> --}}
                                        <th class=" text-bold">Space Size In (M<sup>2</sup>) or Unit</th>
                                        <th class=" text-bold">Booked Space Size In (M<sup>2</sup>) or Unit</th>
                                        <th class=" text-bold">Amount Collected</th>
                                        <th class=" text-bold">Currency</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php $i=0; @endphp
                                    @foreach ($pavilions as $item)
                                    <tr>
                                        <td>{{$item->pavilionName}}</td>
                                        {{-- <td>{{$item->category_name}}</td> --}}
                                        <td>{{$item->pavilion_size}}</td>
                                            @if ($item->bookingStatus=='partialBooked')
                                           <td class=" alert-success"> {{$item->pavilion_size}}</td>
                                           <td>
                                            {{number_format($item->cost_one_msq*$item->pavilion_size,2)}}
                                         </td>
                                        <td>{{$item->type_of_currency}}</td>
                                           @else
                                            {{-- for shared pavilions --}}
                                            @php
                                            $booked=0;
                                            $amount_collected=0;
                                            $currency='-';
                                             @endphp
                                            @foreach ($booked_booths as $data)
                                            @if ($data->pavilionID==$item->pavilionID)
                                            @php
                                            $booked=$booked+$data->boothSize;
                                            $amount_collected=$amount_collected+($data->boothSize*$data->category_cost);
                                            $currency=$data->type_of_currency;
                                             @endphp
                                            @endif
                                            @endforeach
                                            {{-- /for shared pavilion --}}
                                           <td class=" alert-info">{{$booked}}</td>
                                           <td>{{ number_format($amount_collected,2) }}</td>
                                           <td>{{ $currency}}</td>
                                           @endif
                                    </tr>
                                    @php $i++; @endphp
                                    @endforeach
                                    @if ($i==0)
                                    <tr>
                                        <td colspan="5" class="alert-danger"> No data found!</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>

                        </div>
                        </div>
						</div>
					</div>
					<!-- /search field -->
				</div>
				<!-- /content area -->
</x-app-layout>

