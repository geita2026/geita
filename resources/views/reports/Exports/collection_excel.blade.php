<style>
    table{
        width: 100%;
        padding: 1%;
    }
    .collectionReport{
        height: 98%;
        border:2px solid black;
        padding: 1%;
    }
</style>

<div class="collectionReport" >
    <table>
        @include('reports.reports_layouts.report_header')
    </table>
    <div style="border:3px solid blue:"></div>
    <div style="border:3px solid rgb(0, 255, 64):"></div><br>
    <table class="  table table-bordered  table-responsive table-striped" border="1">
        <thead>
           <tr>
               <th colspan="3" style="background-color: rgb(227, 227, 236);">
                   <p class="text-bold text-uppercase text-center">COLLECTION REPORTS FOR <b>{{ $event_name }}</b> FROM {{ date('M d, Y',strtotime($from_date)) }} To {{ date('M d, Y',strtotime($to_date)) }}.</p>
               </th>
           </tr>
            <tr>
                <th class=" text-bold text-center">TOTAL INVOICES</th>
                <th class=" text-bold text-center">AMOUNT EXPECTED</th>
                <th class=" text-bold text-center">AMOUNT PAID</th>
            </tr>
        </thead>
        <tbody>

            @php  $usd=0; $tzs=0; $usd_paid=0; $tz_paid=0;  @endphp
            @foreach ($bills as $data)
            @if ($data->currency=='TZS')
            @php
            $tzs=$tzs+$data->total_amount;
            $tz_paid=$tz_paid+$data->amount_paid;
            @endphp
            @endif

            @if ($data->currency=='USD')
            @php
            $usd=$usd+$data->total_amount;
            $usd_paid=$usd_paid+$data->amount_paid;
            @endphp
            @endif

            @endforeach


           <tr>
               <td style=" text-align:center;">{{ number_format($nvoiceCount) }}</td>
               <td style=" text-align:right;">
                   {{ number_format($tzs,2) }} TZS
                   <hr>
                   {{ number_format($usd,2) }} USD
               </td>

               <td style=" text-align:right;">
                   {{ number_format($tz_paid ?? 0,2) }} TZS
                   <hr>
                   {{ number_format($usd_paid ?? 0,2) }} USD

               </td>
           </tr>
        </tbody>
       </table>

       <div style="bottom: 0;display:inline-block; width:100%; font-size:80%;">
        <div style=" text-align:right;"> Printed By: {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} | on: {{ date('M d, Y h:i') }} </div>
       </div>
</div>
