@php
    #Ditf Logo
    $image = Image::make(public_path('badgeAssets/ufi.png'));
    $base64_DITFlogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end Ditf logo

    #Nembo ya taifa
    $image = Image::make(public_path('badgeAssets/coat_of_arms.png'));
    $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************

    #Nembo ya taifa
    $image = Image::make(public_path('images/Logo.png'));
    $base64_tantradeLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************

    #49DITF ya taifa
    $image = Image::make(public_path('badgeAssets/49.png'));
    $base64_sabasaba = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************

@endphp
@php
    #background
    $image_path = public_path('/images/Tzflag.jpg');
    $image = Image::make($image_path);
    $background = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #End Background

    #background
    $image_path = public_path('badgeAssets/ribbon.png');
    $image = Image::make($image_path);
    $ribbon = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #End Background

    #background
    $image_path = public_path('badgeAssets/six.png');
    $image = Image::make($image_path);
    $six = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #End Background

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>TANTRADE ID Card</title>
    <style>
        .id-card {
            background: white;
            margin: auto;
            border: 5px solid #1a237e;
            padding: 5px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: center;
            gap: 40px;
            align-items: center;
        }
        .title {
            text-align: center;
            margin-top: 2px;
        }
        .title h2 {
            color: #1a237e;
            font-size: 9px;
            margin: 0.3px 0;
        }
        .title h3 {
            font-size: 2px;
            margin-top: 1px;
            margin-bottom: 0px;
        }
        .dates {
            color: green;
            font-size: 10px;
            margin-top: 0cm;
        }
        .profile {
            text-align: center;
            margin-top: 2px;
            font-size: 10px;
        }

        .profile-pic {
            height: 100px;
            width: 105px;
            border-radius: 50%;
            border: 3px solid #002aff;
            object-fit: cover;
        }

        .profile_cycle2 {
            margin: 0 auto;
            width: 120px;
            height:110px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            position: relative;
            border-radius: 50%;
            border: 3px solid #1a237e;
        }

        .info {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
        }

        .name {
            font-size: 10px;
            font-weight: bold;
            margin-top: 0.5cm;
            margin-bottom: 0;
            font-size: 10px;
        }

        .logos img {
            height: 50px;
            margin: 0 10px;
        }

        .bottom-curve {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            top: 40.5%;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body>

    @php
        $i=0;
    @endphp
    @while ($i <4)
       
        <div class="id-card"
            style="font-family:Arial; height: 410px; width: 8cm; justify-content:center; margin-top:70px; padding:5px; align-items: center; border:10px solid rgb(0, 55, 255); display: inline-block;">
            
            <div style="text-align: center;margin-top:3px;">
                <img src="{{ $base64_nembo }}" class="left-logo" alt="Tanzania Coat of Arms" height="40"
                    width="50" />
                    <b></b>
                <img src="{{ $base64_tantradeLogo }}" class="right-logo" alt="TanTrade Logo" height="40"
                    width="50" style="margin-left: 50px"/>
            </div>

            <div class="title">
                <h2 style="font-size:12px">TANZANIA TRADE DEVELOPMENT AUTHORITY (TANTRADE)</h2>
                <h3 style="font-size:10px">THE 49<sup>th</sup> DAR ES SALAAM INTERNATIONAL TRADE FAIR</h3>
                <p class="dates">
                    <b>28<sup>th</sup> June â€“ 13<sup>th</sup> July 2025</b>
                </p>
            </div>

            <div class="profile">
                <div>
                    <img src="" class="profile-pic" style="100%" />
                </div>
            </div>

            <div class="info">
                <div
                    style="text-align:center;padding:0.5%; text-transform:uppercase;  background-color: green;color:white;border-radius:2%;margin-top:2px">
                    <b style="font-size:60px;">VIP</b>
                </div>
            </div>
            
            <div style=" text-align:center;margin-top:30px;">
                    <div>
                        <img src="data:images/png;base64, {{ base64_encode(QrCode::size(80)->generate('SAMS|e59a7188-2d98-4dda-b522-fe109e095a1f')) }} "
                        class="qr" style="border: 1px solid black;">
                    </div>
                <div style="margin-top: 20px;">
                    <div class="logos">
                        <img src="{{ $base64_DITFlogo }}" />
                        <img src="{{ $base64_sabasaba }}" />
                        <img src="{{ $six }}" />
                    </div>
                    <div style="font-size:15px;color:blue">
                        Maonesho ya Biashara ya Kimataifa (SabaSaba) Fahari ya Tanzania
                    </div>
                </div>
            </div>
        </div>
        @php
            $i++;
        @endphp
    @endwhile
</body>

</html>

