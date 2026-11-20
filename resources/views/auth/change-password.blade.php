<x-app-layout>

    <title>{{ env('APP_NAME') }} | ChangePassword</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('profiles.show',['profile']) }}"><i class="icon-home2 position-left"></i>User-Profiles</a></li>
            <li class="active">Change-Password</li>
        </ul>

        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body table-responsive">

            <div class=" panel-body">
                <form action="{{ route('profiles.store') }}" method="POST">
                    @csrf
                   <div class=" row">
                    <div class=" col-md-12">
                            <!-- Navigation -->
					    	<div class="panel panel-flat">
								<div class="panel-heading">
									<h4 class="panel-title text-uppercase text-info"><b>{{ Auth::user()->first_name }}'S</b> PROFILE INFORMATIONS</h4>
								</div>

								<div class="list-group no-border no-padding-top">
                                    <div class=" row">
                                        <div class=" form-group col-md-3 ">
                                            <a href="#" class="list-group-item"><i class="icon-calendar3"></i>Last Password Change:<b> {{ date('M d, Y H:i:s') }}</b></a>
                                        </div>

                                        <div class=" col-md-8">
                                            <div class="form-group">
                                                <label>Current Password <i class=" text-danger">*</i></label>
                                                <input type="password" name="password" class=" form-control" placeholder=" Current Password" value="{{ old('password') }}" required>
                                                <span class=" text-danger">@error('password') {{ $message }} @enderror</span>
                                            </div>

                                            <div class="">
                                                <div class="content-group">
                                                    <div class="form-group">
                                                        <label>New Password <i class=" text-danger">*</i> <i class=" text-muted">(Min. 8 characters with uppercase, lowercase, number, and special character)</i></label>
                                                        <div class="input-group group-indicator">
                                                            <input type="password" name="new_password" class="form-control" placeholder="Enter your New password" value="" required>
                                                            <span class="input-group-addon password-indicator-group">No password</span>
                                                        </div>
                                                        <span class=" text-danger">@error('new_password') {{ $message }} @enderror</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="">
                                                <div class="content-group form-group">
                                                        <label>Confirmation Password <i class=" text-danger">*</i></label>
                                                            <input type="password" name="confirmation_password"  class="form-control" placeholder="Enter your password" value="" required>
                                                        <span class=" text-danger">@error('confirmation_password') {{ $message }} @enderror</span>
                                                </div>
                                            </div>

                                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                                                <div class=" form-group row">
                                                    <div class="col-md-2 form-group ">
                                                        {{-- <button type="button" class="btn btn-info generate-group">Generate password</button> --}}
                                                    </div>
                                                    <div class=" text-right col-md-10 form-group">
                                                        <button type="submit" id="changePasswordBtn" class=" btn btn-primary btn-ladda btn-ladda-spinner">Change-Password </button>
                                                    </div>
                                                </div>

                                        </div>

                                    </div>
								</div>
							</div>
							<!-- /navigation -->
                    </div>

                   </div>
                </form>
            </div>
            {{-- //data here --}}
        </div>
    </div>
</div>
<!-- /dashboard content -->
</x-app-layout>
<script>
document.querySelector('form[action="{{ route('profiles.store') }}"]')?.addEventListener('submit', function () {
    const btn = document.getElementById('changePasswordBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Please wait...'; }
});
</script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_controls_extended.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jasny_bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/autosize.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/formatter.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/inputs/passy.js') }}"></script>



<div id="change-image" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-md">
     <div class="modal-content">
         <div class="modal-header bg-primary">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
             <h5 class="modal-title text-uppercase text-center">Change your profile image</h5>
         </div>

         <div class="modal-body">
             <form action="{{ route('profiles.update',['profile'=>Auth::user()->id]) }}" method="POST" enctype="multipart/form-data">
                 @csrf
                 @method('PUT')
                 <div class=" form-group">
                     <input type="file" name="profile_image" class=" form-control profile_image">
                 </div>
                 <input type="hidden" name="image" value="{{Auth::user()->profile_image}}">
                <div class=" text-right">
                 <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                 <button type="submit" class="btn btn-primary ml-20 save" >Update</button>
                </div>
             </form>
         </div>
     </div>
    </div>
    </div>
    {{-- upload modal --}}
