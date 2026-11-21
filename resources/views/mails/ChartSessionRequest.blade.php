<div id="containner" style="border-radius:5px; border:1px solid rgb(224, 228, 233); font-family:monospace; ">
    <div style="background-color: rgb(224, 228, 233);text-align:center;">
        <a href="{{ url(env('APP_URL')) }}" style="text-decoration: none;">
        <div style="text-align: center; padding:1%">
            <h3>GeMEMIS - Geita Mineral Exhibition Management Information System</h3>
        </div>
        </a>

    </div>
    <div style="padding:4%;background-color: rgb(255, 255, 255); font-size:120%;">
        <p><b style="color:blue">{{ $CompanyName }}</b>, Is welcoming you to new chart session through the link bellow</p>

    <p>
        {{ $chartLink }}
    </p>

    <p >If you did'nt Request chart session, No futher action is required and we are sory for any  inconvenience.</p>
    <p>
        Regard,<br>
        GeMEMIS SUPPORT TEAM
    </p>
    </div>

    <div class=" footer" style="background-color: rgb(224, 228, 233); padding:4%; text-align:center;">
        &copy;{{ date('Y') }} <a href="{{ url(env('APP_URL')) }}" style="color:blue;text-decoration: none;">GeMEMIS.</a> All Rights reserved.
    </div>
    </div>
