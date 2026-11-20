<div id="containner" style="border-radius:5px; border:1px solid rgb(224, 228, 233); font-family:monospace; ">
    <div style="background-color: rgb(224, 228, 233);text-align:center;">
        <a href="{{ url(env('APP_URL')) }}" style="text-decoration: none;">
        <div style="text-align: center; padding:1%">
            <h3>SPACE ALLOCATION MANAGEMENT SYSTEM (SAMS)</h3>
        </div>
        </a>

    </div>
    <div style="padding:4%;background-color: rgb(255, 255, 255); font-size:120%;">
        <p>Welcome <b style="color:blue">{{ $name }}</b>,<br></p>
    <p>
        We are proud to have you for the first time as a new customer on our system, to login please use bellow details.
        <li>User Name: your registered email</li>
        <li>Password: <b>{{ $password }}</b></li>
    </p>
    </div>
    @include('mails.MailFooterLayout')
    </div>
