<div class="tab-pane @if ($item->applicationStatus >= 6) active @endif" id="stageFour">
    <div class=" row p-5">
    </div>
    @if ($item->applicationStatus == 6)
        <div class=" col-md-10">
            <p class=" alert alert-info">
                Congraturation <b>{{ $item->companyName }}</b>
                Tanzania Trade Development Authority has issued you permit, with permit No:
                <b>{{ $item->permitNo }}</b> on <b>{{ date('M d,Y', strtotime($item->DateOfIssue)) }}</b>.<br>
                @foreach ($asignedReviewer as $value)
                    For any question and official help, contact <b>{{ $value->first_name ?? '' }}
                        {{ $value->last_name ?? '' }}
                        ({{ $value->phone_number ?? '' }})
                    </b>
                @endforeach

            </p>
        </div>
        <div class=" col-md-2 text-right">
            <a target="_newtab" href="{{ route('apply_permit.edit', ['apply_permit' => $item->applicationId]) }}"
                class=" btn btn-success">Download Permit <i class=" icon icon-file-download"></i></a>
        </div>
    @else
        <div class="alert alert-warning alert-styled-left alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span
                    class="sr-only">Close</span></button>
            <span class="text-semibold">You are almost to done! </span> Please Complete payment to get your Permit.
        </div>
    @endif
</div>
