<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
          @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('book-services.create') }}"><i class=" icon-server position-left"></i> Booking Service</a></li>
            <li class="active"> Book services </li>
        </ul>
        <ul class="breadcrumb-elements">
         @include('layouts.help')
        </ul>
    </div>
    </div>
 <!-- /page header -->

 <div class="content">
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title text-bold">REGISTER NEW CUSTOMER SPONSORSHIP PACKAGE</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body table-responsive">
    <div class=" form-group">
        <a href="{{ route('sponsorships.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a>
        <br><br>
        <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
    </div>
        <form action="{{ route('book-packages.store') }}" method="POST">
            @csrf
         <div class="">
             <div class=" form-group col-md-6">
                 <label>Select Cusomer / Company Name <i class=" text-danger">*</i></label>
                <select name="customer_name" class=" form-control" data-live-search="true" data-width="100%" placehoder="Select customer">
                    <option value="{{ old('customer_name') }}">Select Customer Name . . . .</option>
                    @foreach ($companies as $item)
                    <option value="{{ $item->companyID }}">{{ $item->companyName }}</option>
                    @endforeach
                </select>
                <span class="text-danger"> @error('customer_name'){{ $message }}@enderror</span>
             </div>
             <div class=" form-group col-md-6">
                 <label>Customer Sponsorship Amount (TZS)<i class=" text-danger">*</i></label>
                 <input type=" text" name="customer_amount" class="form-control" placeholder="Enter customer Sponsorship amount (TZS)" value="{{ old('customer_amount') }}">
                 <span class=" text-danger">@error('customer_amount') {{ $message }} @enderror </sapan>
             </div>
             <div class=" form-group col-md-6">
                 <label>Select Package Name<i class=" text-danger">*</i></label>
                 <select id="type" name="package_name" class="form-control">
                     <option value="item0">--Select Sponsorship package--</option>
                     @foreach($packages as $value)
                     <option value="{{ $value->package_id }}">{{ $value->package_name }}____{{ number_format($value->from_amount) }} - {{ number_format($value->to_amount) }} {{ $value->type_of_currency }}</option>
                     @endforeach
                 </select>
                 <span class="text-danger"> @error('package_name'){{ $message }}@enderror</span>
             </div>
              <div class=" form-group col-md-6">
                 <label>Select year of Exhibition<i class=" text-danger">*</i></label>
                <select name="exhibition_year" class=" form-control" placehoder="Select customer">
                    @for ($i =2022-1976 ; $i <=100 ; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <span class="text-danger"> @error('exhibition_year'){{ $message }}@enderror</span>
             </div>
         </div>
             <div class=" text-right">
                 <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label"> Book Package</span></button>
             </div>
     </form>

       {{--  booked services  --}}
       <div class=" form-group">
           <table class=" table  table-bordered table-striped table-responsive table-lg text-nowrap">
               <thead>
                   <tr class=" bg-success">
                       @foreach ($booked_package as $data)
                       @endforeach
                       <th colspan="6" class=" text-bold text-uppercase text-center">Booked Services For  {{ $data->companyName ?? '(Customer Name)' }} </th>
                   </tr>
                   <tr>
                       <th class=" text-bold">S/N</th>
                       <th class=" text-bold">Package Name</th>
                       <th class=" text-bold">Customer Amount</th>
                       <th class=" text-bold">Type Of Currency</th>
                       <th class=" text-bold">Exhibition Year</th>
                       <th class=" text-bold">Action</th>
                   </tr>
               </thead>
               <tbody>
                   @php
                       $i=1;
                   @endphp
                @foreach ($booked_package as $item)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $item->package_name }}</td>
                    <td>{{ number_format($item->package_amount,2) }}</td>
                    <td>{{ $item->type_of_currency }}</td>
                    <td></td>
                    <td>
                        <form action="{{ route('sponsorship-bookings.destroy') }}" method="POST">
                            @csrf
                        @method('DELETE')
                        <button type=" submit" class=" btn btn-danger"><i class=" icon-bin"></i></button>
                        </form>
                    </td>
                </tr>
                @php
                $i++;
            @endphp
            @endforeach
               @if ($i>1)
               <form action="{{ route('sponsorship-bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_name" value="{{ $item->company_id }}">
                <input type="hidden" name="exhibition_year" value="{{ $item->exhibition_year }}">
                <input type="hidden" name="confirm_booking" value="ok">
                <tr>
                    <td colspan="6">
                       <div class=" text-right">
                        <button class=" btn btn-primary ">
                            Confirm Booking
                        </button>
                       </div>
                    </td>
                </tr>
            </form>
               @endif

               </tbody>
           </table>
       </div>
       {{--  end of booked service  --}}
 </div>
 </div>
 </div>
 <!-- /content area -->
 </div>
 <!-- /main content -->
 </x-app-layout>
