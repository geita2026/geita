<x-app-layout>
    <title>{{ env('APP_NAME') }} | Services</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>EVENT SERVICES</a></li>
                <li class="active">View All other services</li>
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
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#service">Add
                        Service <i class=" icon-add-to-list"></i></button>
                </div>
                <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                    <thead>
                        <tr>
                            <th class="text-bold text-center">GFS CODE</th>
                            <th class="text-bold text-center">SERVICE NAME</th>
                            <th class="text-bold text-center">SERVICE COST</th>
                            <th class="text-bold text-center">SERVICE EVENT</th>
                            <th class="text-bold text-center">SERVICE PRIOLITY</th>
                            <th class=" text-bold text-center">SERVICE PROVIDER</th>
                            <th class="text-bold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php$i=1;@endphp
                        @foreach ($services as $item)
                            <tr>
                                <td class=" text-center">{{ $item->gfs_code }}</td>
                                <td>
                                    {{ $item->serviceName }}
                                    @if ($item->is_printable == 1)
                                        <i class=" icon-printer text-small text-primary"></i>
                                    @endif
                                </td>
                                <td class=" text-right">{{ number_format($item->serviceCost, 2) }}
                                    {{ $item->type_of_currency }}</td>
                                <td>{{ $item->event_name }}</td>
                                <td class=" text-center">{{ $item->service_priolity }}</td>
                                <td class=" text-center">{{ $item->service_type }}</td>


                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a data-toggle="modal" data-target="#edit{{ $item->serviceID }}"
                                                        class=" text-primary"><i class="icon-pencil7"></i> Edit
                                                        Service</a>
                                                </li>
                                                <li>
                                                    <a data-toggle="modal" data-target="#delete{{ $item->serviceID }}"
                                                        class="text-danger"> <i class=" icon-bin"></i> Delete
                                                        Service</a>
                                                </li>

                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                {{-- edit service modal --}}
                                <div id="edit{{ $item->serviceID }}" class="modal fade" aria-modal="true"
                                    data-backdrop="static">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">EDIT SERVICE DETAILS.</h5>
                                            </div>
                                            <form
                                                action="{{ route('additional_services.update', ['additional_service' => $item->serviceID]) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>Service Name<i class=" text-danger">*</i></label>
                                                                <input type="text" name="service_name"
                                                                    class=" form-control" placeholder=" Service Name"
                                                                    value="{{ $item->serviceName }}" required>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Service Cost<i class=" text-danger">*</i></label>
                                                                <input type="text" name="service_cost"
                                                                    class=" form-control"
                                                                    value="{{ $item->serviceCost }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="row">

                                                            <div class=" form-group col-md-6">
                                                                <label>Type Of Currency <i
                                                                        class=" text-danger">*</i></label>
                                                                <select name="type_of_currency" class=" form-control "
                                                                    required>
                                                                    <option value="{{ $item->type_of_currency }}">
                                                                        {{ $item->type_of_currency }}</option>
                                                                    <option value="TZS">TZS</option>
                                                                    <option value="USD">USD</option>
                                                                </select>
                                                            </div>

                                                            <div class=" form-group col-md-6">
                                                                <label> Service Event<i
                                                                        class=" text-danger">*</i></label>
                                                                <select name="service_event" class=" form-control "
                                                                    placehoder="Select Event" required>
                                                                    <option value="{{ $item->service_event }}">
                                                                        {{ $item->event_name }}</option>
                                                                    @foreach ($events as $event)
                                                                        <option value="{{ $event->event_id }}">
                                                                            {{ $event->event_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-danger"> @error('event_name')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>

                                                        </div>

                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>Service Provider</label>
                                                                <select name="service_type" class=" form-control"
                                                                    required>
                                                                    <option value="{{ $item->service_type }}">
                                                                        {{ $item->service_type }}</option>
                                                                    <option value="TanTrade">TanTrade</option>
                                                                    <option value="Exhibitor">Exhibitor</option>
                                                                </select>
                                                            </div>

                                                            <div class=" form-group col-md-6">
                                                                <label>Service Priolity</label>
                                                                <select name="service_priolity" class="form-control"
                                                                    required>
                                                                    <option value="{{ $item->service_priolity }}">
                                                                        {{ $item->service_priolity }}</option>
                                                                    <option value="Mandatory">Mandatory Service
                                                                    </option>
                                                                    <option value="Optional">Optional Service</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class=" row form-group">
                                                            <div class="form-group col-md-6">
                                                                <label>Service GFS code<i
                                                                        class="text-danger">*</i></label>
                                                                <select name="gfs_code" class=" form-control "
                                                                    required>
                                                                    <option value="{{ $item->gfs_code }}">
                                                                        {{ $item->gfs_code }}</option>
                                                                    @foreach ($income as $data)
                                                                        <option value="{{ $data->gfs_code }}">
                                                                            {{ $data->income_name }} -
                                                                            {{ $data->gfs_code }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class=" text-danger">
                                                                    @error('gfs_code')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>

                                                            <div class=" form-group col-md-6">
                                                                <label>Is Printable Service (eg car stikers)<i
                                                                        class="text-danger">*</i></label>
                                                                <select name="is_printable" class=" form-control">
                                                                    @if ($item->is_printable == 0)
                                                                        <option value="0">No</option>
                                                                        <option value="1">Yes</option>
                                                                    @else
                                                                        <option value="1">Yes</option>
                                                                        <option value="0">No</option>
                                                                    @endif
                                                                </select>
                                                                <span
                                                                    class=" text-danger text-error is_printable_error"></span>
                                                            </div>

                                                        </div>
                                                        <div class=" row form-group">

                                                            <div class=" form-group col-md-6">
                                                                <label>Allow Self Booking<i
                                                                        class="text-danger">*</i></label>
                                                                <select name="self_booking" class=" form-control">
                                                                    @if ($item->self_booking == 0)
                                                                        <option value="0">No</option>
                                                                        <option value="1">Yes</option>
                                                                    @else
                                                                        <option value="1">Yes</option>
                                                                        <option value="0">No</option>
                                                                    @endif
                                                                </select>
                                                                <span
                                                                    class=" text-danger text-error is_printable_error"></span>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Service Booking Relate to Space Size ?<i
                                                                        class="text-danger">*</i></label>
                                                                <select name="relateToSpaceSize"
                                                                    class=" form-control">
                                                                    @if ($item->relateToSpaceSize == 0)
                                                                        <option value="0">No</option>
                                                                        <option value="1">Yes</option>
                                                                    @else
                                                                        <option value="1">Yes</option>
                                                                        <option value="0">No</option>
                                                                    @endif
                                                                </select>
                                                                <span
                                                                    class=" text-danger text-error is_printable_error"></span>
                                                            </div>

                                                        </div>
                                                        <div class=" form-group col-md-12">
                                                            <label>Service Description</label>
                                                            <textarea class="form-control" name="service_description" placeholder="Service description">{{ $item->serviceDescription }}</textarea>
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
                                <div id="delete{{ $item->serviceID }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete
                                                    <b><q>{{ $item->serviceName }}</q></b> Service ?
                                                </h5>
                                            </div>

                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('additional_services.destroy', ['additional_service' => $item->serviceID]) }}"
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
            {{--  end here  --}}

        </div>
    </div>
    <!-- /dashboard content -->

    {{-- add role modal --}}
    <div id="service" class="modal fade" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title text-center text-bold">ADD ADDTIONAL SERVICE DETAILS</h5>
                </div>
                <div class="modal-body">
                    <form data-action="{{ route('additional_services.store') }}" id="add_service" method="POST">
                        @csrf
                        <div class="row">
                            <div class=" row">
                                <div class=" form-group col-md-6">
                                    <label>Service Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="service_name" class=" form-control"
                                        placeholder=" Service Name" value="{{ old('service_name') }}">
                                    <span class=" text-danger text-error service_name_error"></span>
                                </div>
                                <div class=" form-group col-md-6">
                                    <label>Service Cost<i class=" text-danger">*</i></label>
                                    <input type="text" name="service_cost" class=" form-control"
                                        placeholder=" Service Cost" value="{{ old('service_cost') }}">
                                    <span class=" text-danger text-error service_cost_error"></span>

                                </div>
                            </div>
                            <div class=" row">
                                <div class=" form-group col-md-6">
                                    <label> Type of currence <i class=" text-danger">*</i></label>
                                    <select name="type_of_currency" class=" form-control">
                                        {{-- <optgroup title="Sevice Priolities"> --}}
                                        <option value="">Select Type of currency . . . . .</option>
                                        <option value="TZS">TZS</option>
                                        <option value="USD">USD</option>
                                        {{-- </optgroup> --}}
                                    </select>
                                    <span class=" text-danger text-error type_of_currency_error"></span>
                                </div>

                                <div class=" form-group col-md-6">
                                    <label> Service Event<i class=" text-danger">*</i></label>
                                    <select name="service_event" class="bootstrap-select " data-live-search="true"
                                        data-width="100%" placehoder="Select Event">
                                        <option value=""> - - - Select Events - - - </option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class=" text-danger text-error service_event_error"></span>
                                </div>
                            </div>

                            <div class=" row">
                                <div class=" form-group col-md-6">
                                    <label>Service Provider<i class="text-danger">*</i></label>
                                    <select name="service_type" class=" form-control">
                                        <option value="TanTrade">Tan Trade</option>
                                        <option value="Exhibitor">Exhibitor</option>
                                    </select>
                                    <span class=" text-danger text-error service_type_error"></span>
                                </div>

                                <div class=" form-group col-md-6">
                                    <label>Service Priolity<i class="text-danger">*</i></label>
                                    <select name="service_priolity" class=" form-control">
                                        <option value="Mandatory">Mandatory Service</option>
                                        <option value="Optional">Optional Service</option>
                                    </select>
                                    <span class=" text-danger text-error service_priolity_error"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Service GFS code<i class="text-danger">*</i></label>
                                    <select name="gfs_code" class=" form-control ">
                                        <option value="">- - - Select GFS Code - - - </option>
                                        @foreach ($income as $item)
                                            <option value="{{ $item->gfs_code }}">{{ $item->income_name }} -
                                                {{ $item->gfs_code }}</option>
                                        @endforeach
                                    </select>
                                    <span class=" text-danger text-error gfs_code_error"></span>
                                </div>
                                <div class=" form-group col-md-6">
                                    <label>Is Printable Service (eg car stikers)<i class="text-danger">*</i></label>
                                    <select name="is_printable" class=" form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <span class=" text-danger text-error is_printable_error"></span>
                                </div>
                            </div>
                            <div class=" row">
                                <div class=" form-group col-md-6">
                                    <label>Allow Self Booking<i
                                            class="text-danger">*</i></label>
                                    <select name="self_booking" class=" form-control">
                                        @if ($item->self_booking == 0)
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        @else
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        @endif
                                    </select>
                                    <span
                                        class=" text-danger text-error is_printable_error"></span>
                                </div>
                                <div class=" form-group col-md-6">
                                    <label>Service Booking Relate to Space Size ?<i class="text-danger">*</i></label>
                                    <select name="relateToSpaceSize" class=" form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <span class=" text-danger text-error is_printable_error"></span>
                                </div>

                            </div>
                            <div class=" form-group col-md-12">
                                <label>Service Description</label>
                                <textarea class="form-control" name="service_description" placeholder="Service description">Service description Here</textarea>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                            class="ladda-label">Register Service</span></button>
                </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    $(document).ready(function() {
        var form = '#add_service';
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
                    $('#add_service').find('span.text-error').text('');
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
                        $('#service').modal('toggle');
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
                        $('#service').modal('toggle'); // reload page
                    }


                }
            });
        });
    });
</script>
