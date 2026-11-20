<div class=" col-md-12 form-group">

    @php
        $i = 0;
    @endphp
    @foreach ($balanceInvoice as $balance)
        @php
            $i++;
        @endphp
    @endforeach


    @if ($i > 0)
        <div class=" form-group col-md-12">
            <p class=" alert alert-warning">You have unpaid invoice to your Account, Pay for it to get slots for
                uploading new badges<br>
                @foreach ($balanceInvoice as $balance)
                    <a href="{{ route('adviceNotes.show', ['adviceNote' => $balance->advice_note_id]) }}"
                        class=" btn btn-default border-warning">{{ $balance->advice_note_id }}
                        <b>({{ number_format($balance->balance, 2) }} {{ $balance->currency }})</b> </a>
                @endforeach
            </p>

        </div>
    @elseif (session('availableBadges') > 0 || session('companyID')==config('global.COMPANY_EXCEPTION'))
        {{-- ALLOCATION COUNT --}}
        <div class="row col-md-12">
            <div class=" col-md-10 text-center" style="font-size: 1vmax">
                <b class=" bg-warning p-10" title="Used Badge Slotes">
                    USED: {{ session('usedBadges') }}
                </b>
                <b class=" bg-success p-10" title="Free Badge Slotes">
                    AVAILABLE: {{ session('availableBadges') }}
                </b>
                <b class=" bg-primary p-10" title="Total Booked Slotes">
                    ALLOCATED: {{ session('totalAllocatedBadges') }}
                </b>
            </div>
            @if ($previousBadges > 0)
                <div class="col-md-2 text-center">
                    <a class=" btn btn-primary "
                        href="{{ route('reuse_badges.show', ['reuse_badge' => session('companyID')]) }}">
                        Reuse {{ number_format($previousBadges) }}</b> Badge from Privious Events 
                        <i class=" icon-arrow-right13"></i></a>
                </div>
            @else
                <div class=" form-group col-md-2 text-center">
                    <button type="submit" class=" btn btn-default border-primary text-primary" data-toggle="modal"
                        data-target="#addBadge">Add Exhibition Badge <i class=" icon-plus2"></i></button>
                </div>
            @endif

        </div>
        {{-- END ALLOCATION COUNT --}}

        {{-- Add new Member Badges --}}
    @elseif (session('bookedBadges') == 0)
        <div class=" form-group col-md-12">
            <p class=" alert alert-warning">Ooops! Sory you haven't booked badges, Please <a
                    href="{{ route('book-services.index') }}" class=" text-bold">Click Here</a> to book new badges and
                pay for it
                to get slots.</p>

        </div>
    @elseif (session('availableBadges') <= 0)
        <div class=" alert alert-warning col-md-12">All badges have been used up, please <a
                href="{{ route('book-services.index') }}" class=" text-bold">Click here</a> to book new Badge
            to get new slots.</div>
    @endif
</div>
