<x-app-layout>
    <title>{{ env('APP_NAME') }} | Logs</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('audits.index') }}"><i class="  icon-gear position-left text-uppercase"></i>User
                        Audit</a></li>
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


                <div style=" background-color:rgb(227, 232, 236)" class=" col-md-12 form-group">
                    <h4 class=" p-20 text-center">LIST OF USER ACTIVITIES IN THE SYSTEM</h4>
                </div>



                <div class=" col-md-12 form-group">
                    <table
                        class=" table table-sm datatable-button-html5-columns table-bordered table-striped table-responsive">
                        <thead>
                            <tr>
                                <th class=" text-center text-bold">#</th>
                                <th class=" text-center text-bold">USER NAME</th>
                                <th class=" text-center text-bold">EVENT PERFORMED</th>
                                <th class=" text-center text-bold">DATE OF ACTION</th>
                                <th class=" text-center text-bold">ACTIONS</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($data as $item)
                                <tr>

                                    <td>{{ $i }}</td>
                                    <td><a href="{{ route('audits.show', ['audit' => $item->id]) }}"
                                            class=" text-bold">{{ $item->first_name }} {{ $item->middle_name }}
                                            {{ $item->last_name }}</a></td>
                                    <td><a href="{{ route('audits.show', ['audit' => $item->id]) }}"> {{ $item->event }} (<i style="font-size: 80%">{{ $item->auditable_type }}</i>)</a>
                                    </td>
                                    <td>{{ date('d-m-Y h:i:s', strtotime($item->created_at)) }}</td>

                                    <td class="text-center"><a data-toggle="modal"
                                            data-target="#delete{{ $item->id }}" class=" text-danger"> <i
                                                class="icon-bin"></i></a></td>

                                    {{-- delete modal --}}
                                    <div id="delete{{ $item->id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title"><i class=" icon-warning22"></i> Are you sure
                                                        you
                                                        whant to delete this User Activity ?</h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('audits.destroy', ['audit' => $item->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-danger ml-20">Yes,
                                                                Delete</button>
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
                <div class=" form-group col-md-12">
                    {{ $data->links() }}
                </div>

            </div>


        </div>
        {{-- //data here --}}
    </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
