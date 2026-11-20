<div id="containner" style="border-radius:5px; border:1px solid rgb(224, 228, 233); font-family:monospace; ">
    <div style="padding:4%;background-color: rgb(255, 255, 255);">
        <p>Hi, <b style="color:blue; text-transform:capitalize">{{ $name ?? '' }}</b>,<br></p>
        <p>
            Your new password is:<b>{{ $password }}</b>
        </p>

        <p >
            <b style="color: red">NOTE: </b> you are required to change your password after login.
        </p>

    </div>

    @include('mails.MailFooterLayout')
</div>
