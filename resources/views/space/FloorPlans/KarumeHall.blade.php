<div class=" scrollable-row" style="width:5000px;text-align: center; border:1px solid rgb(196, 193, 193); height:1200px">

    <div style="border:3px dashed rgb(170, 169, 169); padding:0.5%">
        @php $i = 0; @endphp
        @foreach ($booths as $stand)
            @php
                $i++;
                $mapLocation = $stand->boothLocation ?? $stand->location ?? null;
                if ($mapLocation !== null && $mapLocation !== '' && is_numeric($mapLocation)) {
                    $mapSlot = (int) $mapLocation;
                    if ($mapSlot > 0) {
                        $i = $mapSlot;
                    }
                }
            @endphp
            {{-- First Row --}}
            @if ($i <= 15)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 15)
                <br><br>
                <br> 
                <b style="margin-left:40%; font-size:1vmax" class=" text-danger">EXIT <i class=" icon-arrow-right16"></i></b>
                <br>
                <br>
                <br>
            @endif

            {{-- Second Row --}}
            @if ($i == 16)
                <b style="margin-right:5%"> @include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 17 && $i <= 20)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 21)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 22 && $i <= 25)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 26)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 26)
                <br><br><br>
            @endif
            {{-- End second Row --}}


            {{-- Third --}}
            @if ($i == 27)
                <b style="margin-right:5%"> @include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 28 && $i <= 31)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 32)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 33 && $i <= 36)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 37)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 37)
                <br><br><br>
                 <b style="margin-right: 40%"> <b style="font-size:1vmax" class=" text-danger"> <i class=" icon-arrow-left16"></i>EXIT</b> </b>
                <br><br>
            @endif
            {{-- End Third Row --}}


            {{-- Fourth --}}
            @if ($i == 38)
                <b style="margin-right:5%"> @include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 39 && $i <= 42)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 43)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 44 && $i <= 47)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 48)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 48)
                <br><br><br>
            @endif
            {{-- End Fourth Row --}}


            {{-- Five --}}
            @if ($i == 49)
                <b style="margin-left: 7%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 50 && $i <= 53)
                <b style="">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 54)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 55)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 56 && $i <= 58)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 59)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 60 && $i <= 62)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 63)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 64)
                <b style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 65)
                <b style="margin-left:1%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 66 && $i <= 73)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            {{-- End Five Row --}}

            @if ($i == 73)
                <br><br><br><br><br>
            @endif

            {{-- Six Row --}}
            @if ($i == 74)
                <b style="margin-left: 7.2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 75)
                <b style="margin-left: 2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i >= 76 && $i <= 78)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 79)
                <b style="margin-left: 3%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 80 && $i <= 82)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 83)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 84 && $i <= 87)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 88)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 89 && $i <= 91)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 92)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 93 && $i <= 96)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 97)
                <b style="margin-left: 4%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 97)
                <br><br><br>
            @endif

            {{-- Seven Row --}}
            @if ($i == 98)
                <b style="margin-left: 7.2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 99)
                <b style="margin-left: 2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i >= 100 && $i <= 102)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 103)
                <b style="margin-left: 3%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 104 && $i <= 106)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 107)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 108 && $i <= 111)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 112)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 113 && $i <= 115)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 116)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 117 && $i <= 120)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 121)
                <b style="margin-left: 4%">@include('space.FloorPlans.BoothFormat')</b>
            @endif


            @if ($i == 121)
                <br><br><br><br><br>
            @endif

            {{-- EIGHT Row --}}
            @if ($i == 122)
                <b style="margin-left: 7.2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 123)
                <b style="margin-left: 2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i >= 124 && $i <= 126)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 127)
                <b style="margin-left: 3%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 128 && $i <= 130)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 131)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 132 && $i <= 135)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 136)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 137 && $i <= 139)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 140)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 141 && $i <= 144)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 145)
                <b style="margin-left: 4%">@include('space.FloorPlans.BoothFormat')</b>
            @endif




            @if ($i == 145)
                <br><br><br>
            @endif

            {{-- NINE Row --}}
            @if ($i == 146)
                <b style="margin-left: 7.2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 147)
                <b style="margin-left: 2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i >= 148 && $i <= 150)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 151)
                <b style="margin-left: 3%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 152 && $i <= 154)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 155)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 156 && $i <= 159)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 160)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 161 && $i <= 163)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 164)
                <b style="margin-left: 5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 165 && $i <= 168)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 169)

            {{-- <b style="margin-left: 4%"> <b style="font-size:1vmax" class=" text-danger">EXIT <i class=" icon-arrow-right16"></i></b> </b> --}}

                <b style="margin-left: 4%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 169)
                <br><br><br>
            @endif
            @if ($i == 170)
                <b style="margin-right:80.1%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 171)
                <b style="margin-left: 94.56%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 171)
                <br><br>
            @endif
            @if ($i == 172)
                <b style="margin-right:80.1%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 173)
                <b style="margin-left: 94.56%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 173)
                <br><br>
            @endif
            @if ($i == 174)
                <b style="margin-right:80.1%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 175)
                <b style="margin-left: 94.56%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 175)
                <br><br>
            @endif
            @if ($i == 176)
            <b style="margin-right:80.1%">@include('space.FloorPlans.BoothFormat')</b>
        @endif
        @if ($i == 177)
            <b style="margin-left: 94.56%">@include('space.FloorPlans.BoothFormat')</b>
        @endif
            @if ($i == 177)
                <br><br><br>
            @endif
            {{-- TEN Row --}}
            @if ($i==177)
            <b style="margin-left: 7.5%;">
                <i class=" icon-home" style="font-size:4vmax; width:107px"></i>
            </b>
            @endif
            @if ($i >= 178 && $i <= 190)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 191)
                <b style="margin-left: 2.9%;font-size:1vmax" class=" text-danger"> <i class="icon-arrow-down16"></i> EXIT</b><b style="margin-left: 3%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 192 && $i <= 202)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 203)
            <b style="margin-left: 2.9%;font-size:1vmax" class=" text-danger"> <i class=" icon-arrow-up16"></i> MAIN ENTRANCE</b> <b style="margin-left: 2%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

        @endforeach
    </div>
</div>
