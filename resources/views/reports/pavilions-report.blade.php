
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space-Reports</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
          @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i> Home</a></li>
           <li class="active">Dashboard</li>
       </ul>

       <ul class="breadcrumb-elements">
           @include('layouts.help')
       </ul>
   </div>
   </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body table-responsive">
    <div class=" form-group">
<a href="{{ route('reports.index') }}" class=" btn btn-primary"><i class=" icon-arrow-left12"></i> Back</a>
    </div>
   <h3 class=" text-bold text-primary">DAR ES SALAAM INTERNATIONAL TRADE FAIR GROUND REPORT</h3>

   {{-- pavilions in general reports --}}
   <table class=" table table-striped table-bordered bg-purple-300">
       <thead>
           <tr>
               <th colspan=" 4" class=" text-bold text-center"> PAVILION LIST AND THEIR SPECIFICATIONS.</th>
           </tr>
           <tr class=" text-bold">
            <th class=" text-bold text-center">Pavilion Name</th>
            <th class=" text-bold text-center">Pavilion Size (in meter square)</th>
            <th class=" text-bold text-center">Pavilion Category</th>
            <th class=" text-bold text-center">Supervisor Name</th>
           </tr>
       </thead>
       <tbody>
           @php
            $total=0;
            @endphp
           @foreach ($pavilions as $pavilion)
           <tr >
               <td>{{ $pavilion->pavilionName }}</td>
               <td class=" text-center">{{ $pavilion->pavilion_size }}</td>
               <td>{{ $pavilion->category_name }}</td>
               <td>{{ $pavilion->user_name }} ____ {{ $pavilion->name }}</td>
           </tr>
           @php
               $total=$total+$pavilion->pavilion_size;
           @endphp
           @endforeach
           <tr>
               <td colspan="4">Dar es salaam International Trade Fair Ground Total Space Size = <b style="color:rgb(131, 237, 245)">{{ number_format($total) }}</b> metre square.</td>
           </tr>
       </tbody>
   </table>
   {{-- //pavilion general report --}}


</div>
</div>
</div>
<!-- /content area -->
</div>
<!-- /main content -->
</x-app-layout>


<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
