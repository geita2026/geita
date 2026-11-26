<x-app-layout>
    <title>{{ env('APP_NAME') }} | Stickers</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> EXHIBITION Stickers</a></li>
                <li class="active">View All exhibitor Stickers</li>
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

                {{-- Error or success messages --}}
                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <span class=" text-danger">{{ $error }} ,</span>
                @endforeach
                @endif
                {{-- //error messages --}}

                <div class=" row">
                    <div class=" form-group col-md-6">
                        <a href=""  class=" btn btn-default border-primary text-primary"><i class=" icon-arrow-left12"></i> Back </a>
                    </div>
                </div>

                <div class=" table-header form-group col-md-12 p-20">
                    <h4 class="panel-title text-bold text-center">
                        PRINTEND STICKERS: {{ $printedStickers }}  UNPRINTED STICKERS:{{ $UnprintedStickers }}
                    </h4>
                </div>


                <div class=" form-group col-md-12">
                    @include('Stickers.StickerFilter')
                </div>


                <form action="{{ route('print_stickers.edit',['print_stickers']) }}" method="POST">
                    @csrf
                    @method('GET')
                    <input type="hidden" name="source" value="2">

                <table data-table-paginated class="table datatable-save-state table-striped table-bordered  table-responsive">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">
                                <label class=" text-bold">CheckAll</label>
                                <input type="checkbox" id="selectall"  name="member_id[]" class="switchery">
                            </th>
                            <th class=" text-bold text-center">Company Name</th>
                            <th class=" text-bold text-center">Sticker Title</th>
                            <th class=" text-bold text-center">Plate No.</th>
                            <th class=" text-bold text-center">Sticker Status</th>
                            <th class=" text-bold text-center">
                                <button type="submit" class=" btn btn-default text-primary">Print Stickers <i class=" icon-printer"></i></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($stickers as  $item)
                        <tr>
                            <td class=" text-center">
                                <input type="checkbox" name="sticker_id[]" class="switchery" value="{{ $item->sticker_id }}">
                            </td>
                            <td>
                                {{ $item->companyName }}
                            </td>
                            <td  class=" text-center"><b class="label label-block p-20" style="background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};" class=" p-20">
                                {{ $item->serviceName }} - {{ number_format($item->serviceCost+(0.18*$item->serviceCost),2) }} {{ $item->type_of_currency }}</b>
                            </td>
                            <td class=" text-center">{{ $item->plate_number }}</td>
                            <td class=" text-center">
                                @if($item->sticker_status==1)
                                <div class=" label label-success p-10">PRINTED</div>
                                @else
                                <div class=" label label-warning p-10">IN PROCESS</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('print_stickers.edit',['print_sticker'=>$item->sticker_id]) }}" class=" btn btn-default text-primary">Print Sticker <i class=" icon-printer"></i></a>
                            </td>
                        </tr>
                        @php $i++; @endphp
                        @endforeach
                    </form>
                    </tbody>
                </table>
            </div>
            <div class=" form-group text-right">
                {{ $stickers->withQueryString()->links() }}
            </div>
        </div>
    </div>
    <!-- /dashboard content -->

</x-app-layout>

<script type="text/javascript">
    $('#selectall').change(function () {
        if ($(this).prop('checked')) {
        $('input').prop('checked', true);
        }
        else {
            $('input').prop('checked', false);
        }
    });
    $('#selectall').trigger('change');
    </script>

<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
