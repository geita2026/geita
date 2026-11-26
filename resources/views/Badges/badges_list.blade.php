<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> EXHIBITION
                        BADGES</a></li>
                <li class="active">View All exhibitor Badges</li>
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
                <div class=" table-header form-group col-md-12 p-20">
                    <h4 class="panel-title text-bold text-center">
                        PRINTEND BADGES: {{ $PrintedBadges }} UNPRINTED BADGES:{{ $UnprintedBadges }}
                    </h4>
                </div>

                <div class=" form-group col-md-12">
                    @include('Badges.BadgeLayouts.BadgeFilter')
                </div>

                <form action="{{ route('print_badges.show', ['print_badges']) }}" method="POST">
                    @csrf
                    @method('GET')
                    <input type="hidden" name="badges" value="badges">
                    <table data-table-paginated class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center">
                                    <label class=" text-bold">CheckAll</label>
                                    <input type="checkbox" id="selectall" name="member_id[]" class="switchery">
                                </th>
                                <th class=" text-bold text-center">Member Photo</th>
                                <th class=" text-bold text-center">Member Name</th>
                                <th class=" text-bold text-center">Company Name</th>
                                <th class=" text-bold text-center">Member Group</th>
                                <th class=" text-bold text-center">View Badges</th>
                                <th class=" text-bold text-center">
                                    <button type="submit"
                                        class=" text-primary btn btn-default border-primary ml-20">Print Badges <i
                                            class=" icon-printer"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($badges as $item)
                                <tr>
                                    <td class=" text-center">
                                        <input type="checkbox" name="badge_id[]" class="switchery"
                                            value="{{ $item->member_id }}">
                                    </td>

                                    <td class=" text-center">
                                        <img src="{{ $item->member_image }}" height="50" width="70"
                                            class=" img-rounded">
                                    </td>
                                    <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}</td>
                                    <td>
                                        @php $a=0; @endphp
                                        @foreach ($associate as $data)
                                            @if ($data->companyID == $item->associateCompany)
                                                {{ $data->companyName }}
                                            @endif
                                            @php $a++; @endphp
                                        @endforeach

                                        @if ($a == 0)
                                            {{ $item->companyName }}
                                        @elseif ($item->associateCompany == null)
                                            {{ $item->companyName }}
                                        @endif

                                    </td>
                                    <td
                                        style="background-color: {{ $item->group_background_color }};color:{{ $item->text_color }}; text-align:center;">
                                        <b>{{ $item->group_name }}</b>
                                    </td>

                                    <td class=" text-center">

                                        @if ($item->badge_status == 'PRINTED')
                                            <div class=" label label-success p-10">{{ $item->badge_status }}</div>
                                        @elseif ($item->badge_status == 'UNPRINTED')
                                            <div class=" label label-warning p-10">{{ $item->badge_status }}</div>
                                        @endif



                                    </td>
                                    <td class=" text-center">
                                        <a href="{{ route('print_badges.show', ['print_badge' => $item->member_id]) }}"
                                            class=" text-primary btn btn-default border-primary">Print Badge <i
                                                class=" icon-printer2"></i></a>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach


                </form>
                </tbody>
                </table>
                {{-- <div class=" form-group text-right">
                    {{ $badges->withQueryString()->links() }}
                </div> --}}

            </div>
        </div>
    </div>
    <!-- /dashboard content -->

</x-app-layout>

<script type="text/javascript">
    $('#selectall').change(function() {
        if ($(this).prop('checked')) {
            $('input').prop('checked', true);
        } else {
            $('input').prop('checked', false);
        }
    });
    $('#selectall').trigger('change');
</script>

<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
