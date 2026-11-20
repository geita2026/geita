<div class="tab-pane fade" id="Invoice">
    <div class="panel panel-flat">
        <div class=" col-md-12 table-header form-group">
            <h2 class=" text-center  text-bold">
                INVOICE DEFAULT SETTING
            </h2>
        </div>
        <div class="panel-body">
                <div class="form-group">

                     <div class=" row form-group">
                        <div class="col-md-6">
                            <label>Invoice Other Information</label>
                            <textarea name="invoice_other_information" rows="4" class=" form-control" placeholder="Enter invoice other information">{{ $item->invoice_other_information ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Invoice Deadline Date</label>
                                <input type="date" name="invoice_deadline_date" class="form-control daterange-single" value="{{ $item->invoice_deadline_date ?? date('d-m-Y') }}">
                                <span class=" text-muted"><b>CURRENT DEADLINE: ({{ $item->invoice_deadline_date ?? 'Not set' }})</b></span>
                        </div>

                     </div>

                </div>
        </div>
    </div>
</div>
