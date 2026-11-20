<div
    style="border-radius:5px; border:2px solid rgb(133, 182, 185); font-family: Serif;padding:15%;background-color:rgb(237, 243, 242);">
    <div style="padding: 2%;background-color:white;">
        <table style="width: 100%">
            <thead>
                <tr>
                    <th>
                        <img src="{{ $message->embed(public_path() . '/images/logo.jpg') }}" alt="" height="50"
                            width="60" style="text-align:right;">
                    </th>
                    <th>
                        <b style="font-size: 3vmin">ONLINE TICKET REQUEST VERIFICATION</b>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <p style="font-size:2vmin"> Hi, <b
                                style="color:rgb(10, 112, 228);font-size:2vmin">{{ $name }}</b>.
                        </p>
                        You have booked online ticket for Dar es salaam International Trade Fair (DITF) - Sabasaba,
                        <br>
                        <p style="text-align:justify">
                            {{ $ticketDescription }}
                            <b>Total Amount: {{ number_format($ticketAmount,2) }} TZS</b>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{ $link }}">
                            <button
                                style="padding:1%;background-color:rgb(0, 26, 255);border-radius:3%;color:white;">
                                confirm Booking
                            </button>
                        </a>
                    </td>
                    <td>
                        <a href="{{ env('APP_URL') . '/online_tickets/' . $booking_id }}">

                            <button style="padding: 0.8%;background-color:red; color:white;border-radius:3%">Reject
                                Booking
                            </button>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        @include('mails.MailFooterLayout')
    </div>
</div>
