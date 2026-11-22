<x-app-layout>
    <title>{{ config('global.MODULE_TWO') }} | Company Profile</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li class="active">Update Company details</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <div class="content">
        @foreach ($myCompany as $item)
        @endforeach
        <!-- Basic modals -->
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title text-bold"> Company Registration || <a href="{{ route('companies.index') }}"
                        class=" text-primary btn">Registered Companies <i class=" icon-arrow-right13"></i></a></h5>

                @if ($item ?? false)
                    <div class="heading-elements">
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

                                            @can('View CompanyUsers')
                                                <li>
                                                    <a href="{{ route('company_users.show', ['company_user' => $item->companyID ?? '12']) }}"
                                                        class=" text-primary"><i class=" icon-users4"></i> Company Users
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('Company Registration')
                                                <li>
                                                    <a href="{{ route('associate_companies.show', ['associate_company' => $item->companyID ?? '12']) }}"
                                                        class=" text-primary">Associate Members<i
                                                            class=" icon-plus2"></i></a>
                                                </li>
                                            @endcan
                                            @can('Make AsExhibitor')
                                                <li>
                                                    <a href="{{ route('company_profiles.edit', ['company_profile' => $item->companyID ?? '12']) }}"
                                                        class=" text-primary">Make Exhibitor<i class=" icon-markup"></i></a>
                                                </li>
                                            @endcan

                                            @can('View CompanyPayments')
                                                <li>
                                                    <a href="{{ route('payments.edit', ['payment' => $item->companyID ?? '12']) }}"
                                                        class=" text-primary"><i class=" icon-coin-dollar"></i> Company
                                                        Payments
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('Delete Companies')
                                                <li>
                                                    <a class=" text-danger" title="Delete Company" data-toggle="modal"
                                                        data-target="#delete_company"><i class=" icon-bin2"></i> Suspend
                                                        Company
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>

                                    </li>

                                </ul>

                            </div>

                        </div>
                    </div>
                @endif

            </div>

            <div class="panel-body">

                <div class="">
                </div>

            </div>


        </div>
        <!-- /basic modals -->
    </div>
</x-app-layout>
<script>
    const checkbox = document.getElementById('TSRN');
    const toggleDiv = document.getElementById('TSRNFIELD');

    if (checkbox && toggleDiv) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                toggleDiv.style.display = 'block';
            } else {
                toggleDiv.style.display = 'none';
            }
        });
    }
</script>
<script>
    $("#country").change(function() {
        if ($(this).val() == "Tanzania") {
            $("#region").show();
        } else {
            $("#region").hide();
        }
    });
</script>
<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

<div id="delete_company" class="modal fade" aria-modal="true">

    <div class="modal-dialog modal-md">

        <div class="modal-content">

            <div class="modal-header bg-danger">

                <h4 class="modal-title text-center text-uppercase">DELETE
                    <b>{{ $item->companyName ?? '12' }}</b> Company</h4>

            </div>

            <div class=" modal-body">

                <form action="{{ route('companies.destroy', ['company' => $item->companyID ?? 12]) }}" method="post"
                    id="company">

                    @csrf

                    @method('DELETE')

            </div>

            <div class=" modal-footer">

                <div class=" text-right">

                    <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>

                    <button type="submit" class="btn btn-warning  btn-ladda btn-ladda-spinner"><span
                            class="ladda-label">Delete Company</span></button>

                </div>

            </div>

            </form>

        </div>

    </div>

</div>
