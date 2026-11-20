@foreach ($adviceNotes as $item)
<div
    style="border-radius:5px; border:2px solid rgb(133, 182, 185); font-family: Serif;padding:8%;background-color:rgb(237, 243, 242);">
    <div style="padding: 2%;background-color:white;">
        <table style="width: 100%">
            <thead>
                <tr>
                    <th>
                        <img src="{{ $message->embed(public_path() . '/images/logo.jpg') }}" alt="" height="50" --}}
                            width="60" style="text-align:right;">
                    </th>
                    <th>
                        <b style="font-size: 2.5vmin">DAR ES SALAAM INTERNATIONAL TRADE FAIR ONLINE TICKET INVOICE</b>
                    </th>
                   
                </tr>
            </thead>
            
            <tbody>
                <tr>
                    <td colspan="2">
                        <hr >
                    </td>
                </tr>
                <tr >
                    <td style=" text-align:left">
                        <p style="color:rgb(60, 60, 61);">Invoiced To:</p>
                        <b > Name: {{ $item->companyName }} ,</b><br>
                        <b >Address:  {{ $item->companyAddress ?? '-'}} , {{ $item->companyLocation }}, </b><br>
                        <b>Email: {{ $item->companyEmail ?? '-' }}</b><br>
                        <b> Tel: {{ $item->companyPhone ?? '-' }}</b><br>

                    </td>
                    <td style="text-align:right">
                        <p > <b>Invoice From</b>:</p>
                        <p>
                        <p>Tanzania Trade Development Authority,</p>
                        <p>P.O.BOX 5402,</p>
                        <p>DAR ES SALAAM.</p>
                        </p>

                    </td>
                </tr>

                <tr >
                    <td colspan="2">
                        <p ><b>INVOICE DETAILS</b><br>
                        <p>CONTROL NUMBER: <b>{{ $control_number }}</b></p>
                        {{--  --}}
                        <p>TOTAL AMOUNT: <b>{{ number_format($ticket_amount, 2) }} TZS</b></p>
                        {{--  --}}
                        <p>ISSUE DATE: <b>{{ date('d-m-Y h:i:s', strtotime($ticket_booked_on)) }}</b></p>
                        {{--  --}}
                        <p>DEADLINE: <b>{{ date('d-m-Y h:i:s', strtotime($payment_deadline)) }}</b></p>
                        {{--  --}}
                        </p>

                        <div>
                            <b>OTHER INFORMATIONS</b><br>
                            We kindly request that you remit the total amount due by the due date mentioned above.
                            If you have any questions or require further clarification,
                            Regarding the invoice or payment process, please feel free to contact TanTrade
                            at +255 733-002-014.
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>

        @include('mails.MailFooterLayout')
        
    </div>
</div>

<!-- END -->
@endforeach
