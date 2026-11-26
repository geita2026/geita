
@php
$booths=App\Models\booking\booked_booths::where('userID',Auth::user()->user_uuid)->where('advice_note_id',Null)->count();
$space=App\Models\booking\booked_pavilions::where('userID',Auth::user()->user_uuid)->where('advice_note_id',Null)->count();
$services=App\Models\advice_note\service_charges::where('user_id',Auth::user()->user_uuid)->where('advice_note_id',Null)->count();
$cat=$booths+$space+$services;
@endphp
{{--  --}}

<div class="page-header">
    <div class="page-title">
        {{-- <h4 class=" text-bold text-center"> GeMEMIS - Geita Mineral Exhibition Management Information System</h4> --}}
    </div>
    <div class="heading-elements">
        <div class="heading-btn-group">
           @if (session('moduleNo', 1)==1)
           @can('BOOK_SPACE')
           <a href="{{ route('booking.index') }}" class="btn btn-link btn-float has-text"><i class=" icon-cart-add text-primary"></i><span>Book Space</span></a>
           <a href="{{ route('booking.edit',['booking']) }}" class="btn btn-link btn-float has-text"><i class=" icon-cart5 text-primary"></i> <span class=" text-warning text-bold">{{ $cat }}</span></a>
              
           @endcan
           @endif
        </div>
    </div>
</div>
