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
                        <b style="font-size: 3vmin">EXHIBITION SPACE REQUEST</b>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <p style="font-size:2vmin"> Hi, <b
                                style="color:rgb(10, 112, 228);font-size:2vmin">{{ $name }}</b>.
                        </p>
                        <p>
                            This is to notify you that we have received your request for space at the
                            Geita Mineral Exhibition, with request number <b>{{ $requestNo }}</b>.
                            <br>
                            We are working for your request in one to two working days! Then we will be back to you.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <br><br>
        <p style="color:rgb(95, 97, 99);font-size:1vmin;text-align:center;">
            You are receiving this email because you requested a space for exhibition.
            If you received this email incorrectly, we are sorry for any inconvenience, and no action is required.
            <br>
            For more information about the exhibition, visit:
            <a href="{{ env('APP_URL') }}" style="text-decoration: none;"> {{ env('APP_URL') }} </a>
            <br>
            &copy; {{ date('Y') }} Geita Mineral Exhibition Secretariat. All rights reserved.
        </p>
    </div>
</div>
