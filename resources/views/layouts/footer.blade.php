<div class="navbar navbar-default navbar-fixed-bottom bg-secondary" style="color:white">
    <ul class="nav navbar-nav no-border visible-xs-block">
        <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second"><i class="icon-circle-up2"></i></a></li>
    </ul>

    <div class="navbar-collapse collapse" id="navbar-second">
        <div class="navbar-text">
            Copyright &copy; {{ date('Y') }}. Geita Mineral Exhibition Management Information System (GeMEMIS).
        </div>

        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <li><a class=" text-bold text-muted"> {{ config('global.APP_VERSION') }}</a></li>
            </ul>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/pages/components_popups.js') }}"></script>

