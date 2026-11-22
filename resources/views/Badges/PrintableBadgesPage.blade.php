@php
    #Logo
    $logo = public_path('badgeAssets/coat_of_arms.png');
    $image = Image::make($logo);
    $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    #Badge Header
    $badgeHeader = public_path('images/badgeHeader.png');
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
                        <th colspan="3">
                            <img src="{{ $base64_badgeHeader }}" height="50" style="width: 100%">
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style="">
                                <b>THE 48<sup>th</sup> DAR ES SALAAM INTERNATIONAL TRADE FAIR</b>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style="text-align: center; font-size:90%; color:green; height:1cm;">28<sup>th</sup>June
                                - 13<sup>th</sup> July 2024</div>
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

                                 @php
                                        #Nembo
                                     $image_path = public_path('/Members/' . Str::after($item->member_image,'Members/'));
                                        $image = Image::make($image_path);
                                        $base64_image = 'data:image/png;base64,' . base64_encode($image->encode('png'));
                                        #End Nembo
                                    @endphp
                                    
                                    <img src="{{ $base64_image }}" height="75" width="75"
                                        style="border-radius: 1%; border:2px solid blue;">
                                
                            </div>
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style=" text-transform:uppercase;text-align:center;font-size:12px;">
                            @php $name=$item->first_name.'  '.substr($item->middle_name, 0, 1).' '.$item->last_name; @endphp
                           
                            <div style=""> <b>{{ Illuminate\Support\Str::limit($name, 30) }}</b></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" style="text-align: center;">
                            <div style="text-transform: uppercase; height:1cm; color:green;font-size:15px;">

                                @php 
                            $company=App\Models\companies::where('companyID',$item->associateCompany)->first();
                            @endphp
                            @if (!$company)
                            <div style=""> 
                                <b>{{ Illuminate\Support\Str::limit($item->companyName, 60) }}</b>
                            </div>
                            @else
                            <div style="">
                                <b style="">{{ Illuminate\Support\Str::limit($company->companyName, 60) }}.</b>
                                
                            </div>
                            
                            @endif
                                 
                                
                                </div>

                            <div
                                style="text-align:center;padding:3%;  background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};">
                                <b>{{ $item->group_name }}</b></div>
                        </td>
                    </tr>

                    {{-- <tr style="">
                <td colspan="3">
                   <div style="text-align:center;color:blue;"><b><q>Tanzania Your Best Destination For Business and Investiment </q></b></div>
                </td>
            </tr> --}}
                    <tr style="">
                        <td>
                            <div style="text-align: left;">
                                <img src="{{ $logo }}" width="50" height="50">
                            </div>

                        </td>
                        <td colspan="2">
                            <div style="color:blue;text-align:left;"><b>Tanzania: Your Best Destination for Business and
                                    Investment</b></div>
                        </td>
                    </tr>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</div>
