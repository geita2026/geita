<div
    style="border-radius:5px; border:2px solid rgb(217, 219, 219); font-family: Serif;padding:2%;background-color:rgb(237, 243, 242);">
    
    <p >Hi <b style="color: blue; text-transform:capitalize;">{{ $name ?? 'NULL' }}</b> </p>

    <div >
        <p>Thank you for registering with us. To complete your registration, please Activate your Account by clicking the
            link below:
        </p>

        <p style="text-align: center">
            <a href="{{ route('auth.verify_email', ['token' => $token ?? 'NULL']) }}"
                style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Activate
                Account
            </a>
        </p>
        @include('mails.MailFooterLayout')
    </div>
</div>
