<!-- Main navbar -->
<div class="navbar navbar-default navbar-fixed-top header-highlight gememis-navbar">
    <div class="navbar-header">
        <a class="navbar-brand gememis-brand" href="{{ route('dashboard') }}" aria-label="GeMEMIS home">
            <img src="{{ asset('images/coat-of-arms.svg') }}" alt="">
            <span>GeMEMIS</span>
        </a>
        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class=" icon-user"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav ">
            <li><a class="sidebar-control sidebar-main-toggle hidden-xs text-white"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
       

        {{-- header profile collupse --}}
        <div class=" ">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown dropdown-user" id="profile">
                    <a class="dropdown-toggle text-bold" data-toggle="dropdown">
                        @if(empty(Auth::user()->profile_image))
                        <img src="{{ asset('assets/images/placeholder.jpg') }}">
                        @else
                        <img src="{{ asset('profile_images/'.Auth::user()->profile_image) }}">
                        @endif
                    <span class=" text-black">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        </span>
                        <i class="caret "></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('profiles.index')}}"><i class="icon-user-plus"></i> My profile</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('profiles.show',['profile'])}}"><i class=" icon-user-lock"></i> Change Password</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('logout') }}" onclick=" event.preventDefault();document.getElementById('form').submit();"><i class="icon-switch2"></i> Logout </a>
                            <form action="{{ route('logout') }}" method="post" id="form">@csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        {{-- header profile collupse --}}
    </div>
</div>
<!-- /main navbar -->
