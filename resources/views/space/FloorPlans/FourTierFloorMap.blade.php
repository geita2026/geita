<div class=" scrollable-row" style="width:1880px;text-align: center; border:1px solid rgb(196, 193, 193);padding:2%">

    <div style="border:3px dashed rgb(70, 69, 69); padding:0.5%;width:1690px; text-align:center">
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
            @if ($i <= 16)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 16)
                <br><br>
                <b class=" text-danger" style="font-size:1vmax">
                    Entrance <i class=" icon-arrow-right16"></i>
                    <i class=" icon-rotate-ccw2" style=" margin-left:80%;font-size:1.8vmax"></i>
                </b>
                <br><br>
            @endif

            {{-- Second Row --}}
            @if ($i >= 17 && $i <= 32)
                @include('space.FloorPlans.BoothFormat')
            @endif


            {{-- Third Row --}}

            @if ($i == 32)
                <br><br>
            @endif

            @if ($i >= 33 && $i <= 48)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 48)
                <br><br>
                <b class=" text-danger" style="font-size:1vmax">
                    Entrance <i class=" icon-arrow-right16"></i>
                    <i class=" icon-rotate-ccw2" style=" margin-left:80%;font-size:1.8vmax"></i>
                </b>
                <br><br>
            @endif

            {{-- Forth row --}}
            @if ($i >= 49 && $i <= 64)
                @include('space.FloorPlans.BoothFormat')
            @endif
        @endforeach
        </div>
    </div>
