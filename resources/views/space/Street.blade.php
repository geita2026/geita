<x-app-layout>
    <title>{{ env('APP_NAME') }} | STREETs</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>EVENT STREETS</a></li>
                <li class="active">View All other STREETs</li>
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

                {{-- errors --}}
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class=" text-danger">
                            <li>{{ $error }}</li>
                        </span>
                    @endforeach
                @endif
                {{-- end errors --}}

                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#STREET">Add
                        Street <i class=" icon-add-to-list"></i></button>
                </div>
                <div class=" form-group col-md-12">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                <th colspan="3" class=" text-center text-bold">MWL. NYERERE INTERNATIONAL FAIR GROUND STREETS</th>
                            </tr>
                            <tr>
                                <th class="text-bold text-center">Street Name</th>
                                <th class="text-bold text-center">Street Descriptions</th>
                                <th class="text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php$i=1;@endphp
                            @foreach ($streets as $item)
                                <tr>
                                    <td class=" ">{{ $item->streetName }}</td>
                                    <td>{{ $item->streetDescription }}</td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a data-toggle="modal" data-target="#edit{{ $item->streetId }}"
                                                            class=" text-primary"><i class="icon-pencil7"></i> Edit
                                                            Street</a>
                                                    </li>
                                                    <li>
                                                        <a data-toggle="modal"
                                                            data-target="#delete{{ $item->streetId }}"
                                                            class="text-danger"> <i class=" icon-bin"></i> Delete
                                                            Street</a>
                                                    </li>

                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                    {{-- edit STREET modal --}}
                                    <div id="edit{{ $item->streetId }}" class="modal fade" aria-modal="true"
                                        data-backdrop="static">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">EDIT STREET DETAILS.</h5>
                                                </div>
                                                <form
                                                    action="{{ route('streets.update', ['street' => $item->streetId]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class=" row">
                                                                <div class=" form-group col-md-12">
                                                                    <label>Street Name<i
                                                                            class=" text-danger">*</i></label>
                                                                    <input type="text" name="streetName"
                                                                        class=" form-control" placeholder=" Street Name"
                                                                        value="{{ $item->streetName }}" required>
                                                                </div>

                                                                <div class=" form-group col-md-12">
                                                                    <label>Street Description</label>
                                                                    <textarea class="form-control" name="streetDescription" placeholder="street description">{{ $item->STREETDescription }}</textarea>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">Close</button>
                                                        <button type="submit"
                                                            class="btn btn-primary btn-ladda btn-ladda-spinner ">Save
                                                            changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- /edit role modal -->
                                    {{-- delete modal --}}
                                    <div id="delete{{ $item->streetId }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete
                                                        <b><q>{{ $item->streetName }}</q></b> STREET ?
                                                    </h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('streets.destroy', ['street' => $item->streetId]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-default">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-danger btn-ladda btn-ladda-spinner ml-20"
                                                                data-style="slide-down">Yes, Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- delete modal --}}
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
            {{--  end here  --}}

        </div>
    </div>
    <!-- /dashboard content -->

    {{-- add role modal --}}
    <div id="STREET" class="modal fade" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title text-center text-bold">ADD NEW STREET</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('streets.store') }}"  method="POST">
                        @csrf

                        <div class=" row">
                            <div class=" form-group col-md-12">
                                <label>Street Name<i class=" text-danger">*</i></label>
                                <input type="text" name="streetName" class=" form-control"
                                    placeholder=" Street Name" value="{{ old('streetName') }}">
                                <span class=" text-danger text-error streetName_error"></span>
                            </div>
                            <div class=" form-group col-md-12">
                                <label>Street Description</label>
                                <textarea class="form-control" name="streetDescription" placeholder="Street description"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                            class="ladda-label">Register Street</span></button>
                </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    $(document).ready(function() {
        var form = '#add_STREET';
        $(form).on('submit', function(event) {
            event.preventDefault();
            var url = $(this).attr('data-action');
            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $('#add_STREET').find('span.text-error').text('');
                },
                success: function(response) {
                    if (response.status == 0) {
                        $.each(response.error, function(prefix, val) {
                            $(form).find('span.' + prefix + '_error').text(val[0]);
                        });
                    }
                    if (response.status == 1) {
                        $(document).ready(function() {
                            new PNotify({
                                title: 'Congratulations!',
                                text: response.success,
                                icon: 'icon-info3',
                                type: 'success'
                            });
                        });
                        $('#STREET').modal('toggle');
                        document.location.reload(); // reload page
                    }

                    if (response.status == 2) {
                        $(document).ready(function() {
                            new PNotify({
                                title: 'Congratulations!',
                                text: response.fail,
                                icon: 'icon-info3',
                                type: 'danger'
                            });
                        });
                        $('#STREET').modal('toggle'); // reload page
                    }


                }
            });
        });
    });
</script>
