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
                <form action="{{ route('ground_visitors.index') }}" method="POST">
                    @csrf
                    @method('GET')
                    <div class=" form-group">
                        <input type="date" name="date" class=" form-control">
                    </div>
                    <button type="submit" class=" btn btn-primary">Fetch Data <i
                            class=" icon-arrow-right13"></i></button>
                </form>

                <h2 class=" text-center"> SABASABA FAIR GROUND VISITORS AND EXHIBITOR COUNT</h2>
                <div class=" row">
                    <div class=" p-20 bg-brown-300 col-md-4 rounded form-group">
                        <b>Adult: </b> {{ number_format($adult) }}
                    </div>
                    <div class=" p-20 bg-success-800 col-md-4 rounded form-group">
                        <b>Child: </b> {{ number_format($child) }}
                    </div>
                    <div class=" p-20 bg-warning-800 col-md-4 rounded form-group">
                        <b>Car: </b> {{ number_format($cars) }}
                    </div>
                    <div class=" p-20 bg-slate-400 col-md-4 rounded form-group">
                        <b>Badges: </b> {{ number_format($badges) }}
                    </div>
                    <div class=" p-20 bg-slate-800 col-md-4 rounded form-group">
                        <b>Total Visitors: </b> {{ number_format($adult + $child + $badges) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
