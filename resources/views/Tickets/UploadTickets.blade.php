<x-app-layout>
    <title>{{ env('APP_NAME') }} | POS</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> POS</a></li>
                <li class="active">View All POS</li>
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

                <span class=" text-danger">@error('posName') {{ $message }} @enderror</span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">REGISTERED  POS</h4>
                </div>
                <div class=" form-group">
                    <form action="{{ route('upload_tickets') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" form-group">
                            <input type="file" name="ticketFile" class=" form-control">
                            <span class=" text-danger">@error('ticketFile') {{ $message }} @enderror</span>
                        </div>
                        <div class=" form-group text-right">
                            <button type="submit" class=" btn btn-primary">Upload Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>



