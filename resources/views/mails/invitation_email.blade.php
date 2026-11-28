
    <div style="border-radius:5px; border:2px solid rgb(233, 231, 231); font-family: Serif;padding:2%;">
        <p style="font-size:115%;text-align: center;"><b>{{ $message_title }}</b></p>

    <div>
        <p>Hello,
            <br>
            <b style="color:rgb(10, 112, 228);margin-left:3%; font-size:110%">
                {{ $company_name }}.
            </b>
        </p>

        <p>
            {{ $body }}
        </p>

        <p>
            <h5><b style="color: blue">ExhibitionTheme:</b> <b>Tanzania; Mahali sahihi pa biashara na uwekezaji</b></h5>
        </p>

            <p>Sincerely,</p>
            <p>{{ $officer_name }}</p>
            <p><b>{{ $officer_title }}</b></p>
            <p>Geita Mineral Exhibition Secretariat.</p>
            @include('mails.MailFooterLayout')
    </div>
    </div>


