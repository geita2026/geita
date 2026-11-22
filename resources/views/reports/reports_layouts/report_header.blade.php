@php
       #Logo
       $logo =public_path('images/logo.jpg');
        $image = Image::make($logo);
        $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #end logo

        #Nembo
        $nembo =public_path('/images/nembo.png');
        $image = Image::make($nembo);
        $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #End Nembo
        // ***********************
@endphp

<thead style="font-size:12px;">
    <tr>
        <th style=" text-align:left">
            <img src="{{$base64_nembo ?? ''}}" height="50" width="70">
        </th>
        <th style="text-align: center">
            GEITA MINERAL EXHIBITION SECRETARIAT<br>
            P.O. BOX 5402, Dar Es Salaam, Tanzania<br>
            Tel: +255 22 2850238, Fax: +255 22 2850539
            <br><b style="letter-spacing: 3px;">TIN:113-710-888 | VRN: 40-010307-T</b>
        </th>
        <th style=" text-align:right">
            {{-- {{ $base64_logo }} --}}
            <img src="{{$base64_logo ?? ''}}" height="50" width="70">
        </th>
    </tr>
</thead>
