<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoices</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> ePermit </a></li>
                <li class="active">View All permit Invoices</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body table-responsive">
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">TRADE PROMOTION EVENT PERMIT INVOICE LIST</h4>
                </div>
                
                <div class=" form-group">
                    <table class="table datatable-basic table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th class=" text-bold">Invoice No</th>
                                <th class=" text-bold">Company Name</th>
                                <th class=" text-bold">Trade Promotion Event Title</th>
                                <th class=" text-bold">Control Number</th>
                                <th class=" text-bold">Invoice Amount</th>
                                <th class=" text-bold">Amount Paid</th>
                                <th class=" text-bold">Invoice Status</th>
                                <th class=" text-bold">Reviewed By</th>
                                <th class=" text-bold">Invoice Date</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
    
                            @foreach ($invoices as $item)
                                <tr>
                                    <td><a href="{{ route('app_invoices.show',['app_invoice'=>$item->invoiceNo]) }}">{{ $item->invoiceNo }}</a></td>
                                    <td class=" text-uppercase"><a href="{{ route('review_applications.show',['review_application'=>$item->invoiceNo]) }}">{{ $item->companyName }}</a></td>
                                    <td>{{ $item->eventTitle }}</td>
                                    <td>{{ $item->controlNumber }}</td>
                                    <td>{{ number_format($item->invoiceAmount,2) }} {{ $item->currency }}</td>
                                    <td>{{ number_format($item->amountPaid,2) }} {{ $item->currency }}</td>
                                    <td class=" text-center">
                                        @if ($item->amountPaid == $item->invoiceAmount)
                                        <label class=" label label-rounded label-success">PAID</label>
                                        @elseif ($item->amountPaid ==0)
                                        <label class=" label label-rounded label-danger">NOT PAID</label>
                                        @elseif ($item->amountPaid != $item->invoiceAmount)
                                    <label class=" label label-rounded label-default">PARTIAL PAID</label>
                                    @endif
                                    </td>
                                    <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}</td>
                                    <td>{{ date('M d, Y h:i:s', strtotime($item->invoiceDate)) }}</td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="{{ route('app_invoices.show',['app_invoice'=>$item->invoiceNo]) }}"
                                                            class=" btn btn-info">View Invoice <i
                                                                class=" icon-eye"></i></a>
                                                    </li>
                                                    <li>
                                                        <a href="" data-toggle="modal"
                                                            data-target="#deleteInvoice{{ $item->invoiceNo }}"
                                                            class="btn btn-danger"> Cancel Invoice <i
                                                                class=" icon-bin"></i></a>
    
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>

                                    

                                    {{-- delete modal --}}
                                    <div id="deleteInvoice{{ $item->invoiceNo }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to Cancel this Invoice?
                                                        ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('app_invoices.destroy', ['app_invoice' => $item->invoiceNo]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class=" form-group">
                                                            <textarea name="cancelReasons" class=" form-control" required placeholder="Cancellation Reasons"></textarea>
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
                                    {{-- DELETE --}}
    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>
<script>
    var select = document.getElementById('selection');
select.onchange = function(){
    this.form.submit();
};

</script>