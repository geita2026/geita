@php
    #Logo
    $logo = public_path('images/logo.jpg');
    $image = Image::make($logo);
    $logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    #Badge Header
    $badgeHeader = public_path('images/badgeHeader.png');
    $image = Image::make($badgeHeader);
    $base64_badgeHeader = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************
    $i = 0;
@endphp


@while ($i < 4)
    <div
        style=" font-family:Arial;height: 400px; width: 8cm; justify-content:center; margin-top:70px; padding:20px; align-items: center; border:2px dashed black; display: inline-block;">
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" style="display: table-cell;
                    vertical-align: middle;">
                        <p style="font-size:15px; text-align:center;">
                            {{-- <h4 style="text-align: center">
                                NANENANE AGRICULTURAL INTERNATIONAL EXPO, 2024.
                            </h4> --}}
                         </p >
                         <p style="text-align: center;font-size:12px;color:blue;"><b> *PRINTED BY* </b></p>
                         {{-- <h1 style="text-align: center;font-size:15px;">TANTRADE</h1> --}}
                         
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4" style="text-align: center; margin-top:10px">
                        <img src="{{ $logo }}" height="60" width="200">
                        {{-- <p style="text-align: center">
                            TANZANIA TRADE DEVELOPMENT AUTHORITY (Tan Trade)<br>
                                P.O. BOX 5402, Geita, Tanzania<br>
                                Tel: +255 22 2850238, Fax: +255 22 2850539<br>
                                Email: info@tantrade.go.tz.
                           

                        </p> --}}
                        {{-- <p style=" text-align:center">
                            <img src="{{ $logo }}" height="60" width="200">
                        </p> --}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @php
        $i++;
    @endphp
@endwhile
