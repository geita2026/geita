<x-app-layout>
    <title>{{ env('APP_NAME') }} | Logs</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="  icon-gear position-left text-uppercase"></i>User Audit</a></li>
            <li class="active">Check user Activities</li>
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
<div class=" panel-body">
    <div class="row">
        <div class=" form-group col-md-2">
            <a href="{{ route('audits.index') }}" class=" btn btn-default border-blue-400 "><i class=" icon-arrow-left12"></i> Back </a>
         </div>

         <div class=" col-md-10 form-group ">
            @php $i=0; @endphp
            @foreach ($data as $item)
            @php $i++; @endphp
            @endforeach

            @if ($i!=0)
            <b id="h3"  class=" text-center btn btn-default btn-block content-group border-blue-400 text-bold table-header">{{ $item->first_name }} {{ $item->last_name }}'s Logs</b>
            @endif
         </div>
    </div>

     <table class=" table table-sm datatable-button-html5-columns table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">#</th>
                            <th class=" text-bold text-center">EVENT PERFORMED</th>
                            <th class=" text-bold text-center">DATE OF ACTION</th>
                            <th class=" text-bold text-center">ACTIONS</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($data as  $item)
                       <tr>

                        <td>{{ $i }}</td>


                        <td><a href="{{ route('audits.show',['audit'=>$item->id ?? 'Audit']) }}" class=" text-bold">{{ $item->event }} <i class=" icon-arrow-right14"></i> {{ $item->auditable_type }}</a></td>
                        <td>{{ date('d-m-Y h:i:s',strtotime($item->created_at)) }}</td>

                        <td class="text-center">
                           @can('DELETE_AUDIT')
                           <a data-toggle="modal" data-target="#delete{{ $item->id }}" class=" text-danger"> <i class="icon-bin"></i></a>
                           @endcan
                        </td>

                        {{-- delete modal --}}
                        <div id="delete{{ $item->id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h5 class="modal-title"><i class=" icon-warning22"></i> Are you sure you whant to delete this User Activity ?</h5>
                                    </div>

                                    <div class="modal-body">
                                        <form action="{{ route('audits.destroy', ['audit' => $item->id ?? 'Audit']) }}" method="POST">
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
            {{-- //data here --}}
        </div>
    </div>
<!-- /dashboard content -->
</x-app-layout>
