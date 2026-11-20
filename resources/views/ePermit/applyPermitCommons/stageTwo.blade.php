<div class="tab-pane @if ($item->applicationStatus >= 2 && $item->applicationStatus <= 4) active @endif" id="stageTwo">
    
    <div class=" col-md-12">
        <div class="alert alert-info alert-styled-left alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Heads up!</span> This application will pass five stages before you get your Permit, Once Application meet all requirement you will be notified on your number <b>({{ $item->companyPhone }})</b>  to proceed with payments. If you don't have access with this number please <a href="{{ route('company_profile',['companyId'=>$item->registrationID]) }}">click here</a> to update Company Number.
        </div>
        
    </div>
        <li class=" icon-seven-segment-1">. <b >APPLICATION SUBMITED
                FOR REVIEW</b> 
                @if ($item->applicationStatus > 1)<i class=" icon-check text-success"></i>@endif
            </li>
            <br><br>
        <li class=" icon-seven-segment-2">. 
            <b >APPLICATION REVIEWED
                </b>
                @if ($item->applicationStatus >= 3)<i class=" icon-check text-success"></i>@endif
            </li><br><br>
        <li class=" icon-seven-segment-3">. <b >APPLICATION
                VERIFIED</b>@if ($item->applicationStatus >= 4)
                <i class=" icon-check text-success"></i>@endif
            </li>
            <br><br>
        <li class=" icon-seven-segment-4">. 
            <b>APPLICATION APPROVAL</b>
            @if ($item->applicationStatus >= 5)<i class=" icon-check text-success"></i>@endif
        </li>
        <br><br>
        <li class=" icon-seven-segment-5 text-uppercase">. <b>Permit granted</b> @if ($item->applicationStatus >= 6)<i class=" icon-check text-success"></i>@endif
        </li><br>
    

</div>
