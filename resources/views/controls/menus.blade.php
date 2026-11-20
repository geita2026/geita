<x-app-layout>
    <title>{{ env('APP_NAME') }} | Menu</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('menus.index') }}"><i class=" icon-menu10 position-left"></i>MENUS</a></li>
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
<div class=" panel-body table-responsive">


        @if (Session::get('success'))
        <div class="form-group p-20">
        <div class=" alert alert-success alert-bordered alert-rounded">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Congraduration!</span> {{ session::get('success') }}
        </div>
        </div>
        @elseif (Session::get('fail'))
        <div class="form-group p-20">
        <div class=" alert alert-danger alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Oops!</span> {{ session::get('fail') }}
        </div>
    </div>
        @endif



    <div class=" row">
                 {{--  My space  --}}
                 @can('System Control')
               <div class=" col-md-6">
                <a data-toggle="modal" data-target="#system_control" title="Click to request services" class=" btn btn-default border-primary">
                    <b class=" text-uppercase text-bold text-primary">Menu - Control <i class=" icon-gear"></i></b>
                  </a>
               </div>
                  {{--  reports  --}}

            {{-- edit modal --}}
            <div id="system_control" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-bold text-center">SYSTEM CONTROL PANEL</h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('menus.edit',['menu']) }}" method="POST">
                                @csrf
                                @method('GET')
                               <div class=" row">
                                <p class="alert-success p-20"> This action will close The {{ date('Y')-1976 }}<sup>th</sup> DITF and will hide all DITF menu</p>
                                <div class=" form-group">
                                    <label>Hide Menues</label>
                                    <select name="system_control" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">
                                        <option value=""> - - - Show All Menu - - -</option>
                                           <option value="On-Fair">On-Fair Services Menu</option>
                                           <option value="Off-Fair">Off-Fair Services Menu</option>
                                    </select>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button type="button" data-dismiss="modal" class=" btn btn-warning">Close X</button>
                                    <button type="submit" class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner" >Hide / Show Menu</button>
                                </div>

                               </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{--// edit modal --}}
                 @endcan

                    <div class=" col-md-6 text-right">
                        <a href="{{ route('menus.create') }}" class=" btn  btn-default border-success">Register New Menu <i class=" icon-add-to-list"></i></a>
                    </div>
                </div>

                    <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Resource Name</th>
                                <th>Gate Name</th>
                                <th>Common Name</th>
                                <th>Status</th>
                                <th>Menu Season</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($menus as  $item)
                           <tr>
                            <td>{{ $i }}</td>
                            <th>{{ $item->resource_name }}</th>
                            <td>{{ $item->gate_name }}</td>
                            <td >{{ $item->common_name }}</td>
                            <td >@if($item->status==1) <b class="alert-success p-10">Active</b> @else <b class="alert-danger p-10">Disabled</b> @endif</td>
                            <td>{{ $item->sidebar_season }}</td>
                            <td>
                                <a href="{{ route('menus.edit',['menu'=>$item->menu_id]) }}"> <i class="icon-pencil7"></i></a>
                                <a href="" data-toggle="modal" data-target="#{{ $item->menu_id }}"  class="text-danger ml-20"> <i class=" icon-bin"></i></a>
                            </td>
                                {{-- delete modal --}}
                                <div id="{{ $item->menu_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete this Menu ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('menus.destroy', ['menu' => $item->menu_id]) }}" method="POST">
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
