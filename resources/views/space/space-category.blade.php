<x-app-layout>
    <title>{{ env('APP_NAME') }} | Pavilions</title>
    <!-- dashboard -->

    <div class="page-header">
        <div class="page-header-content">
        @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-gear position-left"></i>SPACE CATEGORIES</a></li>
            <li class="active">View All pavilion categories</li>
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

                <div class=" row">
                    <div class=" form-group text-right">
                        <a href="{{ route('spaceCategories.create') }}" class=" btn btn-success">Add Space Category <i class=" icon-plus2"></i></a>
                    </div>
                </div>


                <div class=" form-group table-responsive">

                    <table class="table datatable-button-html5-columns table-striped table-bordered  table-responsive">
                        <thead>
                            <tr class="table-header">
                                <th colspan="6">
                                    <h4 class=" text-bold  text-center">REGISTERED SPACE CATEGORIES</h4>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-center text-bold">#</th>
                                <th class=" text-center text-bold">CATEGORY NAME</th>
                                <th class=" text-center text-bold">COST PER UNIT</th>
                                <th class=" text-center text-bold">CATEGORY EVENTS</th>
                                <th class=" text-center text-bold">CATEGORY DESCRIPTIONS</th>
                                <th class=" text-center text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($categories as  $item)
                           <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $item->category_name }}</td>
                            <td>{{ number_format($item->cost_one_msq,2) }} {{ $item->type_of_currency }}</td>
                            <td>{{ $item->event_name }}</td>
                            <td>{{ $item->category_descriptions }}</td>

                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="{{ route('spaceCategories.edit',['spaceCategory'=>$item->category_id]) }}" class=" text-primary"> Edit Category <i class="icon-pencil7"></i></a>
                                            </li>
                                            <li>
                                                <a data-toggle="modal" data-target="#{{ $item->category_id}}"  class="text-danger"> Delete Category <i class=" icon-bin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                                {{-- delete modal --}}
                                <div id="{{ $item->category_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete {{ $item->category_name }} ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('spaceCategories.destroy', ['spaceCategory' => $item->category_id]) }}" method="POST">
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
    </div>

</x-app-layout>

<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<!-- /theme JS files -->
