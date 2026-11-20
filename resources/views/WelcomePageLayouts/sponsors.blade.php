
<div class="form-group row">
    <marquee  direction="left" scrolldelay="50">
                           
    @php $i=0; @endphp
        @foreach ($sponsors as $item)
        {{-- target --}}
        
        <div class=" col-xs-3 form-group text-center">
        <a href="{{ $item->sponsor_website }}" target="_tab" style="" title="{{ $item->sponsor_name }}"><img src="{{ asset('VirtualImages/'.$item->sponsor_logo) }}"  height="50" width="90" style="border:5px solid rgb(152, 152, 153);border-radius:5%"></a><br>
        <div class=" text-primary">{{ $item->sponsor_name }}</div>
        </div>
       
    @php $i++; @endphp
        @endforeach
    </marquee>
    @if ($i==0)
    <div class=" alert alert-danger">
        <b>No</b> Supporters published now.
    </div>
    @endif
   </div>


