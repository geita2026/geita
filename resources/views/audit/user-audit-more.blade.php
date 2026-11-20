<x-app-layout>
    <title>{{ env('APP_NAME') }} | Logs</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('audits.index') }}"><i class=" icon-gear position-left"></i>User-Audit</a></li>
            <li class="active">view-more</li>
        </ul>

        <ul class="breadcrumb-elements">
           @include('layouts.help')
        </ul>
    </div>
    </div>

    <!-- Content area -->

    <div class="content">
        <div class="panel panel-flat">
            <div class=" panel-body">
                <div class=" form-group">
                    <a href="{{ route('audits.index') }}" class=" btn btn-default text-bold text-primary"><i class=" icon-arrow-left22"></i> Back</a>
                </div>

                <div class=" form-group com-md-12">
                    <table class=" table table-bordered table-striped">

                        <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <th><b>USER_EVENT: </b></th>
                            <th>{{ $item->event }}</th>
                        </tr>
                        <tr>
                            <th><b>MODEL_AFFECTED: </b></th>
                            <th>{{ $item->auditable_type }}</th>
                        </tr>
                        <tr>
                            <th><b>OLD_VALUE: </b></th>
                            <th>{{ $item->old_values }}</th>
                        </tr>
                        <tr>
                            <th><b>NEW VALUE: </b></th>
                            <th>{{ $item->new_values }}</th>
                        </tr>
                        <tr>
                            <th><b>URL_USED: </b></th>
                            <th>{{ $item->url }}</th>
                        </tr>
                        <tr>
                            <th><b>IP_ADDRESS: </b></th>
                            <th>{{ $item->ip_address }}</th>
                            <tr>
                                <th><b>USER_AGENT: </b></th>
                                <th>{{ $item->user_agent }}</th>
                            </tr>
                            <tr>
                                <th><b>DATE_OF_ACTION: </b></th>
                                <th>{{ $item->created_at }}</th>
                            </tr>
                        </thead>
                       @endforeach
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- /dashboard content -->

</x-app-layout>

