 <form action="{{ route('prepare_badges.show',['badge']) }}" method="POST">
    @csrf
    @method('GET')
    <div class=" row">
        <div class=" form-group col-md-8">
            <label>Select Company</label>
            <div class="multi-select-full">
           <select name="company_id[]"  class="multiselect-filtering" multiple="multiple"  data-width="100%">
                   @foreach ($companies as $company)
                   @php $i=0; $a=0; @endphp
                   @foreach ($members as $member)

                   @if($member->company_id==$company->companyID)
                   @php $i++; @endphp

                   {{-- UNPRINTED BADGES --}}
                   @if ($member->badge_status=='UNPRINTED')
                   @php $a++; @endphp
                   @endif
                   {{-- END UNPRINTED BADGES --}}

                   @endif

                   @endforeach

                   {{-- @if($i>0) --}}
                   <option value="{{ $company->companyID}}" class=" text-bold" ><i class=" text-uppercase">{{ $company->companyName }}</i> - [ {{ $a }} / {{ $i }} ]</option>
                   {{-- @endif --}}

                   @endforeach
           </select>
            </div>

        </div>

        <div class=" form-group col-md-4">
            <label>Badge Status <i class=" text-danger">*</i></label>
           <select name="badge_status" class="bootstrap-select" data-live-search="true" data-width="100%" required>
                <option value="UNPRINTED">UnPrinted Badges</option>
                <option value="PRINTED">Printed Badges</option>
           </select>
        </div>

       </div>
    <div class=" col-md-12 text-right">
        <button type="submit" class="btn btn-primary">Load Badges</button>
    </div>
</form>


{{-- <div class=" row col-md-12 form-group bg-info-400">
<p>
<div class=" col-md-6 form-group">
    <div class=" col-md-10">
        <a data-toggle="modal" data-target="#filter_badges" class=" btn btn-default border-primary">Filter <i class=" icon-filter3"></i></a>
    </div>
</div>
</p>

</div> --}}

