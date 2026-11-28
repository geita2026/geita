
{{--  sgdfsgg --}}
@php

    #Logo
    $logo =public_path('images/nembo.png');
        $image = Image::make($logo);
        $nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

     #Logo
     $logo =public_path('images/nbcLogo.png');
        $image = Image::make($logo);
        $nbcLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo
    #Logo
    $logo =public_path('images/crdbLogo2.png');
        $image = Image::make($logo);
        $crdbLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

       #Logo
       $logo =public_path('images/nmbLogo1.png');
        $image = Image::make($logo);
        $nmbLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    $logo =public_path('images/TadbLogo.png');
        $image = Image::make($logo);
        $tadbLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

     #Logo
     $logo =public_path('images/azaniaLogo.png');
        $image = Image::make($logo);
        $azaniaLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo
    #Logo
    $logo =public_path('images/gsmLogo2.png');
        $image = Image::make($logo);
        $gsmLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    #Logo
    $logo = public_path('images/nanenaneLogo.png');
    $image = Image::make($logo);
    $nanenaneLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    #Badge Header badgeHeader.png
    $badgeHeader=public_path('badgeAssets/coat_of_arms.png');
    $image = Image::make($badgeHeader);
    $tantradeLogo= 'data:image/png;base64,' . base64_encode($image->encode('png'));
        // ***********************

    #Badge Header
    $badgeHeader = public_path('images/badgeHeader.png');
    $image = Image::make($badgeHeader);
    $base64_badgeHeader = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************
    #Badge Header
    $badgeHeader = public_path('images/crdbLogo.png');
    $image = Image::make($badgeHeader);
    $base64_badgeHeader = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************
@endphp

<style>
    #h4 {
        font-size: 100%;
        text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
    }
</style>
<div style="">
    @foreach ($badges as $item)
        <div
            style=" font-family:Arial; height: 400px; width: 8cm; justify-content:center; margin-top:70px; padding:20px; align-items: center; border:2px dashed black; display: inline-block;">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <img src="{{ $nembo }}" height="50" width="70">
                        </th>
                        <th></th>
                        <th>
                            <img src="{{ $nanenaneLogo }}" height="50" style="width: 100%">
                        </th>
                        {{-- <th>
                            <img src="{{ $nanenaneLogo }}" height="50" style="width: 100%">
                        </th> --}}
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style="">
                                <b>MAONESHO YA KIMATAIFA YA NANENANE, 2024.</b>
                                {{-- <b style="font-size:10px;">(NANENANE AGRICULTURAL INTERNATIONAL EXPO, 2024.)</b> --}}
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style="text-align: center; font-size:90%; color:green; height:1cm;">01 - 08 Agosti, 2024</div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style=" border-bottom:5px solid green;"></div>
                            <div style=" border-bottom:5px solid blue;margin-top:2px;"></div>
                        </th>
                    </tr>


                    <tr>
                        <td>
                            <div style="text-align:left;">
                                <img src="data:images/png;base64, {{ base64_encode(QrCode::size(80)->generate('SAMS|' . $item->member_id)) }} "
                                    style="border:1px solid black;">
                            </div>
                        </td>
                        <td style="text-align: center">
                            {{-- <b style="font-size: 12px; font-family:monospace;color:green">{{ $item->registrationID }}</b> --}}
                        </td>
                        <td colspan="2">
                            <div style="text-align:right;">
                                <img src="{{ $gsmLogo }}" height="60" width="50">
                                    <img src="{{ $azaniaLogo }}" height="50" width="35">
                                    
                                {{-- <br>
                                @php $name=$item->first_name.'  '.substr($item->middle_name, 0, 1).' '.$item->last_name; @endphp
                                <b style="font-size:10px; text-align:center">{{ $name }}</b> --}}
                            </div>
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center">
                            <img src="{{ $nmbLogo }}" height="20" width="40">
                            <img src="{{ $crdbLogo }}" height="15" width="40">
                            <img src="{{ $tadbLogo }}" height="15" width="50">
                            <img src="{{ $nbcLogo }}" height="20" width="30">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" style="text-align: center;">
                            <div
                                style="text-align:center;padding:3%;  background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};">
                                <b style="font-size:20px">{{ $item->group_name }}</b></div>
                        </td>
                    </tr>
                    {{-- <tr style="">
                <td colspan="3">
                   <div style="text-align:center;color:blue;"><b><q>Tanzania Your Best Destination For Business and Investiment </q></b></div>
                </td>
            </tr> --}}
                    <tr style="">
                        <td colspan="3">
                            <div style="color:blue;text-align:center;font-size:15px;font-family:monospace"><b>
                                <br>Chagua Viongozi Bora wa Serikali za Mitaa kwa Maendeleo Endelevu ya Kilimo, Mifugo na Uvuvi</b></div>
                        </td>
                    </tr>
                    {{-- <tr>
                        <td colspan="3" style="text-align: left;font-size:7px;margin-bottom:-10%;font-family:monospace"><b>POWERED BY <b style="color:green">GeMEMIS</b></b></td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
    @endforeach
</div>
