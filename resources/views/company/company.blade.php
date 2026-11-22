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

                <form action="{{ route('companies.index') }}" method="GET" class="gememis-table-toolbar">
                    <label class="sr-only" for="company-table-search">Search companies</label>
                    <input type="search" class="form-control" id="company-table-search" name="search"
                        value="{{ request('search') }}" placeholder="Search companies">
                    <button type="submit" class="btn btn-primary">Search</button>
                    @if (request()->filled('search'))
                        <a href="{{ route('companies.index') }}" class="btn btn-default">Clear</a>
                    @endif
                    <button type="button" class="btn btn-primary gememis-table-register"
                        data-toggle="modal" data-target="#NoCompany" aria-haspopup="dialog">
                        Register Company
                    </button>
                </form>

                <!-- TABLE -->
                <table data-table-paginated data-table-search="server" class="table table-hover table-bordered">
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

                        @forelse ($companies as $item)
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
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No companies match your search.</td></tr>
                        @endforelse
                    </tbody>
                </table>


                <!-- PAGINATION -->
                <div class="text-right mt-15">
                    {{ $companies->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>
    <!-- ===================== /CONTENT ===================== -->

</x-app-layout>
