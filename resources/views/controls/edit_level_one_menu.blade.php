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
                <li class="active">Edit-menu</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
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
                        <a href="{{ route('system_menus_level_one.index') }}" class=" btn btn-default text-primary"><i
                                class=" icon-arrow-left22"></i> Back</a>
                    </div>

                    <div class="">
                        @foreach ($level_one_menus as $item)
                            <form
                                action="{{ route('system_menus_level_one.update', ['system_menus_level_one' => $item->level_one_menu_id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="">
                                    <div class=" form-group">
                                        <label>Menu Name<i class=" text-danger">*</i></label>
                                        <input type="text" name="level_one_common_name" class=" form-control"
                                            placeholder="Menu Name" value="{{ $item->level_one_common_name }}">
                                        <span class="text-danger"> @error('level_one_common_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group">
                                        <label>Menu Link<i class=" text-danger">*</i></label>
                                        <input type="text" name="level_one_menu_link" class=" form-control"
                                            placeholder=" Gate name" value="{{ $item->level_one_menu_link }}">
                                        <span class="text-danger"> @error('level_one_menu_link')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group">
                                        <label>Menu Icon<i class=" text-danger">*</i></label>
                                        <input type="text" name="level_one_icon_name" class=" form-control"
                                            placeholder="Icon Name" value="{{ $item->level_one_icon_name }}">
                                        <span class="text-danger"> @error('level_one_icon_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group">
                                        <label>Save To which Module<i class=" text-danger">*</i></label>
                                        <select name="moduleNo" class="bootstrap-select "
                                            data-live-search="true" data-width="100%" placehoder="Select menu status">
                                            <option value="{{ $item->moduleNo }}">{{ $item->moduleNo }}</option>
                                            <option value="1">SAMS</option>
                                            {{-- [GEMEMIS-CLEANUP] ePermit option removed --}}
                                        </select>
                                        <span class="text-danger"> @error('moduleNo')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class=" form-group">
                                        <label>Menu Status</label>
                                        <select name="level_one_menu_status" class="bootstrap-select "
                                            data-live-search="true" data-width="100%" placehoder="Select menu status">
                                            <option value="{{ $item->level_one_menu_status }}">
                                                @if ($item->level_one_menu_status == 1)
                                                    Show
                                                @else
                                                    Hidden
                                                @endif
                                            </option>
                                            <option value="1">Show</option>
                                            <option value="0">Hide</option>
                                        </select>
                                        <span class="text-danger"> @error('level_one_menu_status')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class=" form-group">
                                        <label> Gate Name <i class=" text-muted">(parmision to see the menu)</i><i
                                                class=" text-danger">*</i></label>
                                        <input type="text" name="level_one_gate_name" class=" form-control"
                                            placeholder=" Gate Name" value="{{ $item->level_one_gate_name }}">
                                        <span class="text-danger"> @error('level_one_gate_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class=" text-right">
                                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"
                                        data-style="slide-down"><span class="ladda-label">Add Menu</span></button>
                                </div>
                            </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- //user registration  --}}
</x-app-layout>
