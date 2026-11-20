{{-- badges frame setting --}}
<div id="printableArea" class=" form-group table-responsive">
    <div style="padding: 1%">
       @foreach ($company_details as $item)
     <div>

       <div style="float:left;width:35%; border:5px solid black; margin-top:5%; margin-left:10%;padding:1%;height:450px;">
           <img src="{{ asset('images/tz-flag.jpg') }}" style="position:absolute; width:35%; height:450px; opacity:10%;">
           <div  style="display: flex; height:80px">
               <div style="margin-right: auto;margin-left: 0;">
                   <img src="{{ asset('images/nembo.png') }}" height="70" width="100">
               </div>

               <div style="margin-left:auto;margin-right: 0;">
                   <img src="{{ asset('images/logo.jpg') }}" height="70" width="120">
               </div>
           </div>

              <div style=" height:80px">
               <p style=" text-align:center; font-size:110%;text-transform: uppercase;"><b>{{ $item->event_name }}</b></p>
               <p style="text-align: center;">{{ date('M d, ',strtotime($item->event_start_date))}} To {{ date('M d, Y',strtotime($item->event_end_date))}}</p>
              </div>

               <div style="height: 20px;">
                   <div style=" border-bottom:5px solid green;"></div>
                   <div style=" border-bottom:5px solid blue;"></div>
               </div>


               <div style="height:110px;position:relative">
                   <p><b>M/S: </b> {{ $item->companyName }}</p>
                   <p style="font-size:25px; text-align:center;padding:2%; background-color:green;color:white;"><b>ORGANIZER</b></p>
               </div>

               <div style=" height:80px;position: relative;">
                   <p>
                       {{ QrCode::size(80)->generate($item->event_name)}}
                   </p>
               </div>

               <div style=" height:25px; position:relative">
                   <p><b>Powered By</b><br>Tanzania Trade Development Authorityz.</p>
               </div>
            </div>
     </div>

       @endforeach
    </div>
   </div>
    {{-- end of badges frame setting --}}
