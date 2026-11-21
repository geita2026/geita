<x-guest-layout>
    <title>{{ env('APP_NAME') }} | Self Registration</title>
    <div class="page-header">
    
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>My Company
                        Registration </a></li>
                <li class="active">Self Registration </li>
            </ul>
           
        </div>
    </div>

    <div class="content">
        <div class="panel panel-white">

            <div class=" panel-body">
                <div class=" col-md-12 text-center text-primary">
                    <p style="font-size:3vmin">SELF REGISTRATION FORM No. 2A</p>
                </div>
                @foreach ($companies as $item)
                    <div class="form-group col-md-12">
                        <div class="alert alert-info alert-styled-left alert-bordered">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                            <span class="text-semibold">Heads up!</span> Fill the form bellow with correct Information <a href="#" class="alert-link text-primary ml-20"><i class=" icon-question3" data-popup="popover" title="Information Update" data-trigger="hover" 
                                data-content="After updating your information and Accepting Terms and condition you will be able to book for space"></i></a>.
                        </div>
                        
                    </div>

                    <div class=" form-group text-danger col-md-12" style="font-size: 2vmin">
                        Service Registration Number (SRN): <b>{{ $item->registrationID }}</b>
                    </div>

                    <form action="{{ route('self_registration.update', ['self_registration' => $item->companyID]) }}" method="POST"
                        class="stepy-clickable">

                        @csrf

                        @method('PUT')

                        @include('company.EditCompany')

                    </form>



                    <div id="delete_company" class="modal fade" aria-modal="true">

                        <div class="modal-dialog modal-md">

                            <div class="modal-content">

                                <div class="modal-header bg-danger">

                                    <h4 class="modal-title text-center text-uppercase">DELETE
                                        <b>{{ $item->companyName }}</b> Company</h5>

                                </div>

                                <div class=" modal-body">

                                    <form action="{{ route('companies.destroy', ['company' => $item->companyID]) }}"
                                        method="post" id="company">

                                        @csrf

                                        @method('DELETE')

                                </div>

                                <div class=" modal-footer">

                                    <div class=" text-right">

                                        <button class=" btn btn-default border-warning text-warning"
                                            data-dismiss="modal"> Close</button>

                                        <button type="submit"
                                            class="btn btn-warning  btn-ladda btn-ladda-spinner"><span
                                                class="ladda-label">Delete Company</span></button>

                                    </div>

                                </div>

                                </form>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    </div>
</x-guest-layout>

<script>
    $("#country").change(function() {
        if ($(this).val() == "Tanzania") { //for functional Halls

            $("#region").show();
        } else {
            $("#region").hide();

        }
    });
</script>

<div id="ShareCompany" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class=" form-group">
                    <input type="text" readonly value="{{ env('APP_URL') }}/{{ $item->companyID }}" id="myInput" class=" form-control">
                </div>
                <div class=" form-group text-right">
                    <button class=" btn btn-default border-warning text-warning" data-dismiss="modal">Close</button>
                    <button class=" btn btn-default" onclick="myFunction()">Copy Link <i class=" icon-copy3"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function myFunction() {
  // Get the text field
  var copyText = document.getElementById("myInput");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

   // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);
}
</script>

<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_bootstrap_select.js') }}"></script>