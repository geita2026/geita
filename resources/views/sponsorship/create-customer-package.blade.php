<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('customer_packages.create') }}"><i class=" icon-package position-left"></i>CUSTOMER PACKAGES</a></li>
            <li class="active">Create Customer Packages</li>
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


{{-- user registration here --}}

       <form action="{{ route('customer_packages.store') }}" method="POST">
                   @csrf
                <div class="">
                    <div class=" form-group col-md-6">
                        <label>Select Cusomer / Company Name <i class=" icon-question3"></i></label>
                       <select name="customer_name" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select customer">
                           <option value="{{ old('customer_name') }}">Select Customer Name . . . .</option>
                           @foreach ($companies as $item)
                           <option value="{{ $item->companyID }}">{{ $item->companyName }}</option>
                           @endforeach
                       </select>
                       <span class="text-danger"> @error('customer_name'){{ $message }}@enderror</span>
                    </div>
                     <div class=" form-group col-md-6">
                         <label>Package name<i class=" text-danger">*</i></label>
                         <input type="text" name="package_name" class=" form-control" placeholder="Sponsorship Package Name" value="{{ old('package_name') }}">
                         <span class="text-danger"> @error('package_name'){{ $message }}@enderror</span>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>Package Amount (Cost)</label>
                        <input type="number" name="package_amount" class=" form-control" placeholder="Package Cost" value="{{ old('package_amount') ?? 0 }}">
                        <span class="text-danger"> @error('package_amount'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-6">
                        <label>Type Of Currency</label>
                       <select name="type_of_currency" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department">
                           <option value="">Select Type of currency . . . .</option>
                           <option value="TZS">TZS</option>
                           <option value="USD">USD</option>
                       </select>
                       <span class="text-danger"> @error('type_of_currency'){{ $message }}@enderror</span>
                    </div>

                     <div class=" form-group col-md-6">
                        <label>Package Description</label>
                        <textarea name="package_description" class=" form-control" placeholder="Package Descriptions">{{ old('package_description') }}</textarea>
                        <span class="text-danger"> @error('package_description'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-6">
                        <label>Select year of Exhibition <i class=" icon-question3"></i></label>
                       <select name="exhibition_year" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select customer">
                           @for ($i =2022-1976 ; $i <=100 ; $i++)
                           <option value="{{ $i }}">{{ $i }}</option>
                           @endfor
                       </select>
                       <span class="text-danger"> @error('exhibition_year'){{ $message }}@enderror</span>
                    </div>
                </div>
                    <div class=" text-right">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register-User</span></button>
                    </div>
            </form>
        </div>
    </div>
</div>
{{-- //user registration  --}}
</x-app-layout>
