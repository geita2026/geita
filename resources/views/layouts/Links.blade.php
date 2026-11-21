<li class="  text-bold menuBtn {{ Route::is('index') ? 'active' : '' }} ">
        <a href="{{ route('index') }}" class="">Home</a>
    </li>


    <li class="  text-bold menuBtn {{ Route::is('preview_programs.index') ? 'active' : '' }} ">
        <a href="{{ route('preview_programs.index') }}" class="">Event Programs</a>
    </li>

    <li class="  text-bold menuBtn {{ Route::is('discover_exhibitor.index') ? 'active' : '' }} ">
        <a href="{{ route('discover_exhibitor.index') }}" class="">Exhibitors</a>
    </li>

    {{-- <li class="dropdown  menuBtn">
        <a href="#" class="dropdown-toggle text-bold" data-toggle="dropdown">About NaneNane <span
                class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <li class="innerLink {{ Route::is('About_ditf') ? 'active' : '' }}"><a href="{{ route('About_ditf') }}"> <i
                        class=" icon-arrow-right13"></i> Understanding NaneNane</a></li>
            <li class="innerLink {{ Route::is('experience.index') ? 'active' : '' }}"><a
                    href="{{ route('experience.index') }}"><i class=" icon-arrow-right13"></i> Our Exprience</a></li>
            <li class="innerLink {{ Route::is('preview_programs.index') ? 'active' : '' }}"><a
                    href="{{ route('preview_programs.index') }}"><i class=" icon-arrow-right13"></i> Event Programs</a></li>
            <li class="innerLink {{ Route::is('ditf_map') ? 'active' : '' }}"><a href="{{ route('ditf_map') }}"><i
                        class=" icon-arrow-right13"></i> NaneNane Map </a></li>
        </ul>
    </li> --}}
    
    {{-- <li class="dropdown  menuBtn ">
        <a href="#" class="dropdown-toggle text-bold" data-toggle="dropdown">Exhibitors<span
                class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <li class="innerLink {{ Route::is('discover_exhibitor.index') ? 'active' : '' }}"><a
                    href="{{ route('discover_exhibitor.index') }}"><i class=" icon-arrow-right13"></i>Exhibitor List</a>
            </li>
        </ul>
    </li> --}}
    
    {{-- <li class="dropdown  menuBtn">
        <a href="#" class="dropdown-toggle text-bold" data-toggle="dropdown">Online Booking <img
                src="{{ asset('images/new-animated.gif') }}" height="20" width="20"><span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <li class="innerLink {{ Route::is('space_booking_inquiries.index') ? 'active' : '' }}"><a
                    href="{{ route('space_booking_inquiries.index') }}"> <i class=" icon-arrow-right13"></i>Space
                    Booking</a></li>
        </ul>
    </li> --}}
    
    <li class="  text-bold menuBtn"><a href="{{ route('login') }}">Login</a></li>
    <li class="  text-bold menuBtn {{ Route::is('registration') ? 'active' : '' }} "><a href="{{ route('registration') }}"
            class="">Register <i class=" icon-user-plus"></i></a>
    </li>
    
    
   
    
    
    
    <div id="login" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">

                        {{-- RESERT PASSWORD --}}
                        <div id="resertPassword" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class=" modal-header text-center">
                                        <b style="font-size:1.2vmax">GeMEMIS Password Reset</b><br>
                                        <p class=" text-muted">Use the secure password recovery page to verify your identity and set a new password.</p>
                                    </div>
                        
                                    <div class="modal-body text-center">
                                        <a href="{{ route('password.request') }}" class="btn bg-primary btn-block">Go to Password Recovery<i
                                                class="icon-arrow-right14 position-right"></i></a>
                                    </div>
                                    <div class=" modal-footer">
                                        <p class=" text-left text-small text-muted">Email must be registered company email, during space
                                            booking, If failed to resert password Call <b>255 733 002 014</b> for support.</p>
                                        <div class=" form-group text-right">
                                            <button class=" btn btn-default border-warning text-warning" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END RESERT PASSWORD --}}

                        <form action="{{ route('login') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="panel panel-body login-form">
                                <div class="text-center">
                                    <div class="icon-object border-slate-300 text-slate-300"><img
                                            src="{{ asset('images/logo.jpg') }}" height="60" width="60"></div>
                                    <h5 class="content-group"><b id="h3">GeMEMIS Login</b><small
                                            class="display-block ">{{ __('Geita Mineral Exhibition Management Information System') }}</small>
                                    </h5>
                                </div>
                                <div class="form-group has-feedback has-feedback-left">
                                    <label>Registered Email / Username<i class=" text-danger">*</i></label>
                                    <input type="text" name="user_name" class="form-control" placeholder="Email / username"
                                        {{ old('user_name') }}>
                                    <div class="form-control-feedback">
                                        <i class="icon-user text-info"></i>
                                    </div>
                                    <span class="text-danger">
                                        @error('user_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="form-group has-feedback has-feedback-left">
                                    <label>Password<i class=" text-danger">*</i></label>
                                    <input type="password" name="password" class="form-control" placeholder="Password"
                                        {{ old('password') }}>
                                    <div class="form-control-feedback">
                                        <i class="icon-lock2 text-info"></i>
                                    </div>
                                    <span class="text-danger">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </span>
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
                                            <a data-toggle="modal" data-target="#resertPassword">Forgot password ?</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" id="submitButton" class="btn bg-blue btn-block btn-ladda btn-ladda-spinner">Login <i
                                        class="icon-arrow-right14 position-right" onclick="showLoader()"></i></button>
                                </div>
                                
                                <span class="help-block text-center no-margin">By continuing, you're confirming that you've read
                                    our <a href="{{ route('policies.index') }}">Terms &amp; Conditions</a></span>
        
                                    
                                <div class=" form-group text-right">
                                    <img src="{{ asset('images/loading2.gif') }}" id="loader" style="display: none;" height="100"
                            width="100">
                                    <button class=" btn btn-default border-warning text-warning"
                                        data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
        
                    </div>
                </div>
            </div>
        </div>
        
    
        <script>
            Ladda.bind( '.ladda-button', { timeout: 8000 } );
        </script>