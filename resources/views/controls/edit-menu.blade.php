<x-app-layout>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>


        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('menus.create') }}"><i class=" icon-menu10 position-left"></i>MENUS</a></li>
            <li class="active">Edit-menu</li>
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
    
                   @csrf
                <div class="">
                    @foreach ($menus as $item)
                    <form action="{{ route('menus.update',['menu'=>$item->menu_id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                     <div class=" form-group">
                         {{-- @hasrole('supper-admin'); --}}

                         <label>Resource Name<i class=" text-danger">*</i></label>
                         <input type="text" name="resource_name" class=" form-control" placeholder="Resource Name" value="{{ $item->resource_name }}" readonly>
                         <span class="text-danger"> @error('resource_name'){{ $message }}@enderror</span>
                      </div>
                      <div class=" form-group">
                        <label>Gate Name<i class=" text-danger">*</i></label>
                        <input type="text" name="gate_name" class=" form-control" placeholder=" Gate name" value="{{ $item->gate_name }}">
                        <span class="text-danger"> @error('gate_name'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group">
                        <label>Common Name (will display on side menu)<i class=" text-danger">*</i></label>
                        <input type="text" name="common_name" class=" form-control" placeholder="Common Name" value="{{ $item->common_name }}">
                        <span class="text-danger"> @error('common_name'){{ $message }}@enderror</span>
                     </div>
                     <div class=" form-group">
                         <label>Menu Status</label>
                        <select name="menu_status" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                            <option value="{{ $item->status }}">{{ $item->status }}</option>
                            <optgroup label="Menu Statuses">
                               <option value="1">Show Menu</option>
                               <option value="0">Hide Menu</option>
                            </optgroup>
                        </select>
                        <span class="text-danger"> @error('menu_status'){{ $message }}@enderror</span>
                     </div>

                     <div class=" form-group">
                        <label>Sidebar Season</label>
                       <select name="sidebar_season" class="bootstrap-select" data-live-search="true" data-width="100%" placehoder="Select menu status">
                           <option value="{{ $item->sidebar_season }}">{{ $item->sidebar_season }}</option>
                           <optgroup label="sidebar season">
                              <option value="On-Fair">On-Fair</option>
                              <option value="Off-Fair">Off-Fair</option>
                           </optgroup>
                       </select>
                       <span class="text-danger"> @error('menu_status'){{ $message }}@enderror</span>
                    </div>

                     <div class=" form-group">
                        <label>Icon Name<i class=" text-danger">*</i></label>
                        <input type="text" name="icon_name" class=" form-control" placeholder=" Icon name" value="{{ $item->icon_name }}">
                        <span class="text-danger"> @error('icon_name'){{ $message }}@enderror</span>
                     </div>
                </div>
                @endforeach
                    <div class=" text-right">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Update Menu</span></button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- //user registration  --}}
</x-app-layout>

