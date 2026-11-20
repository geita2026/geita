<x-app-layout>
    <title>{{ env('APP_NAME') }} | Menu</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('system_menus_level_one.index') }}"><i class=" icon-menu10 position-left"></i>MENUS</a></li>
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



               <div class=" row">
                    <div class=" col-md-12 text-right">
                        <a href="{{ route('system_menus_level_one.create') }}" class=" btn  btn-default border-success">Register New Menu <i class=" icon-add-to-list"></i></a>
                    </div>
                </div>

                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                {{-- <th class=" text-center text-bold">S/N</th> --}}
                                <th class=" text-center text-bold">MENU NAME</th>
                                <th class=" text-center text-bold">MENU LINK</th>
                                <th class=" text-center text-bold">MENU ICON</th>
                                <th class=" text-center text-bold">MENU GATE</th>
                                <th class=" text-center text-bold">MODULE NO.</th>
                                <th class=" text-center text-bold">MENU STATUS</th>
                                <th class=" text-center text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($level_one_menus as  $item)
                           <tr>
                            {{-- <td>{{ $i }}</td> --}}
                            <td>{{ $item->level_one_common_name }}</td>
                            <td>{{ $item->level_one_menu_link }}</td>
                            <td >{{ $item->level_one_icon_name }}</td>
                            <td>{{ $item->level_one_gate_name }}</td>
                            <td>{{ $item->moduleNo }}</td>
                            <td  class=" text-center">@if($item->level_one_menu_status==1) <b class="alert-success label">ON</b> @else <b class="alert-danger p-10">OFF</b> @endif</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('Update Menu')
                                            <li>
                                                <a href="{{ route('system_menus_level_one.edit',['system_menus_level_one'=>$item->level_one_menu_id]) }}" class=" text-primary"><i class=" icon-pencil7"></i> Edit Menu</a>
                                            </li>
                                            @endcan

                                            @can('Update Menu')
                                            <li>
                                                <a href="#" onclick=" event.preventDefault();document.getElementById('{{ $item->level_one_menu_id }}level_two_menu').submit();" class=" text-primary"><i class=" icon-link2"></i>Sub Menus</a>
                                                <form action="{{ route('system_menus_level_two.index',['system_menus_level_one'=>$item->level_one_menu_id]) }}" method="post" id="{{ $item->level_one_menu_id }}level_two_menu">@csrf
                                                    @method('GET')
                                                </form>
                                            </li>
                                            @endcan

                                            @can('Delete Menu')
                                            <li>
                                                <a  data-toggle="modal" data-target="#{{ $item->level_one_menu_id }}"  class=" text-danger"><i class="icon-bin"></i> Delete Menu</a>
                                            </li>
                                            @endcan

                                        </ul>
                                    </li>
                                </ul>
                            </td>

                                {{-- delete modal --}}
                                <div id="{{ $item->level_one_menu_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete <q>{{ $item->level_one_common_name }}</q> Menu ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('system_menus_level_one.destroy', ['system_menus_level_one' => $item->level_one_menu_id]) }}" method="POST">
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
