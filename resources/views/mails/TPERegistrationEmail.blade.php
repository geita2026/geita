<div
    style="border-radius:5px; border:2px solid rgb(217, 219, 219); font-family: Serif;padding:2%;background-color:rgb(237, 243, 242);">

    <p>Dear <b style="color: blue; text-transform:capitalize;">{{ $name }}</b> </p>

    <div>
        <p>
            Thank you for Registering to the {{ $eventName }}! We are excited to have
            your in this Event.
        </p>
        <p>
           You will be contacted upon registration process completed.
        </p>

        <p>
            <b>Warm regards,</b><br><br>
            Organizing committee.
        </p>


        @include('mails.MailFooterLayout')
    </div>
</div>
