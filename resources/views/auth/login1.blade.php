<title>{{ env('APP_NAME') }} | Login</title>
<x-guest-layout>
            <div class=" panel panel-body login-form">
                <div class="">
                    <div class="text-center">
                        <div class="icon-object border-slate-300 text-slate-300"><img src="{{ asset('images/logo.jpg') }}" height="60" width="60"></div>
                        <h5 class="content-group"><b>GeMEMIS Login</b><small class="display-block ">{{ __('Geita Mineral Exhibition Management Information System') }}</small></h5>
                    </div>

                    <form  method="POST" action="{{ route('login') }}">
                        @csrf
                    <div class="form-group has-feedback has-feedback-left">
                        <input class="form-control" type="text" name="email" value="{{ old('email') }}" autofocus placeholder="User Name" >
                        <div class="form-control-feedback">
                            <i class="icon-user text-mutedt text-info"></i>
                        </div>
                        <span class="text-danger">@error('email'){{ $message }}@enderror</span>
                    </div>

                    <div class="form-group has-feedback has-feedback-left">
                        <input type="password" class="form-control" name="password" placeholder="Password" value="{{ old('passwword') }}">
                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted text-info"></i>
                        </div>
                        <span class="text-danger">@error('password'){{ $message }}@enderror</span>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="submit" class="btn bg-success-800 btn-block">Log in <i class="icon-circle-right2 position-right"></i></button>
                    </div>
                    @if (Route::has('password.request'))
                    <div class="">
                        {{-- <a href="{{ route('register') }}" class="">Don't have account?</a> --}}
                        @if (Route::has('password.request'))
                        <a class="" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    </div>
                    @endif
                </div>
            </div>


    </form>
    <!-- /simple login form -->

</x-guest-layout>
