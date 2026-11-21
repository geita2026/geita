<div id="containner" style="border-radius:5px; border:1px solid rgb(224, 228, 233); font-family:monospace; ">
    <div style="background-color: rgb(224, 228, 233);text-align:center;">
        <a href="{{ url(env('APP_URL')) }}" style="text-decoration: none;">
        <div style="text-align: center; padding:1%">
            <h3>SPACE ALLOCATION MANAGEMENT SYSTEM (SAMS)</h3>
        </div>
        </a>

    </div>
    <div style="padding:4%;background-color: rgb(255, 255, 255);">
        <p>Dear <b style="color:blue">{{ $name ?? '' }}</b>,<br>
            your password is:
             <input type="text" value="{{ $password }}" id="myInput">
        </p>

    <p>If you did not request new password , No futher action is required.</p>
    <p>
        Regard,<br>
        SAMS - Support Team.
    </p>
    </div>

    <div class=" footer" style="background-color: rgb(224, 228, 233); padding:4%; text-align:center;">
        &copy;{{ date('Y') }} <a href="{{ url(env('APP_URL')) }}" style="color:blue;text-decoration: none;">GeMEMIS.</a> All Rights reserved.
    </div>
    </div>
