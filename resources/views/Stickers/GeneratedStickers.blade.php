<x-app-layout>
    <title>{{ env('APP_NAME') }} | Stickers</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home
                    </a></li>
                <li class="active">View All stickers</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">

            <div class=" panel-body table-responsive">

                {{-- <div class=" row form-group col-md-12">
                    @can('Allocate Stickers')
                        <div class="form-group col-md-6 text-right">
                            <a data-toggle="modal" data-target="#AddFreeStickers"
                                class=" btn btn-default border-primary text-primary"> Free Sticker <i
                                    class=" icon-plus2"></i></a>
                        </div>
                    @endcan
                </div> --}}

                <div class=" form-group col-md-12">
                    <form action="{{ route('stickers.show', ['sticker' => session('company_id')]) }}" method="POST">
                        @method('GET')
                        @csrf
                        <div class=" col-md-8">
                            <label>Select type of Sticker you have paid</label>
                            <select name="serviceId" class=" form-control" onchange="this.form.submit()">
                                <option value="{{ $service->serviceId ?? '' }}"> {{ $service->serviceName ?? '' }} -
                                    {{ number_format($service->serviceCost + $service->serviceCost * 0.18) }}
                                    {{ $service->type_of_currency }}</option>
                                @foreach ($services as $serv)
                                    <option value="{{ $serv->serviceID }}">{{ $serv->serviceName }} -
                                        @php $serviceCost = intval($serv->serviceCost + $serv->serviceCost * 0.18) @endphp {{ number_format($serviceCost) }} {{ $serv->type_of_currency }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @can('allocate-badges')
                            <div class=" col-md-2">
                                <br>
                                <a data-toggle="modal" data-target="#stickerAllocation" class=" btn btn-primary">Provission
                                    Sticker</a>
                            </div>
                        @endcan
                    </form>
                </div>

                <div id="stickerAllocation" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title">Note Changes to sticker Allocations</h5>
                            </div>
                            <form
                                action="{{ route('allocate_stickers.update', ['allocate_sticker' => session('allocationId')]) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">

                                    @foreach ($adviceNote as $value)
                                        <div class=" form-group">
                                            <label>Sponsorship Benefits
                                                [{{ $value->advice_note_id }}]</label>
                                            <input type="hidden" name="advice_note_id"
                                                value="{{ $value->advice_note_id }}">
                                            <textarea class="form-control" name="reasons" readonly>{{ $value->advice_note_description }}</textarea>
                                        </div>
                                    @endforeach
                                    <div class="form-group">
                                        <label>Sponsorship stickers Quantity<i class=" text-danger">*</i></label>
                                        <input type="number" name="provided_stickers" class=" form-control"
                                            placeholder="Enter Allocation Number"
                                            value="{{ $stickerAllocation->provided_stickers ?? 0 }}">
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                {{-- ************* FREE STICKERS ************ --}}
                {{-- <div class=" form-group col-md-12">
                    <form action="{{ route('stickers.show',['sticker'=>session('company_id')]) }}">
                        <div class=" col-md-8">
                            <select name="serviceId" class=" bootstrap-select form-control">
                                @foreach ($services as $item)
                                    <option value="{{ $item->serviceID }}">
                                        {{ $item->serviceName }} [ {{ number_format($item->serviceCost + 0.18 * $item->serviceCost, 2) }}
                                        {{ $item->type_of_currency }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" col-md-4">
                            <button class=" btn bg-brown btn-ladda btn-ladda-spinner">Fetch Booking Details</button>
                        </div>

                    </form>
                </div> --}}


                <div class=" col-md-12 row form-group">
                    <div class=" col-md-6 p-10">
                        @php
                            if ($freeStickers == null) {
                                $freeStickers = 0;
                            } else {
                                $freeStickers = $freeStickers;
                            }

                            $TotalStickes = $TotalStickes + $freeStickers;

                        @endphp
                        {{-- <b class=" text-uppercase">{{ $service->serviceName }}: </b> --}}
                        <b class="lable label-success p-10">AVAILABLE:
                            {{ $AvailableStickes = $TotalStickes - $UsedStickes ?? 0 }}</b>
                        <b class="lable label-warning p-10">USED: {{ $UsedStickes ?? 0 }}</b>
                        <b class=" lable label-primary p-10">TOTAL: {{ $TotalStickes ?? 0 }}</b>
                    </div>
                    <div class=" col-md-6">
                        @if ($accountBalance > 0)
                            <div class=" form-group col-md-12">
                                <p class=" alert alert-warning">You have unpaid invoice in your Balance, Pay for it to
                                    get slots for stickers
                                    <br>
                                    @foreach ($invoices as $inv)
                                        <a href="{{ route('adviceNotes.show', ['adviceNote' => $inv->advice_note_id]) }}"
                                            class=" btn btn-default text-primary text-bold">{{ $inv->advice_note_id }}
                                            ({{ number_format($inv->balance, 2) }} {{ $inv->currency }})
                                        </a>
                                    @endforeach
                                </p>
                            </div>
                        @elseif ($AvailableStickes > 0 || $allowed > 0)
                            <div class="text-right form-group col-md-12">
                                <button type="submit" class=" btn btn-default border-primary text-primary"
                                    data-toggle="modal" data-target="#Stickers">New Sticker <i
                                        class=" icon-plus2"></i></button>
                            </div>
                        @elseif ($TotalStickes == 0)
                            <div class=" form-group col-md-12">
                                <p class=" alert alert-warning">Ooops! Sory you haven't booked
                                    <b>{{ $service->serviceName ?? '' }}
                                        [ @php echo intval($service->serviceCost + $service->serviceCost * 0.18) @endphp
                                        {{ $service->type_of_currency }} ]</b> sticker, please
                                    <a href="{{ route('book-services.index') }}">Click here</a> to Book stickers</a>
                                    and pay
                                    for it to get your stickers.
                                </p>
                            </div>
                        @elseif ($AvailableStickes == 0)
                            <div class=" form-group col-md-12">
                                <p class=" alert alert-warning">Ooops! Your sticker have been used up, please <a
                                        href="{{ route('book-services.index') }}">Click here</a> to Book stickers</a>
                                    and pay for
                                    it
                                    to get your sticker slots.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>


                <div class=" col-md-12 form-group">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                <th colspan="6" class="table-header">
                                    <h4 class="text-uppercase text-center"><b
                                            class=" text-primary">{{ $companyName->companyName }}'S</b>
                                        {{ $service->serviceName ?? '' }} STICKERS [ @php $cost=intval($service->serviceCost + ($service->serviceCost * 0.18)) @endphp
                                        {{ number_format($cost) }}
                                        {{ $service->type_of_currency }} ]</h4>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">PLATE NUMBER</th>
                                <th class=" text-bold text-center"> STICKER COST</th>
                                <th class=" text-bold text-center">STICKER TITLE</th>
                                <th class=" text-bold text-center">STICKER STATUS</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($stickers as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td class=" text-uppercase"> {{ $item->plate_number }} </td>
                                    <td class="text-center">
                                        {{ number_format($item->serviceCost + 0.18 * $item->serviceCost, 2) }}
                                        {{ $item->type_of_currency }}</td>

                                    <td class=" text-center"><b class="label label-block p-20"
                                            style="background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};"
                                            class=" p-20">{{ $item->serviceName }}</b></td>
                                    <td class=" text-center">
                                        @if ($item->sticker_status == 0)
                                            <b class="text-center label label-warning p-10">IN PROCESS</b>
                                        @elseif ($item->sticker_status == 1)
                                            <b class="  text-center label label-success p-10">PRINTED</b>
                                        @elseif ($item->sticker_status == 2)
                                            <b class="  text-center label bg-slate text-bold p-10">Blocked <i
                                                    class=" icon-blocked"></i></b>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">

                                                    @if ($item->sticker_status != 2)
                                                        @if (Auth::user()->account_type == 2 && $item->sticker_status == 1)
                                                        @else
                                                            @can('Update Stickers')
                                                                <li>
                                                                    <a data-toggle="modal"
                                                                        data-target="#EditSticker{{ $item->sticker_id }}"
                                                                        class="text-primary">Edit Sticker <i
                                                                            class=" icon-pencil"></i></a>
                                                                </li>
                                                            @endcan
                                                            @can('Delete Stickers')
                                                                <li>
                                                                    <a data-toggle="modal"
                                                                        data-target="#DeleteSticker{{ $item->sticker_id }}"
                                                                        class="text-danger"> Delete Sticker <i
                                                                            class=" icon-bin"></i></a>
                                                                </li>
                                                            @endcan
                                                        @endif
                                                        @can('Block Stickers')
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#Block{{ $item->sticker_id }}"
                                                                    class=" text-slate">Block Sticker <i
                                                                        class="icon-spam"></i></a>
                                                            </li>
                                                        @endcan
                                                    @else
                                                        <li>
                                                            {{-- <a  data-toggle="modal" data-target="#DeleteSticker{{ $item->sticker_id }}"  class="text-danger"> Delete Sticker <i class=" icon-bin"></i></a> --}}
                                                            <a data-toggle="modal"
                                                                data-target="#UnBlock{{ $item->sticker_id }}"
                                                                class=" text-success text-bold"> UnBlock Sticker <i
                                                                    class=" icon-user-check"></i></a>
                                                        </li>
                                                    @endif

                                                </ul>
                                            </li>
                                        </ul>
                                    </td>

                                    {{-- edit  modal --}}
                                    <div id="EditSticker{{ $item->sticker_id }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">

                                                    <h5 class="modal-title text-uppercase text-center">Edit
                                                        <b>{{ $item->serviceName }}</b> Sticker For this Exhibitor
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('stickers.update', ['sticker' => $item->sticker_id]) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class=" row form-group">
                                                            <div class=" form-group col-md-6">
                                                                <label>Car Plate Number<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="plate_number"
                                                                    class=" form-control"
                                                                    placeholder=" Enter car plate number"
                                                                    value="{{ $item->plate_number }}">
                                                                <span class="text-danger plate_number_error"></span>
                                                            </div>
                                                            {{-- <div class=" form-group col-md-6">
                                                                 <label>Imegency Car Plate Number</label>
                                                                 <input type="text" name="imegency_plate_number" class=" form-control" placeholder=" Enter Imegency Car Plate number" value="{{ $item->imegency_plate_number }}">
                                                                 <span class="text-danger imegency_plate_number_error"></span>
                                                              </div> --}}
                                                            <div class=" form-group col-md-6">
                                                                <label>Select Sticker Design Group<i
                                                                        class=" text-danger">*</i></label>
                                                                <select name="design_group" class=" form-control"
                                                                    data-live-search="true" data-width="100%"
                                                                    @if (Auth::user()->account_type == 2) disabled @endif>
                                                                    <option value="{{ $item->design_group }}">
                                                                        {{ $item->group_name }}</option>
                                                                    @foreach ($groups as $value)
                                                                        <option value="{{ $value->group_id }}">
                                                                            {{ $value->group_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-danger design_group_error"></span>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit"
                                                        class="btn btn-primary  btn-ladda btn-ladda-spinner">Save
                                                        Changes</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit modal -->

                                    {{-- delete modal --}}
                                    <div id="DeleteSticker{{ $item->sticker_id }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">

                                                    <h5 class="modal-title text-small">Are you sure you whant to delete
                                                        <b class=" text-uppercase">{{ $item->serviceName }}</b>
                                                        Sticker for this exhibitor ?
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('stickers.destroy', ['sticker' => $item->sticker_id]) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="member_image"
                                                            value="{{ $item->member_image }}">
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


                                    {{-- Block modal --}}
                                    <div id="Block{{ $item->sticker_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-slate">
                                                    <h5 class="modal-title text-small">Are you sure you whant to Block
                                                        <b class=" text-uppercase">{{ $item->serviceName }}</b>
                                                        sticker for this exhibitor ?
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('stickers.update', ['sticker' => $item->sticker_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="source" value="1">
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn bg-slate ml-20">Yes,
                                                                Block Sticker</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End block modal --}}

                                    {{-- Block modal --}}
                                    <div id="UnBlock{{ $item->sticker_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success">
                                                    <h5 class="modal-title text-small">Are you sure you whant to
                                                        UnBlock <b
                                                            class=" text-uppercase">{{ $item->serviceName }}</b>
                                                        Sticker for this exhibitor ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('stickers.update', ['sticker' => $item->sticker_id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="source" value="2">
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-success ml-20">Yes,
                                                                UnBlock Sticker</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End block modal --}}

                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form data-action="{{ route('stickers.store') }}" method="POST" id="GenerateSticker"
        enctype="multipart/form-data">
        @csrf
        <div id="Stickers" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <h5 class="modal-title text-uppercase text-center text-bold" style="font-size:1.2vmax">CREATE
                            new Sticker.</h5>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                        <input type="hidden" name="reference_sticker" value="{{ session('service_id') }}">
                        <input type="hidden" name="event_id" value="{{ session('event_id') }}">
                        <div class=" row form-group">
                            <div class=" form-group col-md-6">
                                <label>Car Plate Number<i class=" text-danger">*</i></label>
                                <input type="text" name="plate_number" class=" form-control"
                                    placeholder="Enter the car plate number" value="{{ old('plate_number') }}">
                                <span class="text-danger plate_number_error"></span>
                            </div>
                            {{-- <div class=" form-group col-md-6">
                                     <label>Imegency Car Plate Number</label>
                                     <input type="text" name="imegency_plate_number" class=" form-control" placeholder=" Enter Imegency Car Plate number" value="{{ old('imegency_plate_number') }}">
                                     <span class="text-danger imegency_plate_number_error"></span>
                                  </div> --}}
                            <div class=" form-group col-md-6">
                                <label>Select Sticker Design Group<i class=" text-danger">*</i></label>
                                <select name="design_group" class="bootstrap-select" data-live-search="true"
                                    data-width="100%" @if (Auth::user()->account_type == 2) readonly @endif>
                                    @foreach ($groups as $item)
                                        <option value="{{ $item->group_id }}">{{ $item->group_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger design_group_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default border-warning"
                            data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Generate Sticker</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>

<script>
    $(document).ready(function() {
        var form = '#GenerateSticker';
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
                    $('#GenerateSticker').find('span.text-danger').text('');
                },
                success: function(response) {
                    if (response.status == 0) {
                        $.each(response.error, function(prefix, val) {
                            $(form).find('span.' + prefix + '_error').text(val[0]);
                        });
                    }

                    // success
                    if (response.status == 1) {
                        $(document).ready(function() {
                            new PNotify({
                                title: 'Congratulations!',
                                text: response.success,
                                icon: 'icon-info3',
                                type: 'success'
                            });

                        });
                        $('#Stickers').modal('toggle');

                        setTimeout(() => {
                            document.location.reload();
                        }, 1000);
                    }
                    //    failed
                    if (response.status == 2) {
                        $(document).ready(function() {
                            new PNotify({
                                title: 'Ooops!',
                                text: response.fail,
                                icon: 'icon-warning',
                                type: 'error'
                            });

                        });
                        $('#Stickers').modal('toggle');
                    }


                }



            });
        });

    });
</script>
