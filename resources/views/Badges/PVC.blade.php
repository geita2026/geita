@php
    #Ditf Logo
    $image = Image::make(public_path('images/ditf11.jpeg'));
    $base64_DITFlogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #end Ditf logo

    #Nembo ya taifa
    $image = Image::make(public_path('images/nembo.png'));
    $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************

    #Nembo ya taifa
    $image = Image::make(public_path('images/Logo.png'));
    $base64_tantradeLogo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    // ***********************
@endphp
@php
    #background
    $image_path = public_path('/images/Tzflag.jpg');
    $image = Image::make($image_path);
    $background = 'data:image/png;base64,' . base64_encode($image->encode('png'));
    #End Background
@endphp
<style>
    #h4 {
        font-size: 100%;
        text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
    }

    .background {
        background-image: url('{{ $background }}');
        opacity: 0.2;
        display: cover;
    }
</style>

<div style="">
    @foreach ($badges as $item)
        @php
            #Nembo
            $image_path = public_path('/Members/' . Str::after($item->member_image, 'Members/'));
            $image = Image::make($image_path);
            $base64_photo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
            #End Nembo
        @endphp
        <div class="background"
            style=" font-family:Arial; height: 54mm; width: 85mm; justify-content:center; margin-top:80px; padding:12px; align-items: center; border:2px dashed blue; display: inline-block;">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <img src="{{ $base64_nembo }}" height="20" width="20">
                        </th>
                        <th>
                            <div style="font-size:9px">
                                <b>THE 49<sup>th</sup> DAR ES SALAAM INTERNATIONAL TRADE FAIR</b>
                            </div>
                        </th>
                        <th>
                            <img src="{{ $base64_tantradeLogo }}" height="20" width="30">
                        </th>
                    </tr>

                    <tr style="margin-top: 1px">
                        <th colspan="3">
                            <div style="text-align: center; font-size:8px; color:green; height:0.5cm;">
                                28<sup>th</sup>June
                                - 13<sup>th</sup> July 2025</div>
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
                                <img src="{{ $base64_photo }}" height="80" width="80"
                                    style="border-radius: 1%; border:2px solid blue;">
                            </div>
                        </td>
                        <td>
                            <div style="font-size:10px"><b>
                                    {{ Illuminate\Support\Str::limit($item->first_name . '  ' . substr($item->middle_name, 0, 1) . ' ' . $item->last_name, 30) }}
                                </b></div>

                            <div style="margin-top:2px;text-transform: uppercase; height:0.3cm; font-size:7px;">
                                <b>
                                    @php
                                        $company = App\Models\companies::where(
                                            'companyID',
                                            $item->associateCompany,
                                        )->first();
                                    @endphp
                                    @if (!$company)
                                        {{ Illuminate\Support\Str::limit($item->companyName, 60) }}
                                    @else
                                        {{ Illuminate\Support\Str::limit($company->companyName, 60) }}.
                                    @endif
                                </b>
                            </div>
                            <b style="font-size: 10px; font-family:monospace;">{{ $item->registrationID }}</b><br>
                        </td>

                        <td>
                            <div style="text-align:left;">
                                <img src="data:images/png;base64, {{ base64_encode(QrCode::size(80)->generate('SAMS|' . $item->member_id)) }} "
                                    style="border:1px solid black;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">

                            <div
                                style="text-align:center;padding:1%; text-transform:uppercase;  background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};border-radius:2%;">
                                <b>{{ $item->group_name }}d</b>
                            </div>
                        </td>
                    </tr>
                    <tr style="">
                        <td>
                            <div style="text-align: left;">
                                <img src="{{ $base64_DITFlogo }}" width="30" height="30">
                            </div>

                        </td>
                        <td colspan="2">
                            <div style="color:blue;text-align:left;
                            font-size:8px">
                                <b>
                                    Maonesho ya Biashara ya Kimataifa (SabaSaba) Fahari ya Tanzania
                                </b>
                            </div>
                        </td>
                    </tr>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</div>
