<div class=" scrollable-row"
    style="width:3000px;text-align: center; border:1px solid rgb(196, 193, 193);padding:2%; height:800px">
    @php
        $i = 1;
    @endphp

    <div style="border:3px dashed rgb(170, 169, 169); padding:0.5%">
        @foreach ($booths as $stand)
            {{-- First Row --}}
            @if ($i <= 15)
                @include('space.FloorPlans.BoothFormat')
            @endif

            @if ($i == 15)
                <br><br>
                <br> <br>
            @endif

            {{-- Second Row --}}
            @if ($i >= 16 && $i <= 26)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 26)
                <br><br>
            @endif

            {{-- Third Row --}}

            @if ($i >= 27 && $i <= 37)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 37)
                <br><br><br><br>
            @endif

            {{-- Forth row --}}
            @if ($i >= 38 && $i <= 48)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 48)
                <br><br>
            @endif
            {{-- Five Row --}}

            @if ($i >= 49 && $i <= 73)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 73)
                <br><br><br><br>
            @endif

            {{-- Six Row --}}
            @if ($i >= 74 && $i <= 98)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 98)
                <br><br>
            @endif

            {{-- Seven Row --}}
            @if ($i >= 99 && $i <= 123)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 123)
                <br><br><br><br>
            @endif

            {{-- Eight Row --}}
            @if ($i >= 124 && $i <= 148)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 148)
                <br><br>
            @endif
            {{-- Ten row --}}
            @if ($i >= 149 && $i <= 173)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 173)
                <br><br><br><br>
            @endif

            {{-- Eleven Row --}}
            @if ($i >= 174 && $i <= 198)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 198)
                <br><br>
            @endif
            @if ($i >= 199 && $i <= 222)
                @include('space.FloorPlans.BoothFormat')
            @endif
            @if ($i == 223)
                <br><br>
            @endif




            @php
                $i++;
            @endphp
        @endforeach
    </div>
</div>
