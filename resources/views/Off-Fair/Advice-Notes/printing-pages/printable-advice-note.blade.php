<title>{{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>

    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">View Off Fair Advice note</li>
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
                {{--  message  --}}
                @foreach ($adviceNotes as $item)
                    <div class=" row form-group">



                        <div class="col-md-12 text-right">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#"
                                        class="dropdown-toggle text-primary text-bold btn btn-default border-success"
                                        data-toggle="dropdown">
                                        Actions <i class="icon-menu4"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            {{-- ********download invoice********* --}}
                                            @can('Download Invoice')
                                                @if ($item->control_number != null)
                                            <li>
                                                <a onclick=" event.preventDefault();document.getElementById('download_invoice').submit();"
                                                    class=" text-primary text-bold" title="Download Invoice">Download
                                                    Invoice<i class=" icon-arrow-right13"></i></a>
                                            </li>
                    @endif
                @endcan
                {{-- ******** end download invoice ****** --}}
                </li>

                <li>
                    @if ($item->advice_note_status == 'GENERATED')
                        <a @if ($item->advice_note_deadline == null) disabled title='please set deadline' @else href="{{ route('adviceNotes.edit', ['adviceNote' => $item->advice_note_id]) }}" @endif
                            class=" text-purple"><i class="icon-reply"></i> Send Advice Note</a>
                    @elseif($item->advice_note_status == 'RETURNED')
                        <a @if ($item->advice_note_deadline == null) disabled title='please set deadline' @else href="{{ route('adviceNotes.edit', ['adviceNote' => $item->advice_note_id]) }}" @endif
                            class=" text-purple"><i class=" icon-reply"></i> Send Advice Note</a>
                    @endif
                </li>
                @can('Delete Advice Note')
                    @if ($item->advice_note_status != 'CANCELED')
                        <li>
                            <a data-toggle="modal" data-target="#Delete{{ $item->off_fair_advice_note_id }}"
                                title="Cancel Advice Note" class=" text-danger"><i class="icon-bin"></i> Cancel Invoice
                                </a>
                        </li>
                    @endif
                @endcan
                </ul>
                </li>
                </ul>
            </div>
            {{-- delete modal --}}


            <div id="Delete{{ $item->off_fair_advice_note_id }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title">Are you sure you whant to Cancel
                                <b><q> {{ $item->companyName ?? $item->customer_name }}</q></b> Advice Note ?</h5>
                        </div>
                        <div class="modal-body">
                            <form
                                action="{{ route('Off-Fair-Advice-Notes.destroy', ['Off_Fair_Advice_Note' => $item->off_fair_advice_note_id]) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <div class=" form-group">
                                    <label>Give reasons for Canceling this advice Note<i
                                            class=" text-danger">*</i></label>
                                    <textarea name="reasons" class=" form-control" placeholder="Write your reasons here" required>SYSTEM ERROR</textarea>
                                </div>

                                <div class=" form-group">
                                    @hasrole('Super Admin')
                                        <label>Delete all data from the system</label>
                                        <input type="checkbox" name="action" value="1">
                                    @endhasrole
                                </div>

                                <div class=" text-right">
                                    <button type="button" data-dismiss="modal"
                                        class=" btn btn-secondary">Close</button>
                                    <button type="submit" class="btn btn-danger ml-20">Yes, Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- delete modal --}}
        </div>

        @include('Off-Fair.Advice-Notes.printing-pages.Invoice')
    </div>

    </div>

    </div>
    <!-- /content area -->
    </div>
    @endforeach
    <!-- /main content -->
</x-app-layout>
{{-- download form --}}

<form action="{{ route('downloadOffFairInvoice') }}" method="POST" id="download_invoice" target="_blank">
    @csrf
    <input type="hidden" name="id" value="{{ $item->off_fair_advice_note_id ?? '' }}">
    @method('GET')
</form>
{{-- end --}}
