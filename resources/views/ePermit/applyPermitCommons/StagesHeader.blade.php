<ul class="nav nav-pills nav-pills-toolbar nav-justified">
    <li class="zoom"><a href="{{ route('company_profile',['companyId'=>$item->registrationID]) }}"
        ><b style="font-size:18px;">Company Profile</b><br>
        <span class="">Manage Company Details</span>
    </a>
</li>

    <li @if ($item->applicationStatus == 1) class="active zoom" @endif><a href="#stageOne"
            data-toggle="tab"><b style="font-size:18px;">STAGE NO. 1</b><br>
            <span class="">Event informations [ FORM NO.1A ]</span>
        </a>
    </li>
    <li @if ($item->applicationStatus >= 2 && $item->applicationStatus <= 4) class="active zoom" @endif><a href="#stageTwo"
            data-toggle="tab">
            <b style="font-size:18px;">STAGE NO. 2</b><br>
            <span class="">Application Review and Recorrection</span>
        </a></li>

    <li @if ($item->applicationStatus == 5) class="active zoom" @endif><a
            href="#stageThree" data-toggle="tab">
            <b style="font-size:18px;">STAGE NO. 3</b><br>
            <span class="">Payments</span>
        </a>
    </li>

    <li @if ($item->applicationStatus == 6) class="active zoom" @endif><a href="#stageFour"
            data-toggle="tab">
            <b style="font-size:18px;">STAGE NO. 4</b><br>
            <span class="">Get Your Permit</span>
        </a>
    </li>
</ul>