<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permit</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> PERMISSIONS</a>
                </li>
                <li class="active">View Permit</li>
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
            <div class=" panel-body">

                @foreach ($permit as $item)
                    <div class=" table-header form-group">
                        <h3 class=" text-center text-primary">{{ $item->companyName }}'S @if ($item->permit_type == 1)
                                ENTRANCE
                            @elseif($item->permit_type == 0)
                                EXIT
                            @endif PERMIT</h3>
                    </div>


                    <table style="width: 100%;border:1px solid rgb(199, 197, 197);padding:5%">
                        @include('reports.reports_layouts.report_header')
                        <tr>
                            <th colspan="3">
                                <h3 style="text-align: center; border-bottom:2px solid rgb(202, 200, 200);">
                                    48<sup>th</sup> Dar es salaam International Trade Fair (DITF) Entrance Permit.
                                </h3>
                            </th>
                        </tr>
                        <tbody>
                            <tr>
                                <td colspan="3">
                                    <h4>This is to Notify that <b
                                            style="border-bottom: 1px dashed black;">{{ $item->companyName }}</b> with
                                        Service Registration Number (SRN) <b style="border-bottom: 1px dashed black;">{{ $item->registrationID }}</b>
                                        has participated in Dar es salaam International Trade Fair.
                                    </h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <br><br>

                    <div class=" form-group col-md-12 row">
                        <div class=" panel">
                            <div class=" panel-heading text-center ">

                            </div>

                            <div class=" panel-body">
                                <h5><b>{{ $item->companyName }}</b> with SRN:
                                    <b>{{ $item->registrationID }}</b>,</h5>
                                <p>Has been issued entrance permit during 47<sup>th</sup> Dar es salaam International
                                    Trade Fair.</p>
                                <p>Product Allowed are as follows:-</p>
                                @foreach ($products as $item)
                                    <li>{{ $item->product_list }}</li>
                                @endforeach
                            </div>
                            <div class=" panel-footer text-center">
                                <b>{{ QrCode::size(80)->generate('SAMS|' . $item->permit_id) }}</b>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
