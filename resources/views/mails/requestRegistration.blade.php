
<div style="border-radius:5px; border:2px solid rgb(133, 182, 185); font-family: Serif;padding:2%;background-color:rgb(237, 243, 242);">
    <p style="font-size:1.2vmax;text-align:center;">
       <table style="width: 100%;">
        <thead>
            <tr>
                <th>
                    <img src="{{ $message->embed(public_path() . '/images/logo.jpg') }}" alt="" height="50"
                        width="60" style="text-align:right;">
                </th>
                <th>
                    <b>API AUTHENTICATION DETAILS</b>
                </th>
               
            </tr>
        </thead>
       </table>
       
    </p>

<div>
    <p>Hi, {{ $serviceProvider }}</p>
    <p style="margin-left: 2%">The following are the credentials used to access the Online Ticket System APIs</p>
    <p>BASE URL:  <a href="https://tradefair.tantrade.go.tz/api/v1/">https://tradefair.tantrade.go.tz/api/v1/</a></p>
    <p>SP CODE: <b>{{ $serviceProvider }}</b></p>
    <p>TOKEN:<b>  {{ $access_token }}</b></p>
    
    <div style="padding:2%;text-align:center">
        <a href="https://www.youtube.com/@tantradetanzania234">
            <img src="{{ $message->embed(public_path() . '/images/Youtube.png') }}" alt="" 
            height="30" width="30" style="padding:1%">
        </a>
        <a href="https://www.facebook.com/profile.php?id=100015800295473" style="padding:1%">
            <img src="{{ $message->embed(public_path() . '/images/Facebook.png') }}" alt="" 
            height="30" width="30" style="padding:1%">
        </a>
        <a href="https://www.instagram.com/tantradetz">
            <img src="{{ $message->embed(public_path() . '/images/Instagram.png') }}" alt="" 
            height="30" width="30" style="padding:1%">
        </a>
        <a href="https://twitter.com/TanTradepage">
            <img src="{{ $message->embed(public_path() . '/images/Twitter.png') }}" alt="" 
            height="30" width="30" style="padding:1%">
        </a>
    </div>
    <div style="color:rgb(95, 97, 99);font-size:1vmin;text-align:center;">
        You are receiving this email because you have request API service from TanTrade.
        If you received this email incorrectly, we are sorry for any inconvenience, and no action is required.
        <br>
        For more information visit our website
        <a href="https://www.tantrade.go.tz" style="text-decoration: none;"> https://www.tantrade.go.tz</a>
        <br>
        &copy; {{ date('Y') }} All rights reserved. <a href="www.tantrade.go.tz">TanTrade</a>.
    </div>
</div>
</div>


