<title>
    {{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Open+Sans|Rock+Salt|Shadows+Into+Light|Cedarville+Cursive">
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
                    <div class=" row form-group">
                        {{-- <div class=" col-md-2 text-center" style="font-size:1vmin">
                            <b style="font-size:150%;">PAID: <b
                                    class=" text-success">{{ number_format($item->amount_paid, 2) }}
                                    {{ $item->currency }}</b></b>
                            <b style="font-size:150%;" class=" ml-10">|</b>
                            <b style="font-size:150%;" class=" ml-10">BALANCE: <b
                                    class=" text-warning">{{ number_format($item->balance, 2) }}
                                    {{ $item->currency }}</b></b>
                        </div> --}}
                        <div class=" col-md-9">
                            @if ($exhibitor)
                                @if ($item->control_number == null)
                                    <div class="alert alert-info no-border">
                                        <button type="button" class="close"
                                            data-dismiss="alert"><span>&times;</span><span
                                                class="sr-only">Close</span></button>
                                        Congratulations for booking space for the Geita Mineral
                                        Exhibition, Please request
                                        control number Now.
                                    </div>
                                @else
                                    @if (Auth::user()->account_type == 1)
                                        @if ($exhibitor->participationCommitimentFile != null)
                                            <a href="{{ $exhibitor->participationCommitimentFile }}"
                                                class=" btn btn-primary">Preview Signed Participation Form <i
                                                    class=" icon icon-file-eye2"></i></a>
                                        @endif
                                    @else
                                        @if ($item->control_number == null)
                                            <div class="alert alert-info no-border">
                                                <button type="button" class="close"
                                                    data-dismiss="alert"><span>&times;</span><span
                                                        class="sr-only">Close</span></button>
                                                Congratulations for booking space for the Geita Mineral
                                                Exhibition, Control
                                                number will be given After your Application being reviewed! This may
                                                take one to two working hours.
                                            </div>
                                        @else
                                            <div class="alert alert-success no-border">
                                                <button type="button" class="close"
                                                    data-dismiss="alert"><span>&times;</span><span
                                                        class="sr-only">Close</span></button>
                                                Congratulations for being Accepted as Exhibitor for the Geita
                                                Mineral Exhibition. @if ($item->amout_paid == 0)
                                                    <b class=" text-danger">Note! Your booking will be canceled
                                                        automatically by the system if you fail to pay your full invoice
                                                        Amount before
                                                        {{ date('M d, Y h:i:s', strtotime($item->advice_note_deadline)) }}
                                                @endif
                                                </b>.
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                        <div class=" col-md-2 text-center">
                            @if ($item->sp_invoice_no == null)
                                <a data-toggle="modal" data-target="#requestControlNumber{{ $item->advice_note_id }}"
                                    class="text-bold btn btn-primary" title="Request Control Number">Request
                                    Control Number<i class=" icon-arrow-right13"></i>
                                </a>
                            @endif
                        </div>
                        <div class="col-md-1 text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#"
                                        class="dropdown-toggle text-primary text-bold btn btn-default border-success"
                                        data-toggle="dropdown">
                                        Actions <i class="icon-menu4"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">

                                        {{-- Approve Control Number --}}
                                        @if ($item->sp_invoice_no == null)
                                            @can('Request ControlNumber')
                                                <li>
                                                    <a data-toggle="modal"
                                                        data-target="#requestControlNumber{{ $item->advice_note_id }}"
                                                        class=" text-primary text-bold"
                                                        title="Request
                                                        Control Number">Request
                                                        Control Number<i class=" icon-arrow-right13"></i>
                                                    </a>
                                                </li>
                                            @endcan
                                        @endif

                                        {{-- ********download invoice********* --}}
                                        @can('Download Invoice')
                                            @if ($item->control_number)
                                                <li>
                                                    <a onclick=" event.preventDefault();document.getElementById('download_invoice').submit();"
                                                        class=" text-primary text-bold" title="Download Invoice">Download
                                                        Invoice<i class=" icon-arrow-right13"></i></a>
                                                </li>
                                            @endif
                                        @endcan
                                        {{-- ******** end download invoice ****** --}}


                                        {{-- Apply Discount --}}
                                        @if ($item->sp_invoice_no == null && $item->advice_note_status != 'CANCELED')
                                            @can('apply-discount')
                                                <li>
                                                    <a href="{{ route('review_invoice_discount', ['id' => $item->advice_note_id]) }}"
                                                        class=" text-primary text-bold" title="Download Invoice">Manage
                                                        Discount<i class="icon-arrow-right13"></i></a>
                                                </li>
                                            @endcan
                                        @endif
                                        {{-- End Apply discount --}}

                                        {{-- ****EXTEND DEADLINE********* --}}
                                        @can('Extend Deadline')
                                            @if (date('Ymd', strtotime($item->advice_note_deadline)) < date('Ymd') &&
                                                    $item->control_number != null &&
                                                    $item->advice_note_status != 'CANCELED')
                                                <li>
                                                    <a data-toggle="modal" data-target="#Extend{{ $item->advice_note_id }}"
                                                        class=" text-primary"><i class="icon-arrow-right13"></i> Extend
                                                        Deadline</a>
                                                </li>
                                            @endif
                                        @endcan
                                        {{-- ******END EXTEND DEADLINE******* --}}

                                        {{-- ******* CANCEL INVOICE ******* --}}
                                        @can('Cancel Invoices')
                                            @if ($item->amount_paid == 0 && $item->advice_note_status != 'CANCELED')
                                                <li>
                                                    <a data-toggle="modal"
                                                        data-target="#cancelInvoice{{ $item->advice_note_id }}"
                                                        title="Cancel Advice Note" class=" text-danger"><i
                                                            class="icon-arrow-right13"></i> Cancel Invoice</a>
                                                </li>
                                            @endif
                                        @endcan
                                        {{-- ****** END CANCELE INVOICE **** --}}

                                    </ul>
                                </li>
                            </ul>
                        </div>

                        {{-- *********** INVOICE Control Number ******** --}}
                        <div id="requestControlNumber{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-bold text-center"> CONTROL NUMBER REQUEST</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form
                                            action="{{ route('adviceNotes.update', ['adviceNote' => $item->advice_note_id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="source" value="1">
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
                                                        {{-- Placeholder domain: replace with the official site when it is confirmed. --}}
                                                        By accessing or using <a
                                                            href="https://gememis.go.tz"
                                                            target="_newtab">gememis.go.tz</a>, you agree to
                                                        be bound by <a href="{{ route('terms_and_condition.index') }}"
                                                            target="_newtab">Terms
                                                            and Conditions</a>. If
                                                        you
                                                        do not agree with any part of these terms, please do not use our
                                                        service.
                                                    </label>
                                                </div>
                                            </div>
                                            <div class=" text-right form-group">
                                                <button type="button" data-dismiss="modal"
                                                    class=" btn btn-secondary">Close</button>
                                                <button type="submit"
                                                    class="btn btn-primary ml-20 btn-ladda btn-ladda-spinner">Get
                                                    Control Number <i class=" icon icon-arrow-right13"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ******** END INVOICE DESCRIPTIONS --}}

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

                    </div>
                    <hr>
                    <div style="font-size: 12px;">
                        @include('advice_note.invoice')
                    </div>
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


{{-- download form --}}
<form action="{{ route('download_invoice') }}" method="POST" id="download_invoice" target="_blank">
    @csrf
    <input type="hidden" name="id" value="{{ $item->advice_note_id }}">
    @method('GET')
</form>
{{-- end --}}

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
