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
                <br>
            @endif

            {{-- Second Row --}}
            
            @if ($i >= 17 && $i <= 28)
                @include('space.FloorPlans.BoothFormat')
            @endif
            


            {{-- Third Row --}}

            @if ($i == 28)
                <br><br><br>
            @endif

            @if ($i >= 29 && $i <= 44)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 44)
                <br><br>
                <br>
            @endif

            {{-- Forth row --}}
            @if ($i >= 45 && $i <= 52)
                @include('space.FloorPlans.BoothFormat')
            @endif

             @if ($i == 52)
                <br><br><br>
            @endif

            @if ($i >= 53 && $i <= 60)
                @include('space.FloorPlans.BoothFormat')
            @endif

             @if ($i == 60)
                <br><br><br>
            @endif

            @if ($i >= 61 && $i <= 68)
                @include('space.FloorPlans.BoothFormat')
            @endif

             @if ($i == 68)
                <br><br><br>
            @endif

            @if ($i >= 69 && $i <= 84)
                @include('space.FloorPlans.BoothFormat')
            @endif

             @if ($i == 84)
                <br><br><br>
            @endif

            @if ($i >= 85 && $i <= 100)
                @include('space.FloorPlans.BoothFormat')
            @endif

             @if ($i == 100)
                <br><br><br>
            @endif
            @if ($i >= 101 && $i <= 116)
                @include('space.FloorPlans.BoothFormat')
            @endif

            

        @endforeach
        </di>
    </div>
