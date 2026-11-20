<div class=" scrollable-row"
    style="width:1500px;text-align: center; border:1px solid rgb(196, 193, 193);padding:2%; height:500px">

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
            @if ($i == 1)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 2)
                <b class="mvl">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i >= 3 && $i <= 8)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 9)
                <br><br>
                <b class=" text-danger" style="font-size:1vmax">
                    @include('space.FloorPlans.BoothFormat')
                    <b style=" margin-left:45%;font-size:1vmax"> <i class="icon-arrow-left16"></i> Entrance</b>
                </b>
                <br><br>
            @endif

            {{-- Second Row --}}
            @if ($i == 10)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 11)
                <b class="mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 12)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 13)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 14)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 15)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 15)
                <br><br>
            @endif
            {{-- End second Row --}}


            {{-- Third Row --}}
            @if ($i == 16)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 17)
                <b class="mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 18)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 19)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 20)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 21)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 21)
                <br><br>
            @endif
            {{-- End Third Row --}}

            {{-- Fouth Row --}}
            @if ($i == 22)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 23)
                <b class="mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 24)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 25)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 26)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 27)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 27)
                <br><br>
            @endif
            {{-- End Fouth Row --}}

            {{-- Fifth Row --}}
            @if ($i == 28)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 29)
                <b class="mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 30)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 31)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 32)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 33)
                <b class=" mvl" style="margin-left:5.8%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i == 34)
                <br><br>
                <b style=" margin-left:53%">@include('space.FloorPlans.BoothFormat')</b>
                <br><br>
            @endif
            {{-- End Fifth Row --}}


            {{-- Six Row --}}

            @if ($i == 35)
                <b style="margin-right: 2%">
                    <i class="icon-home" style="font-size:3vmax"></i>
                </b>
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif
            @if ($i >= 36 && $i <= 39)
                <b>@include('space.FloorPlans.BoothFormat')</b>
            @endif

            @if ($i == 40)
                <b class="mvl text-danger" style="font-size:1vmax"> Exit <i class="icon-arrow-down16"></i> </b>
                <b class="mvl" style="margin-left:5%">@include('space.FloorPlans.BoothFormat')</b>
            @endif
            {{-- End Fifth Row --}}

        @endforeach
    </div>
</div>
