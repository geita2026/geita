<style>
    .watermark {
        position: fixed;
        top: 50%;
        left: 20%;
        font-size: 60px;
        transform: rotate(-30deg);
        /* Watermark font size */
        color: rgba(0, 0, 0, 0.06);
        /* Light, semi-transparent color */
        z-index: -1;
        /* Send it behind other content */
        pointer-events: none;
        /* Prevent interaction */
        white-space: nowrap;
        text-align: center;
        /* Prevent text from wrapping */
    }

    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .content {
        padding: 1px;
    }
</style>
@foreach ($permit as $item)
    <title>{{ $item->permitNo }}</title>

    <body>
        <div class="watermark">
            PERMIT - {{ $item->permitNo }}
        </div>
        <div class="content">
            {{-- @include('reports.reports_layouts.report_header') --}}
            <table style="width: 100%">

                @php
                    #Logo
                    $logo = public_path('images/logo.jpg');
                    $image = Image::make($logo);
                    $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
                    #end logo

                    #Nembo
                    $nembo = public_path('/images/nembo.png');
                    $image = Image::make($nembo);
                    $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
                    #End Nembo
                    // ***********************
                @endphp
                <thead>
                    <tr>
                        <th>
                            <img src="{{ $base64_nembo ?? '' }}" height="50" width="80">
                        </th>
                        <th style="text-align: center">
                            TANZANIA TRADE DEVELOPMENT AUTHORITY (TanTrade),<br>
                            P.O. BOX 5402, Dar Es Salaam, Tanzania,<br>
                            Email: info@tantrade.go.tz,<br>
                            Tel: +255 22 2850238, Fax: +255 22 2850539,
                        </th>
                        <th style=" text-align:center">
                            <img src="{{ $base64_logo ?? '' }}" height="50" width="120">
                        </th>
                    </tr>

                </thead>
            </table><br>
            <div style="border: 2px solid RGB(30, 181, 58);width:100%"></div>
            <div style="border: 2px solid RGB(252, 209, 22); width:100%"></div>
            <div style="border: 2px solid RGB(0, 0, 0);width:100%"></div>
            <div style="border: 2px solid RGB(0, 163, 221);width:100%"></div>
            
            
            <br>
            <div style=" text-align:center">
                <img
                    src="data:images/png;base64, {{ base64_encode(
                        QrCode::size(90)->generate(env('APP_URL') . '/verify/' . substr($item->applicationId, 0, 8)),
                    ) }} "><br>
                <b style="font-family: monospace;font-size:10px;letter-spacing: 5px;">{{ $item->permitNo }}</b>
            </div>

            

            <h2 style=" text-align:center"><b style="border-bottom: 1px solid black;">PERMIT TO HOLD TRADE PROMOTION
                    EVENT</b>
            </h2>
            <p>
                Section 5(1) of Tanzania Trade Development Authority Act 2009 as amended - <br>Permit No: <b
                    style="border-bottom:2px dashed black;">{{ $item->permitNo }}</b>
            </p>

            <b style="font-size: 19px;">PART A</b><br>
            <div style="font-size: 15px;">
                <p><b>TSRN: </b>
                    <l style="border-bottom: 2px dashed black;">{{ $item->registrationID }}</l>
                <p><b>Name Of Organizer: </b>
                    <l style="border-bottom: 2px dashed black;">{{ $item->companyName }}</l>
                </p>
                <p><b>Address: </b>
                    <l style="border-bottom: 2px dashed black;"> {{ $item->companyAddress }}</l>
                </p>
                <p><b>City / Country: </b>
                    <l style="border-bottom: 2px dashed black;">{{ $item->companyLocation }}</l>
                </p>
                <p><b>Name of Promotion Event: </b>
                    <l style="border-bottom: 2px dashed black;">{{ $item->eventTitle }}
                    </l>
                </p>
                <p><b>Date Of Event: </b>
                    <l style="border-bottom: 2px dashed black;"> From:
                        {{ date('M d, Y', strtotime($item->startFrom)) }} To
                        {{ date('M  d, Y', strtotime($item->endDate)) }}</l>
                </p>
                <p><b>Venue </b>
                    <l style="border-bottom: 2px dashed black;">{{ $item->venueAddress }}</l>
                </p>
                <p><b>Amount Paid: </b>
                    <l style="border-bottom: 2px dashed black;">{{ number_format($item->invoiceAmount, 2) }}
                        {{ $item->currency }}</l>
                </p>
            </div>
            <div>
                <b style="font-size: 19px">PART B</b><br>
                <l style="line-height: 30px;font-size:15px; text-transform:capitalize">Permision is hereby granted to
                    <b>{{ $item->companyName }}</b>
                    as the <b> {{ $item->promotionEventTypeName }}</b> of
                    <b> {{ $item->eventTitle }}</b> After fullfilment of the requirements
                    as stipulated in the Tanzania Trade Development Authority Act.
                </l>

            </div>


            <br>
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th style="text-align: center;">{{ date('l M d, Y', strtotime($item->DateOfIssue)) }}</th>
                        <th style="text-align:center;"><img
                                src="{{ asset('AuthorizersSignature/PermitAuthorizer.png') }}" height="40"
                                width="80"></th>
                    </tr>

                </thead>
                <tbody>

                    <tr>
                        <td style="text-align: center">Date Of Issue</td>
                        <td style="text-align: center">Signature Of TanTrade Authorized Personel</td>
                    </tr>
                </tbody>
            </table>


            <br>

            <i style="font-size:12px;"><b>NB: </b> This permit is for the single event/venue/organizer . It will be valid for the Date
                approved.</i>
        </div>
    </body>
@endforeach
