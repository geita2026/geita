<title>
    {{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
    <link rel="stylesheet" href="{{ asset('assets/css/exhibition-invoice.css') }}">
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">View Invoice</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        @if ($adviceNotes->first()->isApproved())
            <div class="advice-note-download hidden-print">
                <a class="btn btn-primary" href="{{ route('download_invoice', ['id' => $adviceNotes->first()->advice_note_id]) }}">
                    <i class="icon-download position-left" aria-hidden="true"></i> Download Advice Note
                </a>
            </div>
        @endif
        <div class="panel panel-flat">
            <div class=" panel-body table-responsive">


                {{-- error message --}}

                <div class=" col-md-12 form-group">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class="text-danger">{{ $error }} , </span>
                        @endforeach
                    @endif
                </div>
                {{-- end error message --}}


                {{--  message  --}}
                @foreach ($adviceNotes as $item)
                    @php
                        $user = Auth::user();
                        $role = \Spatie\Permission\Models\Role::class;
                        $canceled = $item->advice_note_status === 'CANCELED';
                        $needsControlNumber = !$canceled && blank($item->control_number);
                        $waitingForBilling = $needsControlNumber && filled($item->sp_invoice_no);
                        $canRequestControl = $needsControlNumber && !$waitingForBilling
                            && $user->can('REQUEST_ADVICE_NOTE', $role);
                        $canPreviewForm = $user->account_type == 1 && filled($exhibitor?->participationCommitimentFile);
                        $canApplyDiscount = !$canceled && blank($item->sp_invoice_no) && $user->can('APPLY_DISCOUNT', $role);
                        $canExtendDeadline = !$canceled && filled($item->control_number) && $item->advice_note_deadline
                            && strtotime($item->advice_note_deadline) < strtotime('today') && $user->can('EXTEND_DEADLINE', $role);
                        $canCancel = $item->isCancelable() && $user->can('CANCEL_INVOICES', $role);
                    @endphp

                    @if ($needsControlNumber)
                        <p class="alert alert-info invoice-action-status" role="status">
                            @if ($waitingForBilling)
                                The request was sent to billing. The control number will appear here once billing issues it.
                            @elseif ($canRequestControl)
                                Request a control number before making a payment.
                            @else
                                A control number has not been requested. Contact the accounts team to request one.
                            @endif
                        </p>
                    @endif

                    @if ($canRequestControl || $canPreviewForm || $canApplyDiscount || $canExtendDeadline || $canCancel)
                        <div class="invoice-toolbar" role="group" aria-label="Invoice actions">
                            @if ($canRequestControl)
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#requestControlNumber{{ $item->advice_note_id }}">
                                    <i class="icon-arrow-right13" aria-hidden="true"></i> Request control number
                                </button>
                            @endif
                            @if ($canPreviewForm)
                                <a href="{{ $exhibitor->participationCommitimentFile }}" class="btn btn-default">
                                    <i class="icon-file-eye2" aria-hidden="true"></i> Preview signed form
                                </a>
                            @endif
                            @if ($canApplyDiscount)
                                <a href="{{ route('review_invoice_discount', ['id' => $item->advice_note_id]) }}" class="btn btn-default">
                                    <i class="icon-pencil7" aria-hidden="true"></i> Apply discount
                                </a>
                            @endif
                            @if ($canExtendDeadline)
                                <button type="button" class="btn btn-default" data-toggle="modal"
                                    data-target="#Extend{{ $item->advice_note_id }}">
                                    <i class="icon-calendar2" aria-hidden="true"></i> Extend deadline
                                </button>
                            @endif
                            @if ($canCancel)
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#cancelInvoice{{ $item->advice_note_id }}">
                                    <i class="icon-cross2" aria-hidden="true"></i> Cancel invoice
                                </button>
                            @endif
                        </div>
                    @endif

                    @if ($canRequestControl)
                        {{-- *********** INVOICE Control Number ******** --}}
                        <div id="requestControlNumber{{ $item->advice_note_id }}" class="modal fade"
                            role="dialog" aria-modal="true" tabindex="-1" aria-labelledby="requestControlNumberTitle{{ $item->advice_note_id }}">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 id="requestControlNumberTitle{{ $item->advice_note_id }}" class="modal-title">Request control number</h2>
                                    </div>
                                    <form
                                        action="{{ route('adviceNotes.update', ['adviceNote' => $item->advice_note_id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="source" value="1">
                                        <div class="modal-body">
                                            @if ($item->currency == 'USD')
                                                <div class=" form-group">
                                                    <label>What type of currency will you use to pay ? <b
                                                            class=" text-danger">Note! Customer Has requested to pay in
                                                            {{ $item->change_currency }}</b></label>
                                                    <select name="currency" class="form-control" required id="mySelect"
                                                        readonly>
                                                        <option value="{{ $item->change_currency }}">
                                                            {{ $item->change_currency }} </option>
                                                        {{-- <option value="TZS">Tanzania Shilling (TZS)</option>
                                                        <option value="USD">US Dollar</option> --}}
                                                    </select>
                                                </div>
                                            @endif
                                            @if (Auth::user()->account_type == 1)
                                                <div class=" form-group">
                                                    <label>Provide any Descrption About Invoice</label>
                                                    <textarea name="invoiceDescription" class=" form-control" placeholder="Enter invoice Description">{{ $item->advice_note_description }}</textarea>
                                                </div>
                                            @endif

                                            <div id="myDiv">
                                                <div class=" form-group col-md-12">
                                                    <label>Exchange Rate for {{ date('M d, Y') }}<i
                                                            class=" text-danger">*</i></label>
                                                    <input type="text" name="exchangeRate"
                                                        value="{{ number_format($rate, 2) }} TZS"
                                                        class="form-control" readonly>
                                                    <span class=" text-danger text-error eventType"></span>
                                                </div>
                                                <div class=" form-group col-md-12">
                                                    <p><b>New Amount After Conversion will be:
                                                        </b>{{ number_format($rate * $item->total_amount, 2) }} TZS</p>
                                                </div>
                                            </div>
                                            <div class=" form-group">
                                                <p class=" text-danger"><b>Note!</b> You are required to pay the full
                                                    invoiced amount before the deadline, Failed to do
                                                    so, The System will cancel your booking and space will be
                                                    available for public booking.</p>
                                            </div>
                                            <div class=" form-group">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="isAcceptedTerms" class="styled"
                                                            required style=" width: 20px;height: 20px;"
                                                            @if ($item->isAcceptedTerms ?? 0 == 1) checked @endif>
                                                        I accept the
                                                        <a href="{{ route('terms_and_condition.index') }}" target="_blank"
                                                            rel="noopener">GeMEMIS terms and conditions</a>.
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">
                                                Get control number <i class="icon-arrow-right13" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- ******** END INVOICE DESCRIPTIONS --}}
                    @endif

                        {{-- REQUEST DISCOUNT --}}
                        <div id="Discount{{ $item->advice_note_id }}" class="modal fade text-left"
                            aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-bold text-center text-uppercase"> YOU ARE APPLYING
                                            DISCOUNT FOR <b>{{ $item->companyName }}</b></h5>
                                    </div>
                                    <div class="modal-body">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END REQUEST DISCOUNT  --}}



                    @if ($canExtendDeadline)
                        {{-- Extend deadline modal --}}
                        <div id="Extend{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title">Are you sure you whant to extend deadline from
                                            <b>{{ date('M d, Y', strtotime($item->advice_note_deadline)) }} </b>
                                            To New Date ?
                                        </h5>
                                    </div>
                                    <div class="modal-body">
                                        <form
                                            action="{{ route('adviceNoteDeadlines.update', ['adviceNoteDeadline' => $item->advice_note_id]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="extend_deadline" value="extend_deadline">
                                            <div class=" form-group">
                                                <label>Give reasons for Extending Invoice Deadline<i
                                                        class=" text-danger">*</i></label>
                                                <textarea name="reasons" class=" form-control" placeholder="Write your reasons here" required>{{ old('reasons') }}</textarea>
                                            </div>


                                            <div class=" form-group">
                                                <label> Select New Deadline Date<i class=" text-danger">*</i></label>
                                                <input type="date" name="expire_date" class=" form-control"
                                                    value="{{ old('expire_date') }}" min="{{ date('Y-m-d') }}"
                                                    required>
                                            </div>

                                            <div class=" text-right">
                                                <button type="button" data-dismiss="modal"
                                                    class=" btn btn-secondary">Close</button>
                                                <button type="submit" class="btn btn-primary ml-20"> Extend
                                                    Deadline</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End extend modal --}}
                    @endif

                    @if ($canCancel)
                        {{-- delete modal --}}
                        <div id="cancelInvoice{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title">Are you sure you whant to Cancel
                                            <b><q>{{ $item->advice_note_id }} ({{ $item->control_number }})</q></b>
                                            Invoice ?
                                        </h5>
                                    </div>
                                    <div class="modal-body">
                                        <form
                                            action="{{ route('adviceNotes.destroy', ['adviceNote' => $item->advice_note_id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class=" form-group ">
                                                <p><b class=" text-danger">NOTE!</b> This process is irrevesible,
                                                    please confirm your details before canceling this invoice</p>
                                                <label>Give reasons for Canceling this Invoice<i
                                                        class=" text-danger">*</i></label>
                                                <textarea name="reasons" class=" form-control" placeholder="Write your Cancelation reasons here" required></textarea>

                                            </div>

                                            <div class=" text-right">
                                                <button type="button" data-dismiss="modal"
                                                    class=" btn btn-secondary">Close</button>
                                                <button type="submit" class="btn btn-danger ml-20">Yes,
                                                    Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- delete modal --}}
                    @endif
                        {{-- *********** Upload Participation Form ******** --}}
                        <div id="participationForm{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-bold text-center"> UPLOAD PARTICIPATION FORM</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="#" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <div class=" form-group">
                                                <p class=" text-danger"><b>Note!</b>
                                                    After booking your Space download Participation Form, sign it and
                                                    upload it as attachment for your Application to be reviewed!
                                                </p>
                                            </div>

                                            <div class=" form-group col-md-12">
                                                <label>Signed Participation Form<i class="text-danger">*</i></label>
                                                <input type="file" class="form-control" name="participationForm">
                                            </div>

                                            <div class=" text-right">
                                                <button type="button" data-dismiss="modal"
                                                    class=" btn btn-secondary">Close</button>
                                                <button type="submit"
                                                    class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner">Get
                                                    Control Number</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ******** END UPLOAD PARTICIPATION FORM --}}

                    @include('advice_note.invoice', ['invoiceStylesLoaded' => true])
                @endforeach
            </div>


        </div>
    </div>
    </div>
    </div>
    <!-- /content area -->
    </div>
    <!-- /main content -->
</x-app-layout>


<div id="ConfirmInvoice{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">CONFIRM INVOICE
                    <b><q>{{ $item->advice_note_id }}</q></b>.
                </h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('adviceNotes.update', ['adviceNote' => $item->advice_note_id]) }}"
                    method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="confirm" value="confirm">
                    <div class="form-group">
                        <h2 class="display-block text-semibold">Payment Mode</h2>
                        <label class="radio-inline">
                            <input type="radio" name="payment_mode" value="1" class="styled"
                                checked="checked">
                            Full Payment
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="payment_mode" value="2" class="styled">
                            Partial Payment
                        </label>


                        <label class="radio-inline">
                            <input type="radio" name="payment_mode" value="3" class="styled">
                            Exactly Payment
                        </label>
                    </div>
                    <div class=" form-group">
                        <input type="date" name="invoiceDeadline" value="{{ $item->advice_note_deadline }}"
                            class=" form-control">
                    </div>
                    <div class=" text-right">
                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                        <button type="submit" class="btn btn-warning ml-20">Confirm
                            Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#myDiv').hide();
        // Listen for changes on the select element
        $('#mySelect').on('change', function() {
            const selectedValue = $(this).val();

            // Show or hide the div based on the selected value

            if (selectedValue === 'TZS') {
                $('#myDiv').show(); // Show the div
            } else {
                $('#myDiv').hide(); // Hide the div
            }
        });
    });

    // @if ($item->sp_invoice_no != null && $item->control_number == null)
    //     location.reload();
    // @endif
</script>
