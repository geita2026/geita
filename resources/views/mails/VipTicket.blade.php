
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
                    <b>DAR ES SALAAM INTERNATIONAL TRADE FAIR ONLINE TICKET</b>
                </th>
               
            </tr>
        </thead>
       </table>
       
    </p>

<div>
    {{-- <p style="text-align: center;font-size:1vmax">TICKET NUMBER: <b>#{{ $ticket_number }}</b></p> --}}
    <p style="font-size:120%;"><b>PAYMENT DETAILS</b><br>
    <p>CUSTOMER NAME: <b>{{ $name }}</b></p>
    <p>CUSTOMER PHONE: <b>{{ $phone_number }}</b></p>
    <p>CUSTOMER EMAIL: <b>{{ $email }}</b></p>
    <p>CONTROL NUMBER: <b>{{ $control_number }}</b></p>
    <p>AMOUNT PAID: <b>{{ number_format($amount_paid,2) }} TZS</b></p>
    <p>ISSUE DATE: <b>{{ date('d-m-Y h:i:s',strtotime($ticket_issued_on)) }}</b></p>
    </p>
    <p style="text-align: center">
        {{  QrCode::size(80)->generate('SAMS|'.$ticket_number) }}
    </p>

    <p>To download your Ticket  <a href="{{ $link }}"> click here</a></p>

    <p>
        <b style="font-size:120%;">OTHER INFORMATIONS</b><br>
        If you have any questions or require further clarification, please feel free to contact TanTrade at 255 733-002-014.
    </p>
    @include('mails.MailFooterLayout')
</div>
</div>


