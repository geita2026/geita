<table class="  table table-bordered  table-responsive table-striped">
    <thead>
       <tr>
           <th colspan="3" style="background-color: rgb(227, 227, 236);">
               <h4 class="panel-title text-bold text-uppercase text-center">SPACE BOOKING REPORT FOR THE {{ $event_name }} FROM {{ date('M d, Y',strtotime($from_date)) }} To {{ date('M d, Y',strtotime($to_date)) }}</h6>
           </th>
       </tr>
        <tr>
            <th class="text-bold text-center">DESCRIPTIONS</th>
            <th class=" text-bold text-center">TOTAL REGISTERED SPACES</th>
            <th class=" text-bold text-center">TOTAL BOOKED SPACES</th>
        </tr>
    </thead>
    <tbody>

       <tr>
           <td>Amount of space booking status from <b>{{ date('M d, Y',strtotime($from_date)) }}</b> To <b>{{ date('M d, Y',strtotime($to_date)) }}</b></td>
           <td class=" text-center">{{ number_format($total_spaces,2) }} M<sup>2</sup></td>
           <td class=" text-center">{{ number_format($booked_space,2) }} M<sup>2</sup></td>
       </tr>
    </tbody>
   </table>
   <div class=" text-right text-muted">Requested on: {{ date('M d, Y h:i:s') }}</div>
