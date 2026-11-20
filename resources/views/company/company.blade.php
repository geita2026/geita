<x-app-layout>
    <title>{{ env('APP_NAME') }} | Companies</title>

    <!-- ===================== PAGE HEADER ===================== -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="icon-home position-left"></i> Home
                    </a>
                </li>
                <li class="active">Companies</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- ===================== /PAGE HEADER ===================== -->

    <!-- ===================== CONTENT ===================== -->
    <div class="content">

        <div class="panel panel-white">

            <!-- PANEL HEADER -->
            <div class="panel-heading border-bottom">
                <h6 class="panel-title text-bold">
                    Registered Companies
                </h6>
                <span class="text-muted">
                    View and manage all registered companies
                </span>
            </div>

            <div class="panel-body table-responsive">

                <!-- COMPANY REGISTRATION FORM -->
                <div class="row mb-15">
                    <div class="col-md-12">
                        @include('company.NewCompanyRegistration')
                    </div>
                </div>

                <!-- SEARCH -->
                <div class="row mb-20">
                    <div class="col-md-6">
                        <select id="search-select" class="form-control" onchange="location=this.value">
                        </select>
                    </div>
                </div>

                <!-- TABLE -->
                <table class="table table-hover table-bordered">
                    <thead style="background:#f5f7fa">
                        <tr>
                            <th class="text-center text-bold" style="width:60px">#</th>
                            <th class="text-bold">TSRN</th>
                            <th class="text-bold">Company Details</th>
                            <th class="text-center text-bold">Contact</th>
                            <th class="text-center text-bold">Status</th>
                            <th class="text-center text-bold" style="width:140px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $i = ($companies->currentPage() - 1) * $companies->perPage() + 1; @endphp

                        @foreach ($companies as $item)
                            @php
                                $currentYear = date('Y');
                                $isCurrent = $item->lastUpdatedOn == $currentYear;
                            @endphp

                            <tr>
                                <!-- INDEX -->
                                <td class="text-center text-muted">
                                    {{ $i }}
                                </td>

                                <!-- TSRN -->
                                <td>
                                    <a href="{{ route('company_profile', ['companyId' => $item->registrationID]) }}"
                                        class="text-bold text-primary">
                                        {{ $item->registrationID }}
                                    </a>
                                </td>

                                <!-- COMPANY -->
                                <td>
                                    <div class="text-uppercase text-bold">
                                        {{ $item->companyName }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $item->companyEmail }}
                                    </small>
                                </td>

                                <!-- CONTACT -->
                                <td class="text-center">
                                    <span class="text-nowrap">
                                        {{ $item->companyPhone }}
                                    </span>
                                </td>

                                <!-- LAST UPDATED -->
                                <td class="text-center">
                                    <span class="label {{ $isCurrent ? 'label-success' : 'label-danger' }}">
                                        {{ $item->lastUpdatedOn ?? 'N/A' }}
                                    </span>
                                    <div class="text-muted" style="font-size:11px">
                                        {{ $isCurrent ? 'Up to date' : 'Update required' }}
                                    </div>
                                </td>

                                <!-- ACTION -->
                                <td class="text-center">
                                    <a href="{{ route('company_profile', ['companyId' => $item->registrationID]) }}"
                                        class="btn btn-xs btn-primary">
                                        Manage Profile <i class="icon-arrow-right13"></i>
                                    </a>
                                </td>
                            </tr>

                            @php $i++; @endphp
                        @endforeach
                    </tbody>
                </table>


                <!-- PAGINATION -->
                <div class="text-right mt-15">
                    {{ $companies->links() }}
                </div>

            </div>
        </div>
    </div>
    <!-- ===================== /CONTENT ===================== -->

</x-app-layout>

<!-- ===================== SEARCH SCRIPT ===================== -->
<script>
$(document).ready(function () {

    function formatCompanyResult(item) {
        if (!item.id) {
            return item.text;
        }

        const parts = item.text.split(' | ');

        return $(`
            <div style="line-height:1.4">
                <div class="text-bold text-primary">
                    ${parts[0]} <span class="text-muted">— ${parts[1]}</span>
                </div>
                <div class="text-muted" style="font-size:12px">
                    <i class="icon-phone"></i> ${parts[2]}
                    &nbsp;&nbsp;
                    <i class="icon-mail5"></i> ${parts[3]}
                </div>
            </div>
        `);
    }

    function formatCompanySelection(item) {
        return item.text;
    }

    $('#search-select').select2({
        ajax: {
            url: '{{ route('search_companies') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: '{{ env('APP_URL') }}/customerReg/company_profile/' +
                                item.registrationID,
                            text: item.registrationID + ' | ' +
                                  item.companyName + ' | ' +
                                  item.companyPhone + ' | ' +
                                  item.companyEmail
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Search by TSRN, company name, phone, or email…',
        minimumInputLength: 0,
        width: '100%',
        templateResult: formatCompanyResult,
        templateSelection: formatCompanySelection,
        escapeMarkup: function (m) { return m; }
    });

});
</script>

