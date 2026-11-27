@php
$count=0;
    #Logo
    $logo = public_path('images/muungano60.png');
    $image = Image::make($logo);
    $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    #Badge Header
    $tantradeLogo = public_path('images/Logo.png');
    $image = Image::make($tantradeLogo);
    $base64_tantradeLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
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
                    {{-- <tr>
                        <th colspan="3">
                            <img src="{{ $base64_tantradeLogo }}" height="50" style="width: 100%">
                        </th>
                    </tr> --}}
                    <tr>
                        <th colspan="3">
                            <div style="font-size:18px;text-transform:uppercase">
                                <b>Maonesho ya Biashara ya Muungano.</b>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style="text-align: center; font-size:90%; color:blue; height:1cm;">19<sup>th</sup>April
                                - 25<sup>th</sup> April 2024.</div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <div style=" border-bottom:5px solid green;"></div>
                            <div style=" border-bottom:5px solid black;"></div>
                            <div style=" border-bottom:5px solid rgb(255, 251, 0);"></div>
                            <div style=" border-bottom:5px solid blue;margin-top:2px;"></div>
                        </th>
                    </tr>


                    <tr style="text-align: center">
                        <td>
                            <img src="{{ $base64_tantradeLogo }}" height="60" width="100">
                        </td>
                        <td colspan="3" style="text-align: right;">
                            <img src="{{ $logo }}" width="150" height="120">

                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" style="text-align: center;">
                            <div style="text-transform: uppercase; height:1cm; color:blue;font-size:15px;">
                                <b style="">{{ Illuminate\Support\Str::limit($item->companyName, 50) }}.</b>
                            </div>
                            <div
                                style="text-align:center;padding:3%;  background-color: @if ($item->badgeTitle == 'ORGANIZER') blue @else green @endif;color:white">
                                <b>{{ $item->badgeTitle }}</b>
                            </div>
                        </td>
                    </tr>

                    {{-- <tr style="">
                <td colspan="3">
                   <div style="text-align:center;color:blue;"><b><q>Tanzania Your Best Destination For Business and Investiment </q></b></div>
                </td>
            </tr> --}}
                    <tr style="">
                        <td colspan="3">
                            <div style="color:blue;text-align:justify; font-family:Arial;">
                                <q>
                                    <b>
                                        Miaka 60 ya Jamuhuri ya Muungano wa Tanzania Tumeshikamana na Tumeimarika Kwa
                                    Maendeleo Ya Taifa Letu.
                                    </b>
                                </q>
                            </div>
                        </td>
                    </tr>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</div>

