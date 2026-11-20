
<style>
    #logo{
    font-size:250%; padding:5%; text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
    text-align: center;
}
</style>

<!-- Main navbar -->
<div class=" navbar navbar-default navbar-fixed-top header-highlight" style="background-color: rgb(16, 140, 230);">
    <div class="navbar-header" style="background-color: rgb(16, 140, 230);">
        <a class="navbar-brand text-bold text-white" href="{{ route('startApp') }}" id="h1">SAMS</a>
        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class=" icon-user"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse row" id="navbar-mobile">
        <ul class="nav navbar-nav ">
            <li><a class="sidebar-control sidebar-main-toggle hidden-xs text-white"><i class="icon-paragraph-justify3"></i></a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-grid6 text-white"></i>
                    <span class="visible-xs-inline-block position-right">System Modules</span>
                    
                </a>
                
                <div class="dropdown-menu dropdown-content">
                    <div class="dropdown-content-heading">
                        System Modules
                        <ul class="icons-list">
                            <li><a href="#"><i class="icon-sync"></i></a></li>
                        </ul>
                    </div>

                    <ul class="media-list dropdown-content-body width-350">
                        
                        <li class="media">
                            <div class="media-left">
                                <a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm"><i class=" icon icon-certificate"></i></a>
                            </div>

                            <div class="media-body" title="click to shift Module">
                                 <a href="{{ route('dashboard') }}">{{ config('global.MODULE_ONE') }}
                                <div class="media-annotation"> {{ config('global.MODULE_ONE_DESCRIPTION') }}</div>
                            </a>
                            </div>
                        </li>

                        <li class="media">
                            <div class="media-left">
                                <a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm"><i class=" icon icon-certificate"></i></a>
                            </div>

                            <div class="media-body" title="click to shift Module">
                                 <a href="{{ route('permitDashboard') }}">{{ config('global.MODULE_TWO') }}
                                <div class="media-annotation"> {{ config('global.MODULE_TWO_DESCRIPTION') }}</div>
                            </a>
                            </div>
                        </li>
                    </ul>

                    <div class="dropdown-content-footer">
                        <a href="{{ route('startApp') }}" data-popup="tooltip" title="This shows the list of all Modules availabe in this system."><i class="icon-menu display-block"></i></a>
                    </div>
                </div>
            </li>
        </ul>
       

        {{-- header profile collupse --}}
        <div class=" ">
            <ul class="nav navbar-nav navbar-right">
                <style>
                    #name:hover{
                        color: black;
                    }

                </style>
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
