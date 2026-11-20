<x-app-layout>
    <!-- dashboard -->
    <div class="panel panel-flat" style="border:1px solid rgb(171, 171, 173);">
        <div class="panel-heading" style=" background-color:rgba(211, 206, 206, 0.836); border-bottom:1px solid rgb(171, 171, 173);">
            <h6 class="panel-title"><b><a href="{{ route('settings.index') }}"><i class=" icon-home"></i> Account Setting Dashboard</a></b></h6>
        </div>
        <div class="container-fluid p-20">
            {{-- data here --}}
            <div class=" panel-body">
                <h1>Dashboard Info to appear here</h1>

            </div>
            {{-- //data here --}}
        </div>
    </div>
<!-- /dashboard content -->
</x-app-layout>
