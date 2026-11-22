@php
    #Ditf Logo
    $image = Image::make(public_path('badgeAssets/coat_of_arms.png'));
    $base64_DITFlogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end Ditf logo

    #Nembo ya taifa
    $image = Image::make(public_path('badgeAssets/coat_of_arms.png'));
    $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************

    #Nembo ya taifa
    $image = Image::make(public_path('badgeAssets/coat_of_arms.png'));
    $base64_tantradeLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************

    #49DITF ya taifa
    $image = Image::make(public_path('badgeAssets/coat_of_arms.png'));
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
    <title>Geita Mineral Exhibition ID Card</title>
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
        }

        .name {
            font-size: 10px;
            font-weight: bold;
            margin-top: 0.5cm;
            margin-bottom: 0;
            font-size: 10px;
        }

        .logos img {
            height: 20px;
            margin: 0 1px;
        }

        .decor {
            position: absolute;
            width: 13px;
            height: 60%;
            background: url({{ $base64_sabasaba }}) repeat-y;
            background-size: contain;
        }

        .decor.left {
            left: 0;
            bottom: 0;
        }

        .decor.right {
            right: 0;
            bottom: 0;
        }

        .bottom-curve {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            top: 23.5%;
        }
    </style>
</head>

<body>

    @foreach ($badges as $item)
        @php
            #Nembo
            $image_path = public_path('/Members/' . Str::after($item->member_image, 'Members/'));
            $image = Image::make($image_path);
            $base64_photo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
            #End Nembo
        @endphp
        <div class="id-card"
            style="font-family:Arial; height: 410px; width: 8cm; justify-content:center; margin-top:70px; padding:15px; align-items: center; border:2px solid rgb(0, 55, 255); display: inline-block;">
            
            <div style="text-align: center;margin-top:3px;">
                <img src="{{ $base64_nembo }}" class="left-logo" alt="Tanzania Coat of Arms" height="40"
                    width="50" />
                    <b></b>
                <img src="{{ $base64_tantradeLogo }}" class="right-logo" alt="Coat of Arms" height="40"
                    width="50" style="margin-left: 50px"/>
            </div>

            <div class="title">
                <h2 style="font-size:12px">GEITA MINERAL EXHIBITION SECRETARIAT</h2>
                <h3 style="font-size:10px">THE 49<sup>th</sup> DAR ES SALAAM INTERNATIONAL TRADE FAIR</h3>
                <p class="dates">
                    <b>28<sup>th</sup> June – 13<sup>th</sup> July 2025</b>
                </p>
            </div>

            <div class="profile">
                <div>
                    <img src="{{ $base64_photo }}" class="profile-pic" style="100%" />
                </div>
            </div>
            <div class="bottom-curve">
                <img width="100%" src="{{ $ribbon }}" alt="" srcset="" />
            </div>

            <div class="info">
                <h2 class="name"><b style="text-transform: uppercase;font-size:12px;">
                        {{ Illuminate\Support\Str::limit($item->first_name . '  ' . substr($item->middle_name, 0, 1) . ' ' . $item->last_name, 30) }}
                    </b></h2>
                <div style="font-size:8px">
                    <b>
                        @php
                            $company = App\Models\companies::where('companyID', $item->associateCompany)->first();
                        @endphp
                        @if (!$company)
                            {{ Illuminate\Support\Str::limit($item->companyName, 60) }}
                        @else
                            {{ Illuminate\Support\Str::limit($company->companyName, 60) }}.
                        @endif
                    </b>
                </div>
                <div
                    style="text-align:center;padding:0.5%; text-transform:uppercase;  background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};border-radius:2%;margin-top:2px">
                    <b style="font-size:18px;">{{ $item->group_name }}</b>
                </div>
            </div>
            
            <div style=" text-align:center;margin-top:5px;">
                    <div>
                        <img src="data:images/png;base64, {{ base64_encode(QrCode::size(80)->generate('SAMS|' . $item->member_id)) }} "
                        class="qr" style="border: 1px solid black;">
                    </div>
                <div style="margin-top: 8px;">
                    <div class="logos">
                        <img src="{{ $base64_DITFlogo }}" />
                        <img src="{{ $base64_sabasaba }}" />
                        <img src="{{ $six }}" />
                    </div>
                    <div style="font-size:10px;color:blue">
                        Maonesho ya Biashara ya Kimataifa (SabaSaba) Fahari ya Tanzania
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
