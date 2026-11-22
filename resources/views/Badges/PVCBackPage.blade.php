@php
    #Logo
    $logo = public_path('images/logo.jpg');
    $image = Image::make($logo);
    $logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end logo

    #Badge Header
    $badgeHeader = public_path('badgeAssets/footer.png');
    $image = Image::make($badgeHeader);
    $footer = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************
    #background
    $image_path = public_path('badgeAssets/six.png');
    $image = Image::make($image_path);
    $six = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #End Background

    #Ditf Logo
    $image = Image::make(public_path('badgeAssets/ufi.png'));
    $base64_DITFlogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end Ditf logo
    $i = 0;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body>
    @while ($i < 4)
        <div
            style="font-family:Arial; height: 400px; width: 8cm; justify-content:center; margin-top:70px; padding:20px; align-items: center; border:2px dashed black; display: inline-block;">
            <div style="text-align: center">
                <img src="{{ $logo }}" width="60%" />
            </div>
            <div class="center-logos">
                <div style="display: flex;text-align:center">
                    <p style="text-align: center; font-size: 15px; font-weight: 700;">
                        The 49<sup>th</sup> Dar Es Salaam<br>International Trade Fair
                    </p>
                    <img src="{{ $six }}" width="22%" />
                    <img src="{{ $base64_DITFlogo }}" style="height:50px;width:50px" />
                </div>

            </div>
            <div style="text-align: center;; color: #3b53a2; font-weight: 500;">
                <p style="font-size:15px;">If found, return to Tanzania Trade Development Authority (TanTrade)</p>
                <p style="font-size:20px;color:green">+255 733 002 014</p>
            </div>
            <div style="position: absolute;  left: 0; right: 0;">
                <img src="{{ $footer }}" width="99%" height="70px" />
            </div>
        </div>
        @php
            $i++;
        @endphp
    @endwhile
</body>

</html>

