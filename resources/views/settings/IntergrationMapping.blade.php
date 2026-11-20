
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Intergration Mappings</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Intergrations</a></li>
                <li class="active">View All Intergration Mappings</li>
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
                <div class=" form-group col-md-12 table-header">
                    <h6 class="text-bold text-center">REGISTERED INTERGRATION MAPPINGS</h6>
                </div>

                @foreach ($intergrationMapping as $item)

                @endforeach

               <form action="{{ route('intergration_mapping.store') }}" method="POST">
                @csrf
                <div class=" form-group col-md-12 row">
                    <div class=" form-group col-md-12">
                        <label>Billing Service Code</label>
                        <input type="text" name="billing_service_code" placeholder="Enter registered Billing Service Code" class=" form-control" value="{{ $item->billing_service_code ?? old('billing_service_code') }}">
                    </div>
                    <div class=" form-group col-md-12">
                        <label>Billing Access Token</label>
                        <input type="text" name="billing_access_token" placeholder="Billing Access Token" class=" form-control" value="{{ $item->billing_access_token ?? old('billing_access_token') }}">
                    </div>

                    {{-- **********OTHER SETTINGS************ --}}

                    <div class=" form-group col-md-12">
                        <label>Billing Base URL</label>
                        <input type="text" name="billing_base_url" placeholder="Post Customer EndPoint" class=" form-control" value="{{ $item->billing_base_url ?? old('billing_base_url') }}">
                    </div>
                    <div class=" form-group col-md-12 text-right">
                        <button class=" btn btn-primary">Update Settings</button>
                    </div>

                </div>
               </form>


            </div>
        </div>
    </div>
</x-app-layout>

