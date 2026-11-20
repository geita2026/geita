<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booths</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home9 position-left"></i>PAVILIONS</a></li>
            <li class="active">pavilion registration</li>
        </ul>

        <ul class="breadcrumb-elements">
            <li><a href="#"><i class="icon-comment-discussion position-left"></i>Help center</a></li>
        </ul>
    </div>
    </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title text-bold">REGISTER EXHIBITION SPACE </h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body table-responsive">

  <div class=" panel-body">
    <div class=" form-group">
        <a href="{{ route('pavilions.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a>
    </div>


       <form action="{{ route('pavilions.store') }}" method="POST" enctype="multipart/form-data">
                   @csrf
                <div class=" row">
                     <div class=" form-group col-md-6">
                         <label>Pavilion Name<i class=" text-danger">*</i></label>
                         <input type="text" name="pavilion_name" class=" form-control" placeholder="pavilion Name" value="{{ old('pavilion_name') }}">
                         <span class="text-danger"> @error('pavilion_name'){{ $message }}@enderror</span>
                      </div>
                      <div class=" form-group col-md-6">
                        <label>Pavilion Category </label>
                       <select name="pavilion_category" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select department">
                           <option value="">Select User Category....</option>
                           <optgroup label="Pavilion Categories">
                               <option value="Shared Pavilion">Local Shared Pavilion</option>
                               <option value="Foreign Shared Pavilion">Foreign Shared Pavilion</option>
                               <option value="Self bult Pavilion">Self bult Pavilion</option>
                               <option value="Stand alon Pavilion">Stand alon Pavilion</option>

                           </optgroup>
                       </select>
                       <span class="text-danger"> @error('pavilion_category'){{ $message }}@enderror</span>
                    </div>
                      <div class=" form-group col-md-6">
                        <label>Total Booths (if shared pavilion)<i class=" text-danger">*</i></label>
                        <input type="text" name="total_booths" class=" form-control" placeholder="Total Booths" value="{{ old('total_booths') }}">
                        <span class="text-danger"> @error('total_booths'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-6">
                        <label>Pavilion Rate<i class=" text-danger">*</i></label>
                        <input type="text" name="pavilion_rate" class=" form-control" placeholder=" Pavilion Rate" value="{{ old('pavilion_rate') }}">
                        <span class="text-danger"> @error('pavilion_rate'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group col-md-12">
                        <label>Pavilion Description</label>
                        <textarea name="pavilion_description" class=" form-control" placeholder="Pavilion description"></textarea>
                        <span class="text-danger"> @error('pavilion_description'){{ $message }}@enderror</span>
                     </div>

                </div>
                    <div class=" text-right">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" data-style="slide-down"><span class="ladda-label">Register-Pavilion</span></button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- //user registration  --}}
</x-app-layout>
