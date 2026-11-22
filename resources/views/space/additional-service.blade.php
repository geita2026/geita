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

                @can('CREATE_ADDITIONAL_SERVICE')
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#service"
                            aria-haspopup="dialog">
                            Add Service <i class="icon-add-to-list"></i>
                        </button>
                    </div>
                @endcan
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
                        @forelse ($services as $item)
                            @php
                                $provider = strcasecmp((string) $item->service_type, 'TanTrade') === 0
                                    ? 'Geita'
                                    : $item->service_type;
                            @endphp
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
                                <td class=" text-center">{{ $provider }}</td>


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
                                                                    @if ($provider && !in_array($provider, ['Geita', 'Exhibitor'], true))
                                                                        <option value="{{ $provider }}" selected>{{ $provider }}</option>
                                                                    @endif
                                                                    <option value="Geita" {{ $provider === 'Geita' ? 'selected' : '' }}>Geita</option>
                                                                    <option value="Exhibitor" {{ $provider === 'Exhibitor' ? 'selected' : '' }}>Exhibitor</option>
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
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No additional services registered.</td></tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
            {{--  end here  --}}

        </div>
    </div>
    <!-- /dashboard content -->

    @can('CREATE_ADDITIONAL_SERVICE')
        @php
            $serviceErrors = $errors->newService;
            $yesNo = ['0' => 'No', '1' => 'Yes'];
            $selectFields = [
                'type_of_currency' => ['Currency', ['TZS' => 'TZS', 'USD' => 'USD'], '', 'Select currency'],
                'service_event' => ['Exhibition event', $events->pluck('event_name', 'event_id'), '', 'Select an event'],
                'gfs_code' => [
                    'Service GFS code',
                    $income->mapWithKeys(fn ($code) => [$code->gfs_code => $code->gfs_code . ' - ' . $code->income_name]),
                    '',
                    'Select a GFS code',
                ],
                'service_type' => ['Service provider', ['Geita' => 'Geita', 'Exhibitor' => 'Exhibitor'], 'Geita'],
                'service_priolity' => ['Service priority', ['Mandatory' => 'Mandatory', 'Optional' => 'Optional'], 'Mandatory'],
                'is_printable' => ['Printable service', $yesNo, '0'],
                'self_booking' => ['Allow self booking', $yesNo, '0'],
                'relateToSpaceSize' => ['Charge based on space size', $yesNo, '1'],
            ];
        @endphp
        <div id="service" class="modal fade gememis-registration-modal" role="dialog" aria-modal="true" tabindex="-1"
            aria-labelledby="add-service-title" aria-describedby="add-service-intro">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="registration-header">
                        <button type="button" class="close registration-close" data-dismiss="modal"
                            aria-label="Close Add Service">&times;</button>
                        <p class="registration-brand">GeMEMIS</p>
                        <h2 id="add-service-title" class="modal-title">Add service</h2>
                        <p id="add-service-intro" class="registration-intro">
                            Set up a service for an exhibition event and assign its local GFS code.
                        </p>
                    </div>
                    <form action="{{ route('additional_services.store') }}" method="POST">
                        @csrf
                        <div class="registration-body">
                            <div class="registration-grid">
                                <div class="registration-field">
                                    <label for="new-service-name">Service name <span class="registration-required" aria-hidden="true">*</span></label>
                                    <input id="new-service-name" name="service_name" class="form-control"
                                        value="{{ old('service_name') }}" aria-describedby="new-service-name-error"
                                        @if ($serviceErrors->has('service_name')) aria-invalid="true" @endif required>
                                    <span id="new-service-name-error" class="registration-error" aria-live="polite">{{ $serviceErrors->first('service_name') }}</span>
                                </div>
                                <div class="registration-field">
                                    <label for="new-service-cost">Service cost <span class="registration-required" aria-hidden="true">*</span></label>
                                    <input id="new-service-cost" name="service_cost" type="number" step="any" class="form-control"
                                        value="{{ old('service_cost') }}" aria-describedby="new-service-cost-error"
                                        @if ($serviceErrors->has('service_cost')) aria-invalid="true" @endif required>
                                    <span id="new-service-cost-error" class="registration-error" aria-live="polite">{{ $serviceErrors->first('service_cost') }}</span>
                                </div>
                                @foreach ($selectFields as $field => $definition)
                                    @php
                                        [$label, $options, $default] = $definition;
                                        $fieldId = 'new-service-' . $field;
                                    @endphp
                                    <div class="registration-field">
                                        <label for="{{ $fieldId }}">{{ $label }} <span class="registration-required" aria-hidden="true">*</span></label>
                                        <select id="{{ $fieldId }}" name="{{ $field }}" class="form-control"
                                            aria-describedby="{{ $fieldId }}-error"
                                            @if ($serviceErrors->has($field)) aria-invalid="true" @endif required>
                                            @if (isset($definition[3]))
                                                <option value="">{{ $definition[3] }}</option>
                                            @endif
                                            @foreach ($options as $value => $name)
                                                <option value="{{ $value }}" {{ (string) old($field, $default) === (string) $value ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span id="{{ $fieldId }}-error" class="registration-error" aria-live="polite">{{ $serviceErrors->first($field) }}</span>
                                    </div>
                                @endforeach
                                <div class="registration-field registration-wide">
                                    <label for="new-service-description">Description (optional)</label>
                                    <textarea id="new-service-description" name="service_description" class="form-control"
                                        rows="2" maxlength="100" aria-describedby="new-service-description-error"
                                        @if ($serviceErrors->has('service_description')) aria-invalid="true" @endif>{{ old('service_description') }}</textarea>
                                    <span id="new-service-description-error" class="registration-error" aria-live="polite">{{ $serviceErrors->first('service_description') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="registration-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="registration-submit">Register service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($serviceErrors->any())
            <script>
                $(function () { $('#service').modal('show'); });
            </script>
        @endif
    @endcan
</x-app-layout>
