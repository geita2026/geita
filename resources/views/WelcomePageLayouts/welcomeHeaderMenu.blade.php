<style>
    .welcome-header-brand {
        background: #070506;
        border-bottom: 1.5px solid rgba(255, 191, 0, 0.25);
        padding: 12px 0;
        margin-bottom: 24px;
    }
    .welcome-top-bar {
        background: rgba(255, 191, 0, 0.12);
        border-bottom: 1px solid rgba(255, 191, 0, 0.2);
        padding: 8px 16px;
        color: #FFBF00;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.05em;
    }
</style>

<div class="col-md-12 p-0">
    <div class="welcome-top-bar d-flex justify-content-between align-items-center">
        <div>
            <i class="icon-calendar52 position-left"></i>
            GEITA MINERAL EXHIBITION
        </div>
        <div id="google_translate_element"></div>
    </div>

    <div class="welcome-header-brand">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xs-3 col-sm-2 text-left">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('images/coat-of-arms.svg') }}" alt="Court of Arms of the United Republic of Tanzania" height="58" width="auto">
                    </a>
                </div>
                <div class="col-xs-6 col-sm-8 text-center">
                    <span style="text-transform: uppercase; display:block;">
                        <strong style="font-size:16px;color:#ffffff;letter-spacing:0.04em;">GeMEMIS</strong><br>
                        <span style="font-size:13px;color:#FFBF00;font-weight:600;letter-spacing:0.08em;">Geita Mineral Exhibition Management Information System</span>
                    </span>
                </div>
                <div class="col-xs-3 col-sm-2 text-right">
                    <a href="{{ route('login') }}" class="btn-brand" style="padding:6px 16px;font-size:13px;">
                        <i class="icon-lock"></i> Sign in
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
