@can('approve-permit')
    <p class="text-justify">Over all decision of the SAC Officers</p>
    @if (Auth::user()->account_type == 1 && $item->permitStatus != 'UNSUBMITED')
        <form action="{{ route('iopermits.update', ['iopermit' => $item->permit_id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="source" value="4">
            <div class=" row">
                <div class=" col-md-3 form-group">
                    <i>Make final decision<i class="text-danger">*</i></i>
                    <select name="permitStatus" class=" form-control" required>
                        <option value="UNDER_REVIEW">UNDER_REVIEW</option>
                        <option value="APPROVED">APPROVED</option>
                    </select>
                </div>
                <div class=" col-md-9 form-group">
                    <textarea name="comment" required class="form-control" placeholder="Enter your comment here"></textarea>
                </div>
                <div class=" col-md-12 text-right form-group">
                    <button type="submit" class=" btn btn-primary">Save Decision<i
                            class=" icon-arrow-right13"></i></button>
                </div>
            </div>
        </form>
    @endif
@endcan
