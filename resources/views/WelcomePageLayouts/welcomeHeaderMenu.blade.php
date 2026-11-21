<style>
    /* .dropdown:hover .dropdown-menu {
        display: block;
    } */

    .innerLink {
        background-color: rgb(103, 202, 209);

    }

    .menuBtn:hover {
        background-color: rgb(103, 202, 209);
        color:black;
    }
</style>
<div class=" col-md-12">
    <div class=" row bg-primary-600" style="background-color:rgb(96, 97, 96);padding:1%">
        <div class=" col-md-10  text-bold text-uppercase ">
            EVENT STARTS FROM: <b style="text-right" class=" text-white ">{{ date('d M, Y', strtotime('28-06-2024')) }} TO
                {{ date('M d, Y', strtotime('13-07-2024')) }}
        </div>
        <div class=" col-md-2" >
            <div id="google_translate_element">
                
            </div>
        </div>
    </div>

    <div class=" row">
        <div class=" text-left col-xs-2"><img src="{{ asset('images/nembo.png') }}" height="60" width="70"
                class=" img-rounded"></div>
        <div class=" text-center col-xs-8">
            <b style="text-transform: uppercase;" class=" text-primary">
                <b style="font-size:3vmin;">TANZANIA TRADE DEVELOPMENT AUTHORITY</b><br>
                {{-- <img src="{{ asset('images/new-animated.gif') }}" height="20"
        width="20"> --}}
                <b style="font-size:2vmin">GeMEMIS - Geita Mineral Exhibition Management Information System</b>
            </b>
        </div>
        <div class=" text-right col-xs-2"><img src="{{ asset('images/Logo.png') }}" height="60" width="70"
                class="img-rounded"></div>
    </div>

    <!-- Default navbar size -->
    <div class="navbar navbar-inverse  navbar-component row bg-primary" id="navbar-second">
        <div class="navbar-header ">
            {{-- navbar-brand --}}
            <ul class="nav navbar-nav no-border visible-xs-block">
{{-- 
                <li class="  text-bold menuBtn {{ Route::is('registration') ? 'active' : '' }} "><a
                        href="{{ route('registration') }}" class="">Register</a></li> --}}
                        
                        <li class="  menuBtn"><a style="font-size: 2vmax"> <b>NaneNane - {{ date('Y') }}</b></a></li>
                        <li class="  text-bold menuBtn"><a class="text-center collapsed" data-toggle="collapse"
                        data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
            </ul>
        </div>



        <div class="navbar-collapse collapse text-bold " id="navbar-second-toggle">
            <ul class="nav navbar-nav">
                @include('layouts.Links')
                
            </ul>
            
        </div>
    </div>
    <!-- default navbar size -->
</div>


<script>
    // Set the date we're counting down to
    var countDownDate = new Date("Jun 28, 2024 00:00:00").getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="timerClock"
        document.getElementById("timerClock").innerHTML = days + "d " + hours + "h " +
            minutes + "m " + seconds + "s ";

        // If the count down is over, write some text 
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("timerClock").innerHTML = "EVENT STARTED";
        }
    }, 1000);
</script>
