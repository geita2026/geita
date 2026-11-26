<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class=" icon-home position-left"></i>Home
                        </a></li>
                <li class="active">View All Companies Details</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class="content">
        <div class="panel panel-white">

            <div class=" panel-body">

                @foreach ($companies as $item)
                    <div class="form-group row">
                        <div class="col-md-12 text-right">

                            <ul class="icons-list">

                                <li class="dropdown">

                                    <a href="#"
                                        class="dropdown-toggle text-primary text-bold btn btn-default border-success"
                                        data-toggle="dropdown">

                                        Actions <i class="icon-menu4"></i>

                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">

                                        @can('VIEW_COMPANY_USER')
                                            <li>
                                                <a href="{{ route('company_users.show', ['company_user' => $item->companyID]) }}"
                                                    class=" text-primary"><i class=" icon-users4"></i> Company Users
                                                </a>
                                            </li>
                                        @endcan
                                        @can('CREATE_COMPANY')
                                        <li>
                                            <a href="{{ route('associate_companies.show', ['associate_company' => $item->companyID]) }}"
                                                class=" text-primary">Associate Members<i class=" icon-plus2"></i></a>
                                        </li>
                                        @endcan
                                        @can('MAKE_AS_EXHIBITOR')
                                        <li>
                                            <a href="{{ route('company_profiles.edit', ['company_profile' => $item->companyID]) }}"
                                                class=" text-primary">Make Exhibitor<i class=" icon-markup"></i></a>
                                        </li>
                                        @endcan

                                        @can('VIEW_COMPANY_PAYMENTS')
                                            <li>
                                                <a href="{{ route('payments.edit', ['payment' => $item->companyID]) }}"
                                                    class=" text-primary"><i class=" icon-coin-dollar"></i> Company Payments
                                                </a>
                                            </li>
                                        @endcan

                                        @can('DELETE_COMPANIES')
                                            <li>
                                                <a class=" text-danger" title="Delete Company" data-toggle="modal"
                                                    data-target="#delete_company"><i class=" icon-bin2"></i> Suspend Company
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>

                                </li>

                            </ul>

                        </div>

                    </div>

                    {{-- Update form --}}
                    <form action="{{ route('companies.update', ['company' => $item->companyID]) }}" method="POST"
                        class="stepy-clickable" enctype="multipart/form-data">

                        @csrf

                        @method('PATCH')
                        @include('company.EditCompany')

                    </form>
                    {{-- End Update form --}}


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
</x-app-layout>

<script>
    $("#region").hide();
    $("#country").change(function() {
        if ($(this).val() == "Tanzania") { //for functional Halls

            $("#region").show();
        } else {
            $("#region").hide();

        }
    });
</script>
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
