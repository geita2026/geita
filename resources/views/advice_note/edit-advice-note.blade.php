<script>
    function printPageArea(areaID){
        var printContent = document.getElementById(areaID);
        var WinPrint = window.open('', '', 'width=900,height=650');
        WinPrint.document.write(printContent.innerHTML);
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
    }
</script>
<title>{{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
    {{-- contents on dashboards --}}
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           <div class="page-title">

           </div>
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('adviceNotes.index') }}"><i class="icon-home2 position-left"></i>ADVICE NOTE</a></li>
           <li class="active">view Advice note</li>
       </ul>

       <ul class="breadcrumb-elements">
           <li><a href="#"><i class="icon-comment-discussion position-left"></i>Help center</a></li>
       </ul>
   </div>
   </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">
<div class="panel panel-flat">
<div class=" panel-body table-responsive">
   <div class=" row">
    <div class=" form-group col-xs-6">
        <a href="{{ route('adviceNotes.index') }}" class=" btn btn-primary"><i class=" icon-arrow-left12"></i> Back</a>
    </div>
  <div class=" form-group col-xs-6 text-right">
      <button class="btn btn-primary" onclick="printPageArea('printableArea')">Print <i class=" icon-printer2"></i></button>
  </div>
   </div>


<div id="printableArea">
    <style>
        #customers {
          font-family: cambria;
          border-collapse: collapse;
          width: 100%;
        }

        #customers td, #customers th {
          border: 1px solid #ddd;
          padding: 8px;
        }

        #customers tr:nth-child(even){background-color: #f2f2f2;}

        #customers tr:hover {background-color: #ddd;}

        #customers th {
          padding-top: 12px;
          padding-bottom: 12px;
          text-align: left;
          background-color: #04AA6D;
          color: white;
        }
          #boxes {
            content: "";
            display: table;
            clear: both;
          }
        </style>
    <div style=" border:1px solid black; width:100%">
    <div class="hedding" style="display: flex;border-bottom:1px solid black;padding:1%; widith:100%">
        <div style="margin-right: auto;margin-left: 0;">
        {{--  <img src="data:image/png;base64,{{ $nembo }}" height="50" width="50">  --}}
        <img src="{{ asset('images/nembo.png') }}" height="80" width="80">
        </div>
        <div style="text-align: center;">
        <h6>GEITA MINERAL EXHIBITION SECRETARIAT<br> (Tan Trade)</h6>
        <p>P.O. BOX 5402, Geita, Tanzania </p>
        <p>Tel: +255 22 2850238, Fax: +255 22 2850539</p>
       </div>
        <div style="margin-left:auto;margin-right: 0;">
        {{--  <img src="data:image/png;base64,{{ $logo }}" height="50" width="50">  --}}
        <img src="{{ asset('images/logo.jpg') }}" height="80" width="100">
        </div>
 </div>

 <div style=" padding:1%">
@foreach ($adviceNotes as $item)
<div style="display:flex;">
<p style="margin-left:auto;margin-right:0">ADVICE NOTE NO: <b style="border-bottom:1px dotted pink;color:rgb(226, 17, 51)">{{ $item->advice_note_id }}</b></p>
</div>
<br>
<div style="display:flex;">
    <div style="margin-left:0;margin-right:auto">
        <p>Exhibitor Name: <b style="border-bottom:1px dotted black;">{{ $item->companyName }}</b></p>
        <p>Address: <b style="border-bottom:1px dotted black;">
            @if($item->companyAddress)
            {{ $item->companyAddress }}, {{ $item->companyLocation }}
            @else
            Null
            @endif
           </b>

        </p>
        <p>Phone Number: <b style="border-bottom:1px dotted black;">{{ $item->companyPhone }}</b></p>
    </div>
    <div style="margin-left:auto;margin-right:0">
        <p><b style="border-bottom:1px dotted black;"></b></p><br><br>
        <p>Date: <b style="border-bottom:1px dotted black;">{{ $item->created_at }}</b></p>
    </div>
</div>
<br>
<table id="customers">
    <tr >
        <th class="text-center">S/N</th>
        <th class="text-center">Paticulars</th>
        <th class="text-center">Quantity</th>
        <th>@ Unit COst</th>
        <th class="text-center">Sub Total</th>
        <th>VAT</th>
    </tr>

    {{-- booked space --}}
    <tr>
        <td>1</td>
        <td>Participation fee</td>
        <td>{{ $total_booth*9 }}</td>
        <td>{{ number_format($booth_cost) }}</td>
        <td>{{ number_format($participation_fee) }}</td>
        <td>Null</td>
    </tr>
    {{-- booked space --}}

    {{-- additional services --}}
    <tr>
        <td>2</td>
        <td>Exhibition Badges</td>
        <td>{{ $mandatory_quantity }}</td>
        <td>{{ number_format($service_cost) }}</td>
        <td>{{ number_format($mandatory_quantity*$service_cost) }}</td>
        <td>Null</td>
    </tr>
    {{-- additional services --}}
@php
    $i=3;
    $optional_service=0;
@endphp
    @foreach ($services as $service)
    @if($service->service_priority=='Optional')
<tr>
    <td>{{ $i }}</td>
    <td>{{ $service->serviceName }}</td>
    <td>{{ $service->service_quantity }}</td>
    <td>{{ $service->serviceCost }}</td>
    <td>{{ number_format(($service->serviceCost)*($service->service_quantity)) }}</td>
    <td>Null</td>
</tr>
@php


$optional_service=(($service->serviceCost)*($service->service_quantity))+$optional_service;
$i++;
@endphp
    @endif
    @endforeach

    <tr class=" bg-white">
        {{-- <td colspan="3" ></td> --}}
        <td colspan="4" class=" text-bold">Total: </td>
        <td>{{ number_format(($mandatory_quantity*$service_cost )+($participation_fee)+$optional_service) }}</td>
        <td>Null</td>
    </tr>

</table>

<div style="display:flex;" class=" mt-20">
    <div style="margin-left: 0; margin-right:auto">
        <p>Issued By: <b style="border-bottom:1px dotted black;">{{ $item->user_name }}</b></p>
        <p>Siginature . . . . . . . . . . . . . . . . . . . . .</p>
    </div>
    <div style="margin-left:auto;margin-right:0%">
        <p>Title:  <b style="border-bottom:1px dotted black;">{{ $item->name }}</b></p>

        {{--  <p>Date: <b style="border-bottom:1px dotted black;">{{ $item->created_at }}</b></p>  --}}
    </div>
</div>
@endforeach
</div>
</div>
</div>
</div>
</div>
<!-- /content area -->
</div>
<!-- /main content -->
</x-app-layout>
