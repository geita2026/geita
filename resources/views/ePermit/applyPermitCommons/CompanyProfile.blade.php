
    <div class="media-link cursor-pointer" data-toggle="collapse" data-target="#james">
        <div class="media-left">
            
            @if ($item->companyLogo ?? false)
            <img src="{{ asset('profile_images/'.$item->companyLogo ?? '') }}" class="img-circle" alt="">
            @else
            <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle" alt="">
            @endif
        </div>
        <div class="media-body">
            <div class="media-heading text-bold">{{ $item->companyName }}</div>
            <span class="text-muted text-bold">{{ $item->registrationID }}</span>
        </div>
        <div class="media-right media-middle text-nowrap">
            <i class="icon-menu7 display-block"></i>
        </div>
    </div>

    <div class="collapse" id="james">
        @include('ePermit.applyPermitCommons.CompanyDetails')
    </div>
