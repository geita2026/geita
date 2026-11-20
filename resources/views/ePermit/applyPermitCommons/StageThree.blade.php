<div class="tab-pane @if ($item->applicationStatus == 5) active @endif" id="stageThree">

    <div class="alert alert-info alert-styled-left alert-bordered">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span
                class="sr-only">Close</span></button>
        <span class="text-semibold">You are almost to done! </span> Request Control Number for payment, Make payments to
        get your Permit.
    </div>

    <table class=" table table-bordered">
        <thead>

            <tr>
                <th>#</th>
                <th>InvoiceNo</th>
                <th>Control Number</th>
                <th>Invoice Amount</th>
                <th>Amount Paid</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $key => $invoice)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $invoice->invoiceNo }}</td>
                    <td class="text-center">
                        @if ($invoice->controlNumber == null)
                            @if ($invoice->spInvoiceNo != null)
                            <a href="{{ route('apply_permit.show',['apply_permit'=>$item->applicationId]) }}">Refresh<i class=" icon icon-reload-alt"></i></a>
                            @else
                                <a href="#" data-toggle="modal" data-target="#requestControlNumber"
                                    class=" btn btn-primary">Click to Generate Control Number</a>
                            @endif
                        @else
                            {{ $invoice->controlNumber }}
                        @endif

                    </td>
                    <td>{{ number_format($invoice->invoiceAmount, 2) }} {{ $invoice->currency }}</td>
                    <td>{{ number_format($invoice->amountPaid, 2) }} {{ $invoice->currency }}</td>
                    <td>
                        @if ($invoice->controlNumber != null)
                            <a target="_newtab"
                                href="{{ route('app_invoices.show', ['app_invoice' => $invoice->invoiceNo]) }}"
                                class=" btn btn-rounded bg-primary">Download Invoice <i
                                    class=" icon-file-download2"></i></a>
                        @endif
                        @if ($invoice->amountPaid != 0)
                            &capdot; <a href="{{ route('app_invoices.edit', ['app_invoice' => $invoice->invoiceNo]) }}"
                                class=" btn btn-rounded bg-success">Download Receipt <i
                                    class=" icon-file-download2"></i></a>
                        @endif

                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach ($invoices as $invoice)

<div id="requestControlNumber" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-uppercase">Request Control Number</h5>
            </div>
            <form action="{{ route('request_controlNo') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <h4>Invoice Amount: {{ number_format($invoice->invoiceAmount, 2) }} {{ $item->currency }}</h4>
                    <div class="">
                        <input type="hidden" name="invoiceNo" value="{{ $invoice->invoiceNo }}">
                        @if ($invoice->currency == 'USD')
                            <div class=" form-group col-md-12">
                                <label>I want to pay in<i class=" text-danger">*</i><span class=" text-muted">(Exhange
                                        Rate is used only if you want to pay in TZS)</span></label>
                                <select name="currency" class=" form-control" id="mySelect">
                                    <option value="USD">USD (US Dollar)</option>
                                    <option value="TZS">TZS (Tanzania Shilling)</option>
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>
                            <div id="myDiv">
                                <div class=" form-group col-md-12">
                                    <label>Exchange Rate for {{ date('M d, Y') }}<i class=" text-danger">*</i></label>
                                    <input type="text" name="exchangeRate" value="{{ number_format($rate, 2) }} TZS"
                                        class="form-control" readonly>
                                    <span class=" text-danger text-error eventType"></span>
                                </div>
                                <div class=" form-group col-md-12">
                                    <h3><b>New Amount After Conversion will be:
                                        </b>{{ number_format($rate * $invoice->invoiceAmount, 2) }} TZS</h3>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                            class="ladda-label">Request Control Number</span></button>
                </div>
            </form>
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
</script>

@endforeach
