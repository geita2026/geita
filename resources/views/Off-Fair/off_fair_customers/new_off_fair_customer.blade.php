<x-app-layout>
    <title>{{ env('APP_NAME') }} | Customers</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
@include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left"></i> OFF FAIR CUSTOMERS</a></li>
            <li class="active"> View All customers</li>
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

    {{-- validation messages --}}
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <span class="alert text-danger">{{ $error }} ,</span>
    @endforeach
    @endif
    {{-- //validation messages --}}

         <div class=" row">
            <div class=" col-md-6">
                <a href="{{ route('Off-Fair.index') }}" class=" btn border-slate"><i class=" icon-arrow-left22"></i> Back</a>
                <br><br>
            </div>
            <div class=" col-md-6 text-right">
                <button class=" btn btn-default border-blue" data-toggle="modal" data-target="#new_customer"><i class=" icon-add"></i> Add New Customer</button>
            </div>
         </div>

         {{-- user registration here --}}

         <div>

        {{-- New customer modal --}}
        {{-- loaded data from database --}}
        @foreach ($customers as $data)
        @endforeach
        {{-- /loaded data from database --}}
            <div id="new_customer" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-bold text-center">NEW CUSTOMER REGISTRATION</h5>
                            </div>
                            <div class="modal-body">

                               {{-- load data from exhilbitor list --}}
                               <form action="{{ route('Off-Fair-Customers.create') }}" method="POST">
                                @csrf
                                @method('GET')
                                <div class=" row">
                                    <div class=" form-group col-md-9">
                                        <label>Load customer informations from Exhibitor list (<i class=" text-primary"> Only if cutomer had been participated in DITF </i>)</label>
                                       <select name="companyID" class="bootstrap-select " data-live-search="true" data-width="100%">
                                           <option value="">- - - Select Customer from list - - -</option>
                                           <optgroup label="User Titles">
                                               @foreach ($exhibitors as $item)
                                               <option value="{{ $item->companyID}}">{{ $item->companyName }}</option>
                                               @endforeach
                                           </optgroup>
                                       </select>
                                    </div>
                                    <div class=" col-md-3 text-right mt-20">
                                        <button type="submit" class=" btn btn-block btn-primary btn-ladda btn-ladda-spinner">Load Data from exhibitor list <i class=" icon-database-refresh"></i></button>
                                    </div>
                                </div>
                            </form>
                               {{-- //load data from exhibitor list --}}
                                <div class=" form-group">
                                    <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                                </div>
                                <form action="{{ route('Off-Fair-Customers.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                 <div class="row form-group">
                                      <div class=" form-group col-md-12">
                                          <label>Customer Name<i class=" text-danger">*</i></label>
                                          <input type="text" name="customer_name" class=" form-control" placeholder="Customer Name" value="{{ $data->companyName ?? old('customer_name') }}">
                                          <span class="text-danger"> @error('customer_name'){{ $message }}@enderror</span>
                                       </div>
                                       <div class=" form-group col-md-12">
                                         <label>Customer phone<i class=" text-danger">*</i></label>
                                         <input type="text" name="customer_phone" class=" form-control" placeholder=" start with: 255..., 254...., etc" value="{{$data->companyPhone ?? old('customer_phone') }}">
                                         <span class="text-danger"> @error('customer_phone'){{ $message }}@enderror</span>
                                      </div>
                                      <div class=" form-group col-md-12">
                                         <label>Customer Email</label>
                                         <input type="text" name="customer_email" class=" form-control" placeholder=" Customer Email" value="{{$data->companyEmail ?? old('customer_email') }}">
                                         <span class="text-danger"> @error('customer_email'){{ $message }}@enderror</span>
                                      </div>
                                      <div class=" form-group col-md-12">
                                         <label>Customer Home Location<i class=" text-danger">*</i></label>
                                         <input type="text" name="customer_home_location" class=" form-control" placeholder="CUstomer Home Location" value="{{ $data->companyLocation ?? old('customer_home_location') }}">
                                         <span class="text-danger"> @error('customer_home_location'){{ $message }}@enderror</span>
                                      </div>
                                 </div>
                            </div>
                            <div class=" modal-footer">
                                <div class="  form-group text-right">
                                    <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                    <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register Off-Fair Customer</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- // new customer modal --}}

            {{-- table for available customers in database--}}
            <div class=" form-group row">
                <div class=" col-md-12">
                    <table class=" table table-bordered table-striped datatable-save-state">
                        <thead>
                            <tr>
                                <th colspan="6" style="background-color:rgb(202, 209, 214); text-align:center;">
                                    <h4 >LIST OF REGISTERED OFF FAIR AND FUNCTIONAL HALL CUSTOMERS.</h4>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold">#</th>
                                <th class=" text-bold text-center text-uppercase">Customer Name</th>
                                <th class=" text-bold text-center text-uppercase">Customer Phone</th>
                                <th class=" text-bold text-center text-uppercase">Customer Email</th>
                                <th class=" text-bold text-center text-uppercase">Customer Location</th>
                                <th class=" text-bold text-center text-uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($off_fair_customers as $customer)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $customer->customer_name }}</td>
                                <td>{{ $customer->customer_phone }}</td>
                                <td>{{ $customer->customer_email }}</td>
                                <td class=" text-uppercase">{{ $customer->customer_home_location }}</td>

                                {{-- update customer modal --}}
                                <div id="update_customer{{ $customer->customer_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title text-bold">UPDATE CUSTOMER DETAILS</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('Off-Fair-Customers.update',['Off_Fair_Customer'=>$customer->customer_id])}}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="row">
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer Name<i class=" text-danger">*</i></label>
                                                                <input type="text" name="customer_name" class=" form-control" placeholder="Customer Name" value="{{ $customer->customer_name ?? old('customer_name') }}">
                                                                <span class="text-danger"> @error('customer_name'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer phone<i class=" text-danger">*</i></label>
                                                                <input type="text" name="customer_phone" class=" form-control" placeholder=" start with: 255..., 254...., etc" value="{{$customer->customer_phone ?? old('customer_phone') }}">
                                                                <span class="text-danger"> @error('customer_phone'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer Email</label>
                                                                <input type="text" name="customer_email" class=" form-control" placeholder=" Customer Email" value="{{$customer->customer_email ?? old('customer_email') }}">
                                                                <span class="text-danger"> @error('customer_email'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer Home Location<i class=" text-danger">*</i></label>
                                                                <input type="text" name="customer_home_location" class=" form-control" placeholder="CUstomer Home Location" value="{{ $customer->customer_home_location ?? old('customer_home_location') }}">
                                                                <span class="text-danger"> @error('customer_home_location'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" col-md-12 form-group text-right">
                                                                <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Update Off-Fair Customer</span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /new customer modal --}}

                                    {{-- delete Customer Modal --}}
                                    <div id="delete_customer{{ $customer->customer_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h4 class="modal-title text-bold text-center">ARE YOU SURE YOU WANT TO DELETE CUSTOMER DETAILS ? </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class=" alert-danger p-20">Note: This action will delete All <b>{{ $customer->customer_name }}'S</b> Information Permanently.</p>
                                                        <form action="{{ route('Off-Fair-Customers.destroy',['Off_Fair_Customer'=>$customer->customer_id])}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('DELETE')

                                                    </div>
                                                    <div class=" modal-footer">
                                                        <div class="  form-group text-right">
                                                            <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Off-Fair Customer</span></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- //delete customer modal --}}
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                               @can('Update Off-Fair Customer')
                                            <li>
                                                <a data-toggle="modal" data-target="#update_customer{{ $customer->customer_id }}" class=" text-primary"><i class=" icon-pencil7"></i> Edit Customer Details</a>
                                            </li>
                                               @endcan

                                               @can('Delete Off-Fair Customer')
                                                <li>
                                                        <a  data-toggle="modal" data-target="#delete_customer{{ $customer->customer_id }}" class=" text-danger"><i class="icon-bin"></i> Delete Customer Details</a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
{{-- //user registration  --}}

<div id="myData">

</div>
<script>

</script>
</x-app-layout>
