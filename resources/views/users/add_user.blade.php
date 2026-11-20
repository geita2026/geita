<x-app-layout>
    <title>{{ env('APP_NAME') }} | Users</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">

            @include('layouts.heading-elements')

        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> USER REGISTRATION</a></li>
                <li class="active">User registration</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body">

                <div class=" col-md-12 form-group">
                    <a href="{{ route('users.index') }}" class=" btn btn-default text-primary"><i
                            class=" icon-arrow-left22"></i> Back</a>
                    <br><br>
                    <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                </div>

                <div class=" col-md-12 form-group table-header">
                    <h4 class="text-center text-bold">SYSTEM USER REGISTRATIONS</h4>
                </div>


                

                <div class=" form-group col-md-12">
                    {{-- user registration here --}}
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" row">
                            <div class=" col-md-6 form-group">
                                <label>User Type<i class=" text-danger">*</i></label>
                                <select name="userType" class=" bootstrap-select" data-live-search="true"
                                    data-width="100%" id="userType" required>
                                    <option> - - - Select User Type - - - </option>
                                    <option value="1">Internal User (TanTrade Staff)</option>
                                    <option value="2">External User (Exhibitor)</option>
                                </select>
                            </div>
                           
                                <div class=" col-md-6 form-group">
                                    
                                    <div id="TSRNFIELD">
                                        <label>TanTrade Service Registration Number (TSRN)<i
                                                class=" text-danger">*</i></label>
                                        <input type="text" id="TSRN" name="TSRN" class=" form-control"
                                            placeholder="TanTrade Service Registration Number (TSRN)" value="{{ old('TSRN') }}">
                                            <span class=" text-danger">@error('TSRN'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                            
        
                            
                        </div>
        
                        <div class=" row">
                            <div class="form-group col-md-6">
                                <label>First Name<i class=" text-danger">*</i></label>
                                <input type="text" name="first_name" class=" form-control input-lg"
                                    placeholder="First Name" value="{{ old('first_name') }}">
                                <span class="text-danger"> @error('first_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" class=" form-control input-lg"
                                    placeholder="Middle Name" value="{{ old('middle_name') }}">
                                <span class="text-danger"> @error('middle_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>Last Name<i class=" text-danger">*</i></label>
                                <input type="text" name="last_name" class=" form-control input-lg"
                                    placeholder="Last  name" value="{{ old('last_name') }}">
                                <span class="text-danger"> @error('last_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>user phone<i class=" text-danger">*</i></label>
                                <input type="text" name="phone_number" class=" form-control input-lg"
                                    placeholder=" start with: 255..., 254...., etc" value="{{ old('phone_number') }}">
                                <span class="text-danger"> @error('phone_number')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>user Email<i class=" text-danger">*</i></label>
                                <input type="text" name="email" class=" form-control" placeholder=" user email"
                                    value="{{ old('email') }}">
                                <span class="text-danger"> @error('email')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>User Title / Resignation </label>
                                <select name="role" class="bootstrap-select" data-live-search="true"
                                    data-width="100%">
                                    <option value=""> - - - - Select User role - - - -</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" class=" text-uppercase">
                                            {{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger"> @error('role')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row">
                            <div class="form-group col-md-6">
                                <label> User Electronic signature</label>
                                <input type="file" name="signature_image" class=" form-control input-lg"
                                    data-show-upload="false" value="{{ old('signature_image') }}">
                                <span class="text-danger"> @error('signature_image')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group col-md-6">
                                <label> User Profile image</label>
                                <input type="file" name="profile_image" class=" form-control input-lg"
                                    data-show-upload="false" value="{{ old('profile_image') }}">
                                <span class="text-danger"> @error('profile_image')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row">
                            <div class=" form-group col-md-12 text-right">
                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                        class="ladda-label">Register User</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- //user registration  --}}
        </div>
</x-app-layout>

<script>
    // $("#TSRNFIELD").hide();
    $("#userType").change(function() {
        if ($(this).val() == "2") { //for functional Halls

            $("#TSRNFIELD").show();
        } else {
            $("#TSRNFIELD").hide();

        }
    });
</script>


<script>
    $(document).ready(function() {
        var form = '#verifyTsrn';
        $('#verifyTsrn').on('keyup', function() {
            event.preventDefault();
            var url = $(this).attr('data-action');
            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    var htmlView = '';
                    if (response.status ==1) {
                        htmlView +=response.message; 
                    }
                    $('#response').html(htmlView);
                }
            });
        });

    });
</script>

{{-- <form data-action="{{ route('verify_tsrn') }}" method="POST" id="verifyTsrn">
    @csrf
    <div class=" col-md-6 form-group">
        
        <div id="TSRNFIELD">
           
            <label>TanTrade Service Registration Number (TSRN)<i
                    class=" text-danger">*</i> <b id="response" class=" text-bold text-success"></b></label>
            <input type="text" id="TSRN" name="TSRN" class=" form-control"
                placeholder="TanTrade Service Registration Number (TSRN)">
        </div>
    </div>
</form> --}}