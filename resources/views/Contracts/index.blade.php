<x-app-layout>
    <title>{{ env('APP_NAME') }} | Contracts Management</title>

    <!-- W3CSS -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <!-- Page header with breadcrumbs -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">Contracts Management</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class="w3-card w3-white w3-round">
            <header class="w3-container w3-light-grey w3-padding">
                <h6>
                    <i class="icon-file-text2 position-left"></i>
                    <b>Signed Contracts</b>
                    <span class="w3-badge w3-blue w3-round" style="margin-left: 8px;">{{ $contracts->total() }}</span>
                    <small>Total</small>
                </h6>
            </header>

            <div class="w3-container w3-padding">
                {{-- Success/Error Messages --}}
                @if (session('success'))
                <div class="w3-panel w3-pale-green w3-border w3-border-green w3-leftbar w3-round" style="position: relative;">
                    <button onclick="this.parentElement.style.display='none'" class="w3-button w3-green w3-tiny" style="position: absolute; top: 0; right: 0;">&times;</button>
                    <p><b>Success!</b> {{ session('success') }}</p>
                </div>
                @endif

                @if (session('fail'))
                <div class="w3-panel w3-pale-red w3-border w3-border-red w3-leftbar w3-round" style="position: relative;">
                    <button onclick="this.parentElement.style.display='none'" class="w3-button w3-red w3-tiny" style="position: absolute; top: 0; right: 0;">&times;</button>
                    <p><b>Error!</b> {{ session('fail') }}</p>
                </div>
                @endif

                <div class="w3-responsive">
                    <table class="w3-table w3-bordered w3-striped">
                        <thead>
                            <tr class="w3-light-grey">
                                <th class="w3-center"><b>#</b></th>
                                <th><b>REGISTRATION ID</b></th>
                                <th><b>COMPANY NAME</b></th>
                                <th class="w3-center"><b>CONTACT</b></th>
                                <th class="w3-center"><b>EXHIBITOR</b></th>
                                <th class="w3-center"><b>WITNESS</b></th>
                                <th class="w3-center"><b>STATUS</b></th>
                                <th class="w3-center"><b>LAST UPDATED</b></th>
                                <th class="w3-center"><b>ACTIONS</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($contracts->currentPage() - 1) * $contracts->perPage() + 1; @endphp
                            @forelse($contracts as $contract)
                            <tr>
                                <td class="w3-center">{{ $i++ }}</td>
                                <td>
                                    <span class="w3-text-blue"><b>{{ $contract->registration_id }}</b></span>
                                </td>
                                <td style="text-transform: uppercase;">
                                    <b>{{ $contract->companyName ?? 'N/A' }}</b>
                                </td>
                                <td>
                                    @if($contract->companyEmail)
                                    <div class="w3-small w3-tag w3-round w3-white" style="margin-bottom: 4px;">
                                        <a href="mailto:{{ $contract->companyEmail }}">{{ $contract->companyEmail }}</a>
                                    </div>
                                    @endif
                                    @if($contract->companyPhone)
                                    <div>
                                        <div class="w3-small w3-tag w3-round w3-white" style="margin-bottom: 4px;">
                                        {{ $contract->companyPhone }}
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td class="w3-center">
                                    @if($contract->exhibitor_name)

                                        {{ $contract->exhibitor_name }}

                                    @else
                                    <span class="w3-tag w3-orange w3-round w3-small">
                                        <i class="icon-hour-glass2 position-left"></i>
                                        Pending
                                    </span>
                                    @endif
                                </td>
                                <td class="w3-center">
                                    @if($contract->witness_name)

                                    <span class="">
                                        {{ $contract->witness_name }}
                                    </span>
                                    @else
                                    <span class="w3-tag w3-orange w3-round w3-small">
                                        <i class="icon-hour-glass2 position-left"></i>
                                        Pending
                                    </span>
                                    @endif
                                </td>
                                <td class="">
                                    @if($contract->signature_count >= 2)


                                    <div class="w3-row w3-center">
                                        <div class="w3-third">
                                             <i class="icon-checkmark-circle"></i>
                                        </div>
                                        <div class="w3-col" style="width:50%">
                                            <p> Complete</p>
                                        </div>

                                    </div>
                                    @else
                                    <span class="w3-tag w3-orange w3-round w3-small">
                                        <i class="icon-clock3 position-left"></i>
                                        Partial ({{ $contract->signature_count }}/2)
                                    </span>
                                    @endif
                                </td>
                                <td class="w3-center">
                                    @if($contract->last_updated)
                                    <span class="w3-text-grey w3-small">
                                        {{ \Carbon\Carbon::parse($contract->last_updated)->format('M d, Y') }}
                                        <br>
                                        {{ \Carbon\Carbon::parse($contract->last_updated)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="w3-text-grey">N/A</span>
                                    @endif
                                </td>
                                <td class="w3-grid-padding">
                                    <a href="{{ route('contract.signing', ['registrationID' => $contract->registration_id]) }}"
                                        target="_blank" style="margin-left: 5% !important;"
                                        class="w3-button w3-blue w3-tiny w3-round"
                                        title="Open in signing app (generates PDF on-the-fly)">
                                        <i class="icon-file-pdf"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="w3-center w3-padding-large">
                                    <div style="padding: 40px 20px;">
                                        <i class="icon-file-empty icon-3x w3-text-grey" style="display: block; margin-bottom: 10px;"></i>
                                        <h6><b>No signed contracts found</b></h6>
                                        <span class="w3-text-grey">Signed contracts will appear here once they are available</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contracts->hasPages())
                <div class="w3-bar w3-border-top w3-padding w3-light-grey" style="margin-top: 16px;">
                    <div class="w3-left w3-padding-small">
                        <span class="w3-text-grey">
                            Showing {{ $contracts->firstItem() ?? 0 }} to {{ $contracts->lastItem() ?? 0 }}
                            of {{ $contracts->total() }} entries
                        </span>
                    </div>
                    <div class="w3-right">
                        {{ $contracts->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- /content area -->

</x-app-layout>
