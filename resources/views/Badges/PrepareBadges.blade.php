@php $i=0; @endphp
{{-- badges frame setting --}}
<div id="printableArea" class=" form-group table-responsive">
<div style="padding: 1%">
   @foreach ($badges as $item)
 <div>
   <div style="float:left;width:40%; border:5px solid black; margin-top:5%; margin-left:6%;padding:1%;height:445px;">
       <img src="{{ asset('images/tz-flag.jpg') }}" style="position:absolute; width:41%; height:470px; opacity:5%;margin-left:-1%;margin-top:-1%">
       <div  style=" height:70px">
           <img src="{{ asset('images/badge-header.png') }}" style="width: 100%" height="70">
       </div>

          <div style=" height:90px">
           {{-- <p style=" text-align:center; font-size:110%;text-transform: uppercase;"><b>The {{ $item->event_name }}</b></p> --}}
           <p style=" text-align:center; font-size:110%;">THE 7<sup>th</sup> TANZANIA INDUSTRIAL PRODUCTS EXHIBITION.</p>
           <p style="text-align: center;">3<sup>rd</sup> - 9<sup>th</sup> December 2022</p>
           {{-- <p style="text-align: center;"> {{ date('d ',strtotime($item->event_start_date))}} - {{ date('d',strtotime($item->event_end_date))}} {{ date('F  Y',strtotime($item->event_end_date))}}</p> --}}
          </div>

           <div style="height: 18px;">
               <div style=" border-bottom:5px solid green;"></div>
               <div style=" border-bottom:5px solid blue;margin-top:2px;"></div>
           </div>

           <div style="height:120px;position:relative">
               <p style="text-transform: uppercase;text-align:center;"><b>{{ $item->companyName }}.</b></p>
               <p style="font-size:25px; text-align:center;padding:2%; background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};"><b>{{ $item->group_name }}</b></p>
           </div>

           <div style=" height:70px; display: flex;">
               <div style="width: 50%">
                   <b>{{ QrCode::size(70)->generate("BADGE ID: ".$item->badge_id."-(".$item->last_name."), ".$item->companyName." , ".$item->companyPhone." , ".$item->companyEmail ." ".$item->companyLocation)}}</b>
               </div>
               <div style="width:50%;">
                   @if ($item->group_name=='ORGANIZER')
                   <img src="{{ asset(asset('images/tz-flag.jpg')) }}" height="75" width="85" style="border-radius: 360%; border:2px solid blue;">
                   @else
                   <img src="{{ asset(asset('Members/'.$item->member_image)) }}" height="75" width="85" style="border-radius: 360%; border:2px solid blue;">
                   @endif
               </div>
           </div>

           <div style=" height:15px;padding:1%; text-align:center">
               @if ($item->group_name=='ORGANIZER')
               <p><b style="text-transform: uppercase; font-size:100%;font-family:monospace;"><q>Nunua Cha Kwetu</q></b></p>
               @else
               <p><b style="text-transform: uppercase; font-size:100%; font-family:monospace;">{{ $item->first_name }} - {{ $item->last_name }}</b></p>
               @endif
           </div>
           <div style=" height:20px;">
               <p style=" font-size:100%;font-family:monospace;"><b>Visit: </b> <i style="color:blue">www.gememis.go.tz</i></p>
           </div>
        </div>
 </div>
 @php $i++; @endphp
   @endforeach
</div>
</div>
{{-- end of badges frame setting --}}


@if($i==0)
<div class="alert-danger p-20">
   No Any badge found against your search! <i class=" icon-eye-blocked"></i>
</div>
@endif
