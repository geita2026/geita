<!-- Second navbar -->
<div class="navbar navbar-default" id="navbar-second">
    <ul class="nav navbar-nav no-border visible-xs-block">
        <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
    </ul>

    <div class="navbar-collapse collapse" id="navbar-second-toggle">

        <ul class="nav navbar-nav">
            <li ><a href="{{ route('About_ditf') }}" class=" link-info"><i class="position-left"></i>About DITF</a></li>
            <li class=" ml-10"><a href="{{ route('discover_exhibitor.index') }}"><i class="position-left"></i>Discover Exhibitors</a></li>
        </ul>




        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="{{ route('index') }}">
                    <span class="label label-inline position-right bg-warning"><b id="h3">Book Space Now <i class=" icon-cart-add position-left"></i></b></span>

                </a>
            </li>

        </ul>

        {{-- <ul class="nav navbar-nav">

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class=" icon-earth position-left"></i>Business Network <span class="caret"></span>
                </a>
                <ul class="dropdown-menu width-200">
                    <li><a href="#"><i class=" icon-collaboration"></i> Exprole Business Networks</a></li>
                    <li><a href="#"><i class="icon-flip-vertical3"></i> Attend B2B Meetings</a></li>
                </ul>
            </li>
        </ul> --}}

    </div>
</div>
<!-- /second navbar -->
