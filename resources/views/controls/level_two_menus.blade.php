<x-app-layout>
    <title>{{ env('APP_NAME') }} | Menu</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-comptwont">
        <ul class="breadcrumb">
            <li><a href="{{ route('system_menus_level_two.index') }}"><i class=" icon-menu10 position-left"></i>MENUS</a></li>
            <li class="active">View-All-Menu</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body">

        <div class=" row form-group">

            <div class=" col-md-6">
                <a href="{{ route('system_menus_level_one.index') }}" class=" btn btn-default border-primary">
                    <b class=" text-uppercase text-bold text-primary"> <i class=" icon-arrow-left12"></i> Back</b>
                </a>
            </div>

            <div class=" col-md-6 text-right">
                <a  href="{{ route('system_menus_level_two.create') }}" class=" btn  btn-default border-success"> <i class=" icon-plus2"> Sub Menu</i></a>
            </div>
        </div>
                {{-- edit modal --}}
            <div id="submenu" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-bold text-center">ADD SUB MENU</h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('system_menus_level_two.store') }}" method="POST">
                                @csrf
                                <div class=" form-group">
                                    <label>Main Menu Name</label>
                                   <select name="upper_level_id" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                                       <option value="">- - - - Select Sub Menu Name - - - -</option>
                                          @foreach ($level_one_menus as $item)
                                          <option value="{{ $item->leve_one_menu_id }}">{{ $item->level_one_common_name }}</option>
                                          @endforeach
                                   </select>
                                   <span class="text-danger"> @error('upper_level_id'){{ $message }}@enderror</span>
                                </div>

                                <div class=" form-group">
                                    <label>Sub Menu Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="level_two_common_name" class=" form-control" placeholder="Menu Name" value="{{ old('level_two_common_name') }}">
                                    <span class="text-danger"> @error('level_two_common_name'){{ $message }}@enderror</span>
                                 </div>

                                 <div class=" form-group">
                                   <label>Menu Link<i class=" text-danger">*</i></label>
                                   <input type="text" name="level_two_menu_link" class=" form-control" placeholder=" Gate name" value="{{ old('level_two_menu_link') }}">
                                   <span class="text-danger"> @error('level_two_menu_link'){{ $message }}@enderror</span>
                                </div>

                                <div class=" form-group">
                                   <label>Menu Icon<i class=" text-danger">*</i></label>
                                   <input type="text" name="level_two_icon_name" class=" form-control" placeholder="Icon Name" value="{{ old('level_two_icon_name') }}">
                                   <span class="text-danger"> @error('level_two_icon_name'){{ $message }}@enderror</span>
                                </div>

                                <div class=" form-group">
                                    <label>Menu Status</label>
                                   <select name="level_two_menu_status" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                                       <option value="">- - - - Select Menu Status - - - -</option>
                                          <option value="1">Show</option>
                                          <option value="0">Hide</option>
                                   </select>
                                   <span class="text-danger"> @error('level_two_menu_status'){{ $message }}@enderror</span>
                                </div>

                                <div class=" form-group">
                                   <label> Gate Name <i class=" text-muted">(parmision to see the menu)</i><i class=" text-danger">*</i></label>
                                   <input type="text" name="level_two_gate_name" class=" form-control" placeholder=" Gate Name" value="{{ old('level_two_gate_name') }}">
                                   <span class="text-danger"> @error('level_two_gate_name'){{ $message }}@enderror</span>
                                </div>



                                {{-- <input type="hidden" name="upper_level_id" value=" @foreach ($level_one_menus as $item) {{ $item->level_one_menu_id }} @endforeach"> --}}

                               <div class=" text-right">
                                   <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" data-style="slide-down"><span class="ladda-label">Add Sub Menu</span></button>
                               </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                <th colspan="6" class="text-center text-bold"><h4>REGISTERED SUB MENU LIST</h4></th>
                            </tr>
                            <tr>
                                {{-- <th class=" text-center text-bold">S/N</th> --}}
                                <th class=" text-center text-bold">MENU NAME</th>
                                <th class=" text-center text-bold">MENU LINK</th>
                                <th class=" text-center text-bold">MENU ICON</th>
                                <th class=" text-center text-bold">MENU GATE</th>
                                <th class=" text-center text-bold">MENU STATUS</th>
                                <th class=" text-center text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($level_two_menus as  $item)
                           <tr>
                            {{-- <td>{{ $i }}</td> --}}
                            <td>{{ $item->level_two_common_name }}</td>
                            <td>{{ $item->level_two_menu_link }}</td>
                            <td >{{ $item->level_two_icon_name }}</td>
                            <td>{{ $item->level_two_gate_name }}</td>
                            <td  class=" text-center">@if($item->level_two_menu_status==1) <b class="alert-success label">ON</b> @else <b class="alert-danger p-10">OFF</b> @endif</td>



                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('Update Menu')
                                            <li>
                                                <a href="{{ route('system_menus_level_two.edit',['system_menus_level_two'=>$item->level_two_menu_id]) }}"  class="text-primary"><i class=" icon-pencil7"></i> Edit Sub Menus</a>

                                            </li>
                                            @endcan

                                            @can('Delete Menu')
                                            <li>
                                                <a  data-toggle="modal" data-target="#delete{{ $item->level_two_menu_id }}"  class=" text-danger"><i class="icon-bin"></i> Delete Menu</a>
                                            </li>
                                            @endcan

                                        </ul>
                                    </li>
                                </ul>
                            </td>

                                {{-- delete modal --}}
                                <div id="delete{{ $item->level_two_menu_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title text-uppercase">Are you sure you whant to delete <q>{{ $item->level_two_common_name }}</q> Sub Menu ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('system_menus_level_two.destroy', ['system_menus_level_two' => $item->level_two_menu_id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                   <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                                   </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- delete modal --}}
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
</x-app-layout>
