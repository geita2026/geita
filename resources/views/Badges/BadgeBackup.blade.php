<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> REMOTE BADGES</a></li>
                <li class="active">Remote Badges</li>
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

                <div class=" col-md-12">
                    <h1 class=" text-center text-bold">SABASABA VERIFIED DATA, BACKUP POINT.</h1>
                </div>
               
                <div class=" form-group col-md-12">
                    <h3>Total Remote Badges: <b>{{ $totaltBadges }}</b></h3>
                </div>

               <div class=" form-group col-md-12">
                <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                    <thead>
                        <tr>
                            <th class=" text-bold text-center">Member Photo</th>
                            <th class=" text-bold text-center">Member Name</th>
                            <th class=" text-bold text-center">Company Name</th>
                            <th class=" text-bold text-center">Member Group</th>
                            <th class=" text-bold text-center">Backup</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($badges as  $item)
                        <tr>
                            <td class=" text-center">
                                <img src="https://tradefair.tantrade.go.tz/public/Members/{{$item['member_image'] }}" height="50" width="70" class=" img-rounded">
                            </td>
                            <td>{{ $item['first_name'] }} {{ $item['middle_name'] }} {{ $item['last_name'] }}</td>
                            <td>{{ $item['companyName'] }}</td>
                            <td  style="background-color: {{ $item['group_background_color'] }};color:{{ $item['text_color'] }}; text-align:center;"><b>{{ $item['group_name'] }}</b></td>
                            <td class=" text-center">
                               <a href="{{ route('badges_backup.show',['badges_backup'=>$item['member_id']]) }}" class=" btn btn-primary">Backup Badge</a>
                            </td>
                        </tr>
                        @php $i++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
                {{-- {{ $badges->links() }} --}}


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
