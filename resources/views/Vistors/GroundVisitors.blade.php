<x-app-layout>
    <title>{{ env('APP_NAME') }} | Ground Visitors</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> VISITORS COUNT</a>
                </li>
                <li class="active">View ground visitors</li>
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
                <h2 class=" text-center"> ACTIVE EVENT STICKER AND BADGE COUNTS</h2>
                <div class=" row">
                    <div class=" p-20 bg-warning-800 col-md-4 rounded form-group">
                        <b>Car stickers: </b> {{ number_format($cars) }}
                    </div>
                    <div class=" p-20 bg-slate-400 col-md-4 rounded form-group">
                        <b>Badges: </b> {{ number_format($badges) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
