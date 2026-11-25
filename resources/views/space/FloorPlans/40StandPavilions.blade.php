<div class=" scrollable-row" style="width:1500px;text-align: center; border:1px solid rgb(196, 193, 193);padding:2%">

    <div style="border:3px dashed rgb(70, 69, 69); padding:0.5%;width:1200px; text-align:center">
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
            @if ($i <= 10)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 10)
                <br><br>
                <b class=" text-danger" style="font-size:1vmax">
                    Entrance <i class=" icon-arrow-right16"></i>
                    <i class=" icon-rotate-ccw2" style=" margin-left:80%;font-size:1.8vmax"></i>
                </b>
                <br><br>
            @endif

            {{-- Second Row --}}
            @if ($i >= 11 && $i <= 20)
                @include('space.FloorPlans.BoothFormat')
            @endif


            {{-- Third Row --}}

            @if ($i == 20)
                <br><br>
            @endif

            @if ($i >= 21 && $i <= 30)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 30)
                <br><br>
                <b class=" text-danger" style="font-size:1vmax">
                    Entrance <i class=" icon-arrow-right16"></i>
                    <i class=" icon-rotate-ccw2" style=" margin-left:80%;font-size:1.8vmax"></i>
                </b>
                <br><br>
            @endif

            {{-- Forth row --}}
            @if ($i >= 31 && $i <= 40)
                @include('space.FloorPlans.BoothFormat')
            @endif
        @endforeach
        </div>
    </div>
