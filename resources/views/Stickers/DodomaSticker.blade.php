<title>
    {{ env('APP_NAME') }} | Print Stickers
</title>

@php
        #Logo
        $logo =public_path('images/nembo.png');
        $image = Image::make($logo);
        $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #end logo

        #Badge Header badgeHeader.png
        $badgeHeader=public_path('images/logo.jpg');
        $image = Image::make($badgeHeader);
        $base64_badgeHeader= 'data:image/png;base64,' . base64_encode($image->encode('png'));
        // ***********************
@endphp

<style>
    #h4{ font-size:100%; text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);}
</style>
<div style="">
    @foreach ($stickers as $item)
    <div style=" font-family:Arial; height: 400px; width: 8cm; justify-content:center; margin-top:70px; padding:20px; align-items: center; border:2px dashed black; display: inline-block;">
    <table >
        <tbody>
            <tr>
                <th>
                    <img src="{{ $base64_logo }}" height="50" style="width: 70%">
                </th>
                <th>

                </th>
                <th>
                    <img src="{{ $base64_badgeHeader }}" height="50" style="width: 70%">
                </th>
            </tr>
            <tr>
                <th colspan="3">
                    <div style="">
                        <b>MAONESHO YA NANENANE KANDA YA KATI - 2023</b>
                    </div>
                </th>
            </tr>
            <tr>
                <th colspan="3" >
                    <div style="text-align: center; font-size:90%; color:green; height:1cm;">1<sup>st</sup> - 8<sup>th</sup> Augost,  2023</div>
                </th>
            </tr>
            <tr>
                <th colspan="3">
                    <div style=" border-bottom:5px solid green;"></div>
                   <div style=" border-bottom:5px solid blue;margin-top:2px;"></div>
                </th>
            </tr>


            <tr>

                <td colspan="1" style="text-align: left;">
                    <img src="data:images/png;base64, {{ base64_encode(QrCode::size(70)->generate('SAMS|'.$item->sticker_id)) }} " style="border:1px solid black">
                </td>
                <td style=" text-align:center;" colspan="2">
                    <b style=" font-size:30px">{{ $item->plate_number }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="3" style=" text-transform:uppercase;text-align:center;font-size:90%;">
                    @php $name=$item->first_name.'  '.substr($item->middle_name, 0, 1).' '.$item->last_name; @endphp
                    <div style=""><b>{{ Illuminate\Support\Str::limit($name,30) }}</b></div>
                </td>
            </tr>

            <tr>
                <td colspan="3" style="text-align: center;">
                   <div style="text-transform: uppercase; height:1cm; color:green;"><b>{{ Illuminate\Support\Str::limit($item->companyName,30) }}.</b></div>

                   <div style="text-align:center;padding:1%; text-transform:uppercase;  background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};">
                    <b>{{ $item->serviceName }}<br> {{ number_format($item->serviceCost+($item->serviceCost*0.18)) }} {{ $item->type_of_currency }}
                </div>
                </td>
            </tr>

            {{-- <tr style="">
                <td colspan="3">
                   <div style="text-align:center;color:blue;"><b><q>Tanzania Your Best Destination For Business and Investiment </q></b></div>
                </td>
            </tr> --}}
            <tr style="">
                {{-- <td>
                    <div style="text-align: left;">
                        <img src="{{ $logo }}" width="50" height="50" >
                    </div>

                </td> --}}
                <td colspan="3">
                   <div style="color:blue;text-align:left;"><b>Vijana na Wanawake ni Msingi Imara wa Mifumo Endelevu ya Chakula.</b></div>
                </td>
            </tr>
        </tr>
        </tbody>
       </table>
    </div>
       @endforeach
   </div>
