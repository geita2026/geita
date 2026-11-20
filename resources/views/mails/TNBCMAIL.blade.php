<div
    style="border-radius:5px; border:2px solid rgb(217, 219, 219); font-family: Serif;padding:2%;background-color:rgb(237, 243, 242);">

    <p>Dear <b>{{ $name }}</b>,</p>

    <div>
        <p>
            Congratulations on entering the second round of the Tanzania National Brand Competition! This is to confirm that we have received your artwork for the second round.
        </p>
        <p>
            Only top three designers will be contacted.
        </p>
       
        {{-- <p>
            Best regards,
        </p>

        <p>
            Latifa M. Khamis,<br>
            <b>DIRECTOR GENERAL - TANTRADE</b>
        </p> --}}
        @include('mails.MailFooterLayout')
    </div>
</div>
