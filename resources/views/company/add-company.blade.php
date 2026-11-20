<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('companies.create') }}"><i class=" icon-office position-left"></i>COMPANIES</a></li>
            <li class="active">Company-registration</li>
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
        <h6 class="panel-title text-bold"> REGISTER COMPANIES</h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
<div class="panel-body table-responsive">

      {{-- //upload file modal --}}
      <div id="upload" class="modal fade" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">UPLOAD EXCEL COMPANY FILE</h5>
                </div>
                <div class="modal-body">
                    <code>Note!:</code>
                    <li>File formart must be excel.</li>
                    <li>column must be arranged as follows: company name,company email,company phone,company category,company location.</li>
                    <li>Company name column must be filled otherwise row will be skiped.</li>
                    <li>Muximum file size must be 100MB.</li>
                    <li>Company name must not be dublicated</li>
                    <form action="{{ route('companies.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" form-group mt-20">
                            <label>Company excel file<i class=" text-danger">*</i></label>
                            <input type="file" name="company_file" class=" form-control">
                        </div>
                       <div class="text-right">
                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                        <button type="submit" class="btn btn-primary ml-20" >Upload File</button>
                       </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
     {{-- //upload file modal --}}
   <div class="row" >
    <div class=" form-group col-md-6 text-left">
        <a href="{{ route('companies.index') }}" class=" btn btn-default text-primary" ><i class=" icon-arrow-left12"></i> Back</a>
    </div>
     <div class=" form-group col-md-6 text-right">
        <a data-toggle="modal" data-target="#upload" class=" btn btn-primary" data-popup="tooltip" title="Upload mass companies data">Upload Company Excel File <i class=" icon-file-empty"></i></a>
    </div>
   </div>


{{-- user registration here --}}
       <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                   @csrf
                   <div class=" row">
                    <div class=" form-group col-md-6">
                        <label>Company Name<i class=" text-danger">*</i></label>
                        <input type="text" name="company_name" class=" form-control" placeholder="Company Name" value="{{ old('company_name') }}">
                        <span class="text-danger"> @error('company_name'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-6">
                       <label>Company Email</label>
                       <input type="text" name="company_email" class=" form-control" placeholder=" Company Email" value="{{ old('company_email') }}">
                       <span class="text-danger"> @error('company_email'){{ $message }}@enderror</span>
                    </div>
                    <div class=" form-group col-md-6">
                       <label>Campany Phone<i class=" text-danger">*</i></label>
                       <input type="text" name="company_phone" class=" form-control" placeholder=" start with: 255..,254..,91.. etc" value="{{ old('company_phone') }}">
                       <span class="text-danger"> @error('company_phone'){{ $message }}@enderror</span>
                    </div>
                    <div class=" form-group col-md-6">
                        <label>Company Location<i class=" text-danger">*</i></label>
                        <input type="text" name="company_location" class=" form-control" placeholder=" Company Location" value="{{ old('company_location') }}">
                        <span class="text-danger"> @error('company_location'){{ $message }}@enderror</span>
                     </div>

                    <div class=" form-group col-md-6">
                        <label>Company Sector<i class=" text-danger">*</i></label>
                       <select name="company_sector" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select Company category">
                           <option value="">Select Sector . . . </option>
                           <optgroup label="Company Sectors">
                             @foreach($sectors as $sector)
                             <option value="{{ $sector->sectorID }}">{{ $sector->sectorName }}</option>
                             @endforeach
                           </optgroup>
                       </select>
                       <span class="text-danger"> @error('company_sector'){{ $message }}@enderror</span>
                    </div>
                    <div class=" form-group col-md-6">
                        <label>Company Category<i class=" text-danger">*</i></label>
                       <select name="company_category" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select Company category">
                           <option value="">Select Category . . . </option>
                           <option value="1">Government Institution</option>
                            <option value="2">Private Companies</option>
                            <option value="3">SMEs</option>
                       </select>
                       <span class="text-danger"> @error('company_category'){{ $message }}@enderror</span>
                    </div>
                   </div>


                   <div class=" text-right">
                       <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Add Company</span></button>
                   </div>
            </form>
        </div>
    </div>
</div>
{{--  </div>  --}}
{{-- //user registration  --}}
</x-app-layout>
