<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>COMPANIES REGISTRATIONS</a></li>
            <li class="active">View-All Companies</li>
        </ul>
        <ul class="breadcrumb-elements">
           @include('layouts.help')
        </ul>
    </div>
</div>

<!-- Clickable title -->
<div class=" content">
<div class="panel panel-white">
    <div class="panel-heading">
        <h6 class="panel-title">COMPANY REGISTRATIONS</h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
    <div class=" panel-body">
    <div class="form-group p-20">
        <a href="{{ route('companies.index') }}" class=" btn border-primary"> <i class=" icon-arrow-left12"></i> Back</a>
    </div>

    <form  action="{{ route('companies.store') }}" method="POST" class="stepy-clickable">
        @csrf
        <fieldset title="1">
            <legend class="text-semibold">PRIMARY INFORMATIONS</legend>
            <div class="row">
                <div class=" form-group col-md-6">
                    <label>Company Name<i class=" text-danger">*</i></label>
                    <input type="text" name="company_name" class=" form-control required" placeholder="Company Name" value="{{ old('company_name') }}" required>
                    <span class="text-danger"> @error('company_name'){{ $message }}@enderror</span>
                 </div>
                 <div class=" form-group col-md-6">
                   <label>Company Email</label>
                   <input type="text" name="company_email" class=" form-control" placeholder=" Company Email ie. remixkabandwa@gmail.com" value="{{ old('company_email') }}">
                   <span class="text-danger"> @error('company_email'){{ $message }}@enderror</span>
                </div>
                <div class=" form-group col-md-6">
                   <label>Campany Phone<i class=" text-danger">*</i></label>
                   <input type="text" name="company_phone" class="form-control" placeholder="255753427137"  value="{{ old('company_phone') }}" required>
                   <span class="text-danger"> @error('company_phone'){{ $message }}@enderror</span>
                </div>
                <div class=" form-group col-md-6">
                    <label>Company Location<i class=" text-danger">*</i></label>
                    <input type="text" name="company_location" class=" form-control" placeholder=" Company Location" value="{{ old('company_location') }}" required>
                    <span class="text-danger"> @error('company_location'){{ $message }}@enderror</span>
                 </div>

                <div class=" form-group col-md-6">
                    <label>Company Sector<i class=" text-danger">*</i></label>
                   <select name="company_sector" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select Company category" required>
                       <option value=""> - - - Select Company Sector - - - </option>
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
                       <option value="">- - - Select Company Category Category - - -</option>
                       <optgroup label="Company Categories">
                        <option value="Government Institution">Government Institution</option>
                          <option value="Private Institution">Private Institution</option>
                       </optgroup>
                   </select>
                   <span class="text-danger"> @error('company_category'){{ $message }}@enderror</span>
                </div>
            </div>
        </fieldset>
        <button type="submit" class="btn btn-primary stepy-finish">Submit <i class="icon-check position-right"></i></button>
    </form>

</div>
</div>
</div>
<!-- /clickable title -->

</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/pages/wizard_stepy.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/wizards/stepy.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jasny_bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>
