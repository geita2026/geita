@foreach ($tickets as $item)
    @php
        #Logo
        $logo = public_path('images/logo.jpg');
        $image = Image::make($logo);
        $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #end logo

        #Nembo
        $nembo = public_path('images/nembo.png');
        $image = Image::make($nembo);
        $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #end Nembo

        #Location
        $location = public_path('/images/location.png');
        $image = Image::make($location);
        $location = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #End Location
        // ***********************

        #mark
        $used = public_path('/images/used.png');
        $image = Image::make($used);
        $used = 'data:image/png;base64,' . base64_encode($image->encode('png'));

        #End mark
        // ***********************

        #Nembo
        $paid = public_path('/images/paid.png');
        $image = Image::make($paid);
        $paid = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        #End Nembo
        // ***********************

        #Nembo
        $blocked = public_path('/images/blocked.jpeg');
        $blocked = Image::make($blocked);
        $blocked = 'data:image/png;base64,' . base64_encode($blocked->encode('png'));
        #End Nembo
        // ***********************

        if ($item->ticketStatus == 1) {
            $mark = $paid;
        } elseif ($item->ticketStatus == 0) {
            $mark = $used;
        } elseif ($item->ticketStatus == 2) {
            $mark = $blocked;
        }
    @endphp

    <style>
        table {
            width: 100%;
        }

        body {
            font-family: 'Arial';
        }

        .vipHeader {
            background-color: gold;
            border-radius: 2%;
            border: 1px solid black;
        }
    </style>

    <div style="height: 12cm">
        <div style="font-family:Arial, Helvetica, sans-serif; ">
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th>
                            <img src="{{ $base64_nembo }}" height="20" width="20">
                        </th>
                        <th>
                            <p style="font-size: 8px">
                                TANZANIA TRADE DEVELOPMENT AUTHORITY<br>
                                P.O. BOX 5402, Dar Es Salaam, Tanzania<br>

                                TIN:113-710-888 | VRN: 40-010307-T
                            </p>
                        </th>
                        <th>
                            <img src="{{ $base64_logo }}" height="20" width="20">
                        </th>
                    </tr>
                </thead>
            </table>
            <div style="border-bottom:3px solid blue;"></div>
            <div style="border-bottom:3px solid rgb(72, 255, 0);"></div>
            <br>
            <table style="width: 100%;">
                <thead>

                    <tr>
                        <th colspan="3" class="vipHeader">
                            <b style="text-align:center;color:blue;font-size:12px;text-transform:uppercase;">{{ $item->ticketName }}

                            </b><br>
                            <b># {{ $item->ticketNo }}</b>
                        </th>
                    </tr>

                </thead>
            </table>

            <table style="width: 100%; font-size:9px;">
                <tbody>
                    <tr>
                        <td colspan="3">
                            <p style="text-align: center;font-size:10px;"><b>
                                    {{ number_format($item->ticketAmount) }} TZS</b></p>
                            @if ($item->plateNumber != null)
                                <p style="text-align: center;font-size:10px;"><b>{{ $item->plateNumber }}</b></p>
                            @else
                                <p></p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p style="font-size:10px;">
                                <b>Customer: </b> {{ $item->customerName }}.<br>
                                <b>Phone:</b> {{ $item->phone_number }}.<br>
                                <b>TicketDate:</b> {{ date('d-m-Y', strtotime($item->ticketDate)) }}.<br>
                            </p>

                        </td>
                        <td style=" text-align:center;">
                            <img src="data:images/png;base64, {{ base64_encode(QrCode::size(50)->generate('SAMS|' . $item->ticketId)) }} "
                                style="border:0.5px solid black;">
                            <br>
                            <b style="font-size:8px; font-famil:cambria;">{{ $item->ticket_issued }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <img src="{{ $location }}" height="10" width="10"> <b
                                style="font-size: 8px;">Mwl J.K Nyerere Fair Ground (SabaSaba).</b>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="background-image:url({{ $mark }});font-family: monospace; padding:10%;color:blue; ">

                <b style="font-size: 10px">{{ date('l, M d, Y h:i:s', strtotime($item->ticketDate)) }}
                    <br>
                    <q>TANZANIA: YOUR BEST DESTINATION FOR
                        BUSINESS AND INVESTIMENTS</q>
                </b>
            </div>

            <p style="color:red; font-family:cambria;text-align:center;font-size:8px">
                <b>
                    @if ($item->ticketAmount < 10000)
                        Ticket will be Used Once by One person Only!
                    @elseif ($item->ticketAmount == 1500000)
                        Ticket will be valid From 28<sup>th</sup> Jun - 13<sup>th</sup> 2025.
                    @endif
            </p>
            <p style="text-align: center">
                <b style="font-size: 8px">
                    powered by <b style="color:blue">TANTRADE</b>
                    @if ($item->serviceCode != 'TANTRADE')
                        in corabollation with
                        <b style="color: blue">{{ $item->serviceCode }}</b>
                    @endif

            </p><br>
            <p style="text-align: center; font-family:monospace; font-size:80%">****** END OF LEGAL TICKET
                *******</p>
        </div>
    </div>
@endforeach
