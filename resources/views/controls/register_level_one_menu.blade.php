<x-app-layout>
    <title>{{ env('APP_NAME') }} | Menu</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class=" icon-menu10 position-left"></i>MENUS</a></li>
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
    <div class="row bg-teal">
        <div class=" col-md-2 form-group">
            <h4><a href="{{ route('system_menus_level_one.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a></h4>
        </div>
        <div class=" form-group col-md-10">
            <h1 class=" text-bold text-center"> REGISTER NEW SIDEBAR MAIN MENU</h1>
        </div>
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

       <form action="{{ route('system_menus_level_one.store') }}" method="POST" enctype="multipart/form-data">
                   @csrf
                <div class="">
                     <div class=" form-group">
                         <label>Menu Name<i class=" text-danger">*</i></label>
                         <input type="text" name="level_one_common_name" class=" form-control" placeholder="Menu Name" value="{{ old('level_one_common_name') }}">
                         <span class="text-danger"> @error('level_one_common_name'){{ $message }}@enderror</span>
                      </div>
                      <div class=" form-group">
                        <label>Menu Link<i class=" text-danger">*</i></label>
                        <input type="text" name="level_one_menu_link" class=" form-control" placeholder=" Gate name" value="{{ old('level_one_menu_link') }}">
                        <span class="text-danger"> @error('level_one_menu_link'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group">
                        <label>Menu Icon<i class=" text-danger">*</i></label>
                        <input type="text" name="level_one_icon_name" class=" form-control" placeholder="Icon Name" value="{{ old('level_one_icon_name') }}">
                        <span class="text-danger"> @error('level_one_icon_name'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group">
                        <label>Save To which Module<i class=" text-danger">*</i></label>
                        <select name="moduleNo" class="bootstrap-select "
                            data-live-search="true" data-width="100%" placehoder="Select menu status">
                            <option>- - - Select System Module - - -</option>
                            <option value="1">SAMS</option>
                            <option value="2">ePermit</option>
                        </select>
                        <span class="text-danger"> @error('moduleNo')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                     <div class=" form-group">
                         <label>Menu Status</label>
                        <select name="level_one_menu_status" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                            <option value="">- - - - Select Menu Status - - - -</option>
                               <option value="1">Show</option>
                               <option value="0">Hide</option>
                        </select>
                        <span class="text-danger"> @error('level_one_menu_status'){{ $message }}@enderror</span>
                     </div>

                     <div class=" form-group">
                        <label> Gate Name <i class=" text-muted">(parmision to see the menu)</i><i class=" text-danger">*</i></label>
                        <input type="text" name="level_one_gate_name" class=" form-control" placeholder=" Gate Name" value="{{ old('level_one_gate_name') }}">
                        <span class="text-danger"> @error('level_one_gate_name'){{ $message }}@enderror</span>
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

