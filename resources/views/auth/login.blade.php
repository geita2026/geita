
<div class=" col-md-12">
    <div id="login" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('login') }}"  method="POST"  enctype="multipart/form-data" >
                        @csrf
                        <div class="panel panel-body login-form" >
                            <div class="text-center">
                                <div class="icon-object border-slate-300 text-slate-300"><img src="{{ asset('images/tanzania-coat-of-arms.svg') }}" height="60" width="60"></div>
                                <h5 class="content-group" ><b id="h3">GeMEMIS LOGIN PANEL</b><small class="display-block ">{{ __('Space Allocation  Management System (SAMS).') }}</small></h5>
                            </div>
                            <div class="form-group has-feedback has-feedback-left">
                                <input type="text" name="user_name" class="form-control" placeholder="Username" {{ old('user_name') }}>
                                <div class="form-control-feedback">
                                    <i class="icon-user text-info"></i>
                                </div>
                                <span class="text-danger">@error('user_name') {{ $message }} @enderror</span>
                            </div>
                            <div class="form-group has-feedback has-feedback-left">
                                <input type="password" name="password" class="form-control" placeholder="Password" {{ old('password') }}>
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-info"></i>
                                </div>
                                <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                            </div>
                            <div class="form-group login-options">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="remember" class="styled">
                                            Remember
                                        </label>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        {{-- href="{{ route('password.request') }}" --}}
                                        <a  data-toggle="modal" data-target="#resertPassword">Forgot password ?</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn bg-blue btn-block btn-ladda btn-ladda-spinner" >Login <i class="icon-arrow-right14 position-right"></i></button>
                            </div>
                            {{-- <div class="content-divider text-muted form-group"><span>Don't have an account ?</span></div>
                            <a data-toggle="modal" data-target="#NewAccount" class="btn btn-default btn-block content-group">Create Account</a> --}}
                            <span class="help-block text-center no-margin">By continuing, you're confirming that you've read our <a href="{{ route('policies.index') }}">Terms &amp; Conditions</a></span>

                            <div class=" form-group text-right">
                                <button class=" btn btn-default border-warning text-warning" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
