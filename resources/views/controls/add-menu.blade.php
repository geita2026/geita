<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('menus.create') }}"><i class=" icon-menu10 position-left"></i>MENUS</a></li>
            <li class="active">Menu-registration</li>
        </ul>

        <ul class="breadcrumb-elements">
            <li><a href="#"><i class="icon-comment-discussion position-left"></i>Help center</a></li>
        </ul>
    </div>
    </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body table-responsive">

  <div class=" panel-body">
    <div class=" form-group">
        <a href="{{ route('menus.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a>
    </div>
         {{-- messages --}}
    <div class="form-group">
        @if (Session::get('success'))

        <div class=" alert alert-success alert-bordered alert-rounded">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Congraduration!</span> {{ session::get('success') }}
        </div>
        @elseif (Session::get('fail'))
        <div class=" alert alert-danger alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Oops!</span> {{ session::get('success') }}
        </div>
        @endif
     </div>
     {{-- //messages --}}

{{-- user registration here --}}

       <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
                   @csrf
                <div class="">
                     <div class=" form-group">
                         <label>Resource Name<i class=" text-danger">*</i></label>
                         <input type="text" name="resource_name" class=" form-control" placeholder="Resource Name" value="{{ old('resource_name') }}">
                         <span class="text-danger"> @error('resource_name'){{ $message }}@enderror</span>
                      </div>
                      <div class=" form-group">
                        <label>Gate Name<i class=" text-danger">*</i></label>
                        <input type="text" name="gate_name" class=" form-control" placeholder=" Gate name" value="{{ old('gate_name') }}">
                        <span class="text-danger"> @error('gate_name'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group">
                        <label>Common Name (will display on side menu)<i class=" text-danger">*</i></label>
                        <input type="text" name="common_name" class=" form-control" placeholder="Common Name" value="{{ old('common_name') }}">
                        <span class="text-danger"> @error('common_name'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group">
                         <label>Menu Status</label>
                        <select name="menu_status" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                            <option value="">Select Menu Status....</option>
                            <optgroup label="Menu Statuses">
                               <option value="1">Show</option>
                               <option value="0">Hide</option>
                            </optgroup>
                        </select>
                        <span class="text-danger"> @error('menu_status'){{ $message }}@enderror</span>
                     </div>

                     <div class=" form-group">
                        <label>Sidebar Season</label>
                       <select name="sidebar_season" class="bootstrap-select" data-live-search="true" data-width="100%" placehoder="Select menu status">
                           <option value=""> - - - Select Sidebar Season - - -</option>
                           <optgroup label="sidebar season">
                              <option value="On-Fair">On-Fair</option>
                           </optgroup>
                       </select>
                       <span class="text-danger"> @error('menu_status'){{ $message }}@enderror</span>
                    </div>

                     <div class=" form-group">
                        <label>Icon Name<i class=" text-danger">*</i></label>
                        <input type="text" name="icon_name" class=" form-control" placeholder=" Icon name" value="{{ old('icon_name') }}">
                        <span class="text-danger"> @error('icon_name'){{ $message }}@enderror</span>
                     </div>
                </div>
                    <div class=" text-right">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" data-style="slide-down"><span class="ladda-label">Add Menu</span></button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- //user registration  --}}
</x-app-layout>

