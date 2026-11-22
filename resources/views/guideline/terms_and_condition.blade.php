
<x-guest-layout>
    <div class="content">
        <div class="container-fluid" style="padding:25px 15px;">

            <div class="card"
                 style="border:0; border-radius:12px;
                 box-shadow:0 4px 20px rgba(0,0,0,0.10);
                 overflow:hidden;">

                <!-- HEADER -->
                <div style="background:#171717;
                            padding:25px;
                            border-bottom:5px solid #C9A34A;">

                    <div class="row">
                        <div class="col-md-8">
                            <h3 style="color:white; margin:0;
                                       font-weight:bold;">
                                GeMEMIS
                            </h3>

                            <p style="color:#C9A34A; margin:8px 0 0;">
                                TERMS AND CONDITIONS OF PARTICIPATION
                            </p>
                        </div>

                        <div class="col-md-4 text-right">
                            <a href="{{ url()->previous() }}"
                               class="btn"
                               style="background:#C9A34A;
                                      color:#171717;
                                      font-weight:bold;">
                                <i class="icon icon-arrow-left12"></i>
                                Go Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div style="padding:25px;">

                    <div class="alert"
                         style="background:#F8F5ED;
                                border-left:5px solid #C9A34A;
                                color:#333;">

                        <i class="icon icon-info22"></i>

                        You are required to read, understand and
                        accept the terms of participation before
                        proceeding with your booking.
                    </div>

                    <!-- PDF ACTIONS -->
                    <div class="row form-group">

                        <div class="col-md-12 text-right">

                            <a href="{{ asset('documents/GeMEMIS_General_Participation_Criteria.pdf') }}"
                               target="_blank"
                               class="btn btn-default"
                               style="border:1px solid #C9A34A;">
                                <i class="icon icon-file-pdf"></i>
                                Open PDF
                            </a>

                            <a href="{{ asset('documents/GeMEMIS_General_Participation_Criteria.pdf') }}"
                               download
                               class="btn"
                               style="background:#171717;
                                      color:white;">
                                <i class="icon icon-download"></i>
                                Download PDF
                            </a>

                            <button type="button"
                                    onclick="document.getElementById('termsPdf').contentWindow.print();"
                                    class="btn"
                                    style="background:#C9A34A;
                                           color:#171717;">
                                <i class="icon icon-printer"></i>
                                Print PDF
                            </button>

                        </div>
                    </div>

                    <!-- PDF VIEWER -->
                    <div style="border:1px solid #ddd;
                                border-radius:8px;
                                overflow:hidden;">

                        <iframe
                            id="termsPdf"
                            src="{{ asset('documents/GeMEMIS_General_Participation_Criteria.pdf') }}"
                            width="100%"
                            height="800"
                            style="border:0; display:block;"
                            loading="lazy">
                        </iframe>

                    </div>

                    <p class="text-center"
                       style="margin-top:15px; color:#777;">
                        GeMEMIS | Official Participation Guidelines
                    </p>

                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
