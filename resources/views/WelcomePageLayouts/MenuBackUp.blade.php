<style>
    .dropdown:hover .dropdown-menu {
    display: block;
}
</style>
<div class=" col-md-12 fixed-top">
 
    <div class=" row bg-primary-600" style="background-color: black;padding:1%">
        <div class=" col-md-12  text-bold text-uppercase text-right" >  
          <b class=" text-white ">{{ date('M d', strtotime('28-06-2024')) }} - {{ date('M d, Y', strtotime('13-07-2024')) }} | <b id="timerClock"></b> </b>
        
        </div>
    </div>

    <div class=" row ">
     <div class=" text-left col-xs-2"><img src="{{ asset('images/nembo.png') }}" height="60" width="70" class=" img-rounded"></div>
     <div class=" text-center col-xs-8">
    <b style="text-transform: uppercase;">
        <b style="font-size:3vmin">GEITA MINERAL EXHIBITION SECRETARIAT</b><br>
        <b style="font-size:2vmin">GeMEMIS - Geita Mineral Exhibition Management Information System</b>
    </b></div>
     <div class=" text-right col-xs-2"><img src="{{ asset('images/Logo.png') }}" height="60" width="100" class="img-rounded"></div>
    </div>

    <!-- Default navbar size -->
         <div class="navbar navbar-inverse  navbar-component row " style="background-color:rgba(255, 255, 255, 0.007);border:1px solid rgba(0, 0, 0, 0);font-size:2vmin">
             <div class="navbar-header col-md-2 p-10">
                 {{-- navbar-brand --}}
                 <ul class="nav navbar-nav pull-right visible-xs-block">
                     {{-- <li><a data-toggle="collapse" data-target="#navbar-size-base"><i class=" icon-menu3"></i></a></li> --}}
                 </ul>

             </div>

             <div class="navbar-collapse collapse text-bold col-md-10 text-center " id="navbar-size-base">
                 <ul class="nav navbar-nav ">

                     <li class="dropdown btn">
                         <a href="#" class="dropdown-toggle text-bold text-primary" data-toggle="dropdown" style="font-size: 1.8vmin">About DITF <span class="caret"></span></a>
                         <ul class="dropdown-menu dropdown-menu-left">
                             <li><a href="{{ route('About_ditf') }}"> <i class=" icon-arrow-right13"></i> Understanding DITF</a></li>
                             <li><a href="{{ route('experience.index') }}"><i class=" icon-arrow-right13"></i> Our Exprience</a></li>
                             <li><a href="{{ route('catalogy.index') }}"><i class=" icon-arrow-right13"></i>DITF Catalogy</a></li>
                             <li><a href="{{ route('preview_programs.index') }}"><i class=" icon-arrow-right13"></i> Event Programs</a></li>
                         </ul>
                     </li>
                     <li class="btn text-bold "><a href="{{ route('ditf_map') }}" class="text-primary" style="font-size: 1.8vmin"> DITF Map</a></li>
                     <li class=" btn text-bold "><a href="{{ route('catalogy.index') }}" class="text-primary" style="font-size: 1.8vmin"> DITF Catalogy</a></li>
                     {{-- <li class=" btn text-bold"><a href="{{ route('delegate_profiles.index') }}" target="_tab">Delegation Profiles</a></li> --}}
                     {{-- <li class=" btn text-bold text-primary" data-toggle="modal" data-target="#login"><a class="text-primary" style="font-size: 1.8vmin"> Login </a></li> --}}
                     {{-- <li class=" btn text-bold" data-toggle="modal" data-target="#NewAccount"><a><i class=" icon-user-plus"></i> REGISTER </a></li> --}}

                 </ul>
             </div>
         </div>
    <!-- /default navbar size -->
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
  document.getElementById("timerClock").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s ";
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("timerClock").innerHTML = "EVENT STARTED";
  }
}, 1000);
</script>