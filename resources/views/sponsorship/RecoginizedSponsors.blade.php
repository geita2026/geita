<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsors</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
@include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left"></i> RECOGINAZED SPONSORS</a></li>
            <li class="active"> View All recoginazed sponsors</li>
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

         <div class=" row form-group">

            <div class=" col-md-12 text-right">
                <button class=" btn btn-default border-blue" data-toggle="modal" data-target="#Sponsor"><i class=" icon-add"></i> Add New Sponsor</button>
            </div>
         </div>

         {{-- user registration here --}}

         <div>


        {{-- /loaded data from database --}}
            <div id="Sponsor" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-bold text-center">ADD NEW EVENT SPONSOR</h5>
                            </div>
                            <div class="modal-body">

                                <div class=" form-group">
                                    <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                                </div>
                                <form action="{{ route('recoginized_sponsors.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                 <div class="row form-group">
                                      <div class=" form-group col-md-12">
                                          <label>Sponsor Name<i class=" text-danger">*</i></label>
                                          <input type="text" name="sponsor_name" class=" form-control" placeholder="Sponsor Name" value="{{  old('sponsor_name') }}">
                                          <span class="text-danger"> @error('sponsor_name'){{ $message }}@enderror</span>
                                       </div>
                                       <div class=" form-group col-md-12">
                                         <label>Sponsor Website<i class=" text-danger">*</i></label>
                                         <input type="text" name="sponsor_website" class=" form-control" placeholder=" Sponsor Website" value="{{old('sponsor_website') }}">
                                         <span class="text-danger"> @error('sponsor_website'){{ $message }}@enderror</span>
                                      </div>
                                      <div class=" form-group col-md-12">
                                         <label>Sponsor Logo</label>
                                         <input type="file" name="sponsor_logo" class=" form-control" placeholder=" Sponsor Logo" value="{{ old('sponsor_logo') }}">
                                         <span class="text-danger"> @error('sponsor_logo'){{ $message }}@enderror</span>
                                      </div>
                                      <div class=" form-group col-md-12">
                                        <label>Sponsorship Category</label>
                                        <select name="sponsorship_category" class="form-control">
                                            <option value="">- - - - Sponsorship Category - - - -</option>
                                            <option value="1">Catalogue Sponsors</option>
                                            <option value="2">Other Sponsorships</option>
                                        </select>
                                      </div>
                                 </div>
                            </div>
                            <div class=" modal-footer">
                                <div class="  form-group text-right">
                                    <button class=" btn btn-warning" data-dismiss="modal"> Close</button>
                                    <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Save</span></button>
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
                                    <h4 >LIST OF RECOGINIZED SPONSORS, WHO WILL BE DISPLAYED TO SITE PAGE</h4>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold">#</th>
                                <th class=" text-bold text-center text-uppercase">SPONSOR NAME</th>
                                <th class=" text-bold text-center text-uppercase">SPONSOR WEBSITE</th>
                                <th class=" text-bold text-center text-uppercase">SPONSOR LOGO</th>
                                <th class=" text-bold text-center text-uppercase">VIEW STATUS</th>
                                <th class=" text-bold text-center text-uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($sponsors as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $item->sponsor_name }}</td>
                                <td><a href="{{ $item->sponsor_website }}">{{ $item->sponsor_website }}</a></td>
                                <td class="text-center">
                                    <img src="{{ asset('VirtualImages/'.$item->sponsor_logo) }}" height="50" width="50" class=" img-rounded">
                                </td>
                                <td class=" text-uppercase">{{ $item->customer_home_location }}</td>

                                {{-- update customer modal --}}
                                <div id="update_customer{{ $item->sponsor_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title text-bold">UPDATE CUSTOMER DETAILS</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('recoginized_sponsors.update',['recoginized_sponsor'=>$item->sponsor_id])}}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="row">
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer Name<i class=" text-danger">*</i></label>
                                                                <input type="text" name="customer_name" class=" form-control" placeholder="Customer Name" value="{{ $item->customer_name ?? old('customer_name') }}">
                                                                <span class="text-danger"> @error('customer_name'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer phone<i class=" text-danger">*</i></label>
                                                                <input type="text" name="customer_phone" class=" form-control" placeholder=" start with: 255..., 254...., etc" value="{{$item->customer_phone ?? old('customer_phone') }}">
                                                                <span class="text-danger"> @error('customer_phone'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer Email</label>
                                                                <input type="text" name="customer_email" class=" form-control" placeholder=" Customer Email" value="{{$item->customer_email ?? old('customer_email') }}">
                                                                <span class="text-danger"> @error('customer_email'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <label>Customer Home Location<i class=" text-danger">*</i></label>
                                                                <input type="text" name="customer_home_location" class=" form-control" placeholder="CUstomer Home Location" value="{{ $item->customer_home_location ?? old('customer_home_location') }}">
                                                                <span class="text-danger"> @error('customer_home_location'){{ $message }}@enderror</span>
                                                            </div>
                                                            <div class=" col-md-12 form-group text-right">
                                                                <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Update Customer</span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /new customer modal --}}

                                    {{-- delete Sponsor Modal --}}
                                    <div id="DeleteSponsor{{ $item->sponsor_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h6 class="modal-title text-bold text-center">ARE YOU SURE YOU WANT TO DELETE SPONSORS DETAILS ? </h6>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('recoginized_sponsors.destroy',['recoginized_sponsor'=>$item->sponsor_id])}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('DELETE')
                                                        </div>
                                                        <div class=" modal-footer">
                                                            <div class="  form-group text-right">
                                                                <button class=" btn btn-warning" data-dismiss="modal"> Close</button>
                                                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Sponsor</span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    {{-- //delete Sponsor modal --}}


                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">

                                               @can('DELETE_SPONSOR')
                                                <li>
                                                    <a  data-toggle="modal" data-target="#DeleteSponsor{{ $item->sponsor_id }}" class=" text-danger"><i class="icon-bin"></i> Delete Sponsor</a>
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

</x-app-layout>
