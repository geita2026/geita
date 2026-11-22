<form action="{{ route('review_applications.update', ['review_application' => $item->applicationId]) }}" method="POST">
    @csrf
    @method('PUT')

    @if ($item->applicationStatus == 2 && Auth::user()->user_uuid == $item->asignedUser)
        @can('REVIEW_STAGE_TWO')
            <div class=" row">
                <div class=" col-md-4">
                    <label>Select Review Decision<i class=" text-danger">*</i></label>
                    <select name="decision" class=" form-control">
                        <option value=""> - - - Select Decision - - -</option>
                        <option value="5">APPLICATION APPROVED</option>
                        <option value="1">RETURN TO APPLICANT</option>
                        <option value="4">RETURN TO DTP</option>
                    </select>
                </div>
                <div class=" col-md-8">
                    <label>Review Comment<i class=" text-danger">*</i></label>
                    <textarea class=" form-control" name="comment" placeholder="Type your comment here" required>{{ $item->reviewComment }}</textarea>
                </div>
                <div class=" col-md-12">
                    <label></label>
                    <button class=" btn btn-primary btn-block btn-rounded btn-ladda btn-ladda-spinner">Save Review
                        Comment</button>
                </div>
            </div>
        @endcan
    @else
        <div class=" row">
            <div class=" col-md-12">
                @if ($item->applicationStatus == 1 && $item->reviewComment != null)
                    <b class=" bg-danger p-10">RETURNED TO CUSTOMER</b>
                @elseif ($item->applicationStatus == 2)
                    <b class=" bg-secondary p-10">UNDER REVIEW</b>
                @elseif ($item->applicationStatus == 3)
                    <b class=" bg-secondary p-10"> REVIEWED</b>
                @elseif ($item->applicationStatus == 4)
                    <b class=" bg-secondary p-10">VERIFIED</b>
                @elseif ($item->applicationStatus == 5)
                    <b class=" bg-success p-10">APPROVED</b>
                @elseif ($item->applicationStatus == 6)
                    <b class=" bg-success p-10">CERTIFICATE ISSUED</b>
                @endif

            </div>

        </div>
    @endif

</form>
<br><br>
<div class=" form-group">
    APPLICATION AT:
    @if ($item->applicationStatus == 2)
        <label class=" label bg-brown">REVIEW POINT</label>
    @elseif ($item->applicationStatus == 3)
        <label class=" label bg-info">VERIFICATION POINT</label>
    @elseif ($item->applicationStatus == 4)
        <label class=" label bg-primary">APROVAL POINT</label>
    @elseif ($item->applicationStatus == 5)
        <label class=" label bg-warning">PAYMENT POINT</label>
    @endif
    <p class=" alert alert-info">
        <b>Review Comment: </b> {{ $item->reviewComment }}
    </p>
</div>
