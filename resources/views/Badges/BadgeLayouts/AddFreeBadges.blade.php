
    @foreach($companies as  $item)
{{-- Add free badges modal --}}
<div id="AddFreeBadges" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title  text-uppercase text-center"> ADD FREE BADGES TO <b>{{ $item->companyName }}</b></h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('badge_allocations.update',['badge_allocation'=>$item->companyID]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class=" form-group">
                        <b>CURRENT FREE BADGES: {{ $freeBadges }}</b>
                    </div>
                    <div class=" form-group">
                        <label>Number Of badges<i class=" text-danger">*</i></label>
                        <input type="number" name="free_badges" class=" form-control" value="{{ old('free_badges') }}" min="1">
                    </div>
                    <div class=" form-group">
                        <label> Reasons for adding Free badges<i class=" text-danger">*</i></label>
                        <textarea name="reasons" class=" form-control" placeholder="Reasons for adding free badges">{{ old('reasons') }}</textarea>
                    </div>
                    <div class=" form-group">
                        <label>Any Attachment as Approval (Optional)</label>
                        <input type="file" name="attachment" class=" form-control">
                    </div>

                    <div class=" form-group text-right">
                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                        <button type="submit" class="btn btn-primary ml-20" >Add Free Badges</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Add free badges modal --}}
@endforeach

