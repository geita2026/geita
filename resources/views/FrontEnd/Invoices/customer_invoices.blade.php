<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('adviceNotes.index') }}"><i class=" icon-file-spreadsheet position-left"></i>Customer Invoice</a></li>
           <li class="active">Invoice List</li>
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
        <div class="panel-heading" style="background-color: rgb(155, 208, 224)">
            <h6 class="panel-title text-uppercase text-bold text-center">List Of All Invoices Generated</h6>
        </div>


<div class=" panel-body">
        <div class=" form-group mt-20">
            <table class="table table-bordered datatable-button-html5-columns table-striped">
                <thead>
                    <tr>

                        <th class=" text-bold text-center">INVOICE ID</th>
                        <th class=" text-bold text-center">CONTROL NUMBER</th>
                        <th class=" text-bold text-center">AMOUNT INVOICED</th>
                        <th class=" text-bold text-center">AMOUNT PAID</th>
                        <th class=" text-bold text-center">BALANCE</th>
                        <th class=" text-bold text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                   @foreach ($advice_note as $item)
                   <tr>

                    <td><a href="{{ route('my_invoices.show', ['my_invoice' => $item->advice_note_id]) }}" class=" text-bold" title="Click here to view {{ $item->advice_note_id }} Advice Note details">{{ $item->advice_note_id }}</a></td>

                    <td class=" text-center" title="Generation Date: {{ date('M d, Y', strtotime($item->advice_note_created_at)) }}. Expire Date: {{ date('M d, Y',strtotime($item->advice_note_deadline)) }}">
                        <a> @if($item->control_number==Null). . . . @else {{ $item->control_number }} @endif   </a>
                   </td>

                   <td class=" text-right"> {{ number_format($item->total_amount,2) }} {{ $item->currency }}</td>
                   <td class=" text-center">{{ number_format($item->amount_paid,2) }} {{ $item->currency }}</td>
                   <td class=" text-right @if($item->balance>0) text-warning @else text-success @endif"> {{ number_format($item->balance,2) }} {{ $item->currency }}</td>



                    <td class="text-center">
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">

                                <li>
                                    <a href="{{ route('my_invoices.show',['my_invoice'=>$item->advice_note_id]) }}" class=" text-primary"><i class=" icon-pencil7"></i>View Advice Note</a>
                                </li>


                                   @can('DELETE_ADVICE_NOTE')
                                    <li>
                                        <a  data-toggle="modal" data-target="#deleteAdviceNote{{ $item->advice_note_id }}" class=" text-danger"><i class="icon-bin"></i> Delete Advice Note</a>
                                    </li>
                                    @endcan


                                </ul>
                            </li>
                        </ul>
                    </td>

                    <div id="deleteAdviceNote{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title">Are you sure you whant to Cancel <b><q>{{ $item->advice_note_id }}</q></b></h5>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('adviceNotes.destroy', ['adviceNote' => $item->advice_note_id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class=" form-group">
                                            <label>Give reasons for Canceling this Invoice<i class=" text-danger">*</i></label>
                                            <textarea name="reasons" class=" form-control" placeholder="Write your reasons here" required></textarea>
                                        </div>
                                        <div class=" text-right">
                                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                            <button type="submit" class="btn btn-danger ml-20" >Yes, Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                </tr>
                @php $i++; @endphp
                   @endforeach
                </tbody>

            </table>

        </div>

            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>


@foreach ($advice_note as $item)

<div id="ShowInvoice{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-body">
                <!-- Invoice template -->
					<div class="panel panel-white">
						<div class="panel-heading">
							<h6 class="panel-title text-uppercase text-bold">Invoice</h6>
							<div class="heading-elements">
								{{-- <button type="button" class="btn btn-default btn-xs heading-btn"><i class="icon-file-check position-left"></i> Save</button> --}}
								<button type="button" class="btn btn-default btn-xs heading-btn"><i class="icon-printer position-left"></i> Print</button>
		                	</div>
						</div>

						<div class="panel-body no-padding-bottom">
							<div class="row">
								<div class="col-sm-6 content-group">
									<img src="{{ asset('images/logo.jpg') }}" class="content-group mt-10" alt="" style="width: 120px;">
		 							<ul class="list-condensed list-unstyled">
										<li>Geita Mineral Exhibition Secretariat</li>
										<li>Dar es salaam, Tanzania</li>
										<li>+255 753 427 137</li>
									</ul>
								</div>

								<div class="col-sm-6 content-group">
									<div class="invoice-details">
										<h5 class="text-uppercase text-semibold">{{ $item->advice_note_id }}</h5>
										<ul class="list-condensed list-unstyled">
											<li>Invoice date: <span class="text-semibold">{{ date('M d, Y',strtotime($item->advice_note_created_at)) }}</span></li>
											<li>Due date: <span class="text-semibold">{{ date('M d, Y',strtotime($item->advice_note_deadline)) }}</span></li>
										</ul>
									</div>
								</div>
							</div>


                            <div id="data">

                            </div>

							<div class="row">
								<div class="col-md-6 col-lg-9 content-group">
									<span class="text-muted">Invoice To:</span>
		 							<ul class="list-condensed list-unstyled">
										<li><h5>{{ Auth::user()->first_name }}</h5></li>
										<li>{{ Auth::user()->phone_number }}</li>
										<li>{{ Auth::user()->email }}</li>
										<li>{{ $item->companyLocation }}</li>
									</ul>
								</div>

								<div class="col-md-6 col-lg-3 content-group">
									<span class="text-muted">Payment Details:</span>
									<ul class="list-condensed list-unstyled invoice-payment-details">
										<li><h5>Total Due: <span class="text-right text-semibold">8,750 USD</span></h5></li>
										<li>Control No: <span class=" text-bold"> 997 964 532 637 238</span></li>
                                        <li>Bank: <span class=" text-bold"> Azania Bank</span></li>
										<li>Account No: <span> Tanzania</span></li>
										<li>City: <span>Dar es salaam</span></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="table-responsive">
						    <table class="table table-lg">
						        <thead>
						            <tr>
						                <th>Description</th>
						                <th class="col-sm-1">Rates</th>
						                <th class="col-sm-1">Units</th>
						                <th class="col-sm-1">Total</th>
						            </tr>
						        </thead>
						        <tbody>
						            <tr>
						                <td>
						                	<h6 class="no-margin">Space Fee</h6>
						                	<span class="text-muted">Booked space (KLM-001, KLM-006)</span>
					                	</td>
						                <td> 1,000 USD</td>
						                <td>9</td>
						                <td><span class="text-semibold">9,000 USD</span></td>
						            </tr>
						            <tr>
						                <td>
						                	<h6 class="no-margin">Exhibition Badges</h6>
						                	<span class="text-muted"> Special badges for all exhibitors</span>
					                	</td>
						                <td>0.00 USD</td>
						                <td>2</td>
						                <td><span class="text-semibold">0.00 USD</span></td>
						            </tr>
						        </tbody>
						    </table>
						</div>

						<div class="panel-body">
							<div class="row invoice-payment">
								<div class="col-sm-7">
									<div class="content-group">
										<h6>Authorized person</h6>
										<div class="mb-15 mt-15">
											<img src="assets/images/signature.png" class="display-block" style="width: 150px;" alt="">
										</div>

										<ul class="list-condensed list-unstyled text-muted">
											<li>Remigius Kabandwa</li>
											<li>255753427137</li>
											<li>remixkabandwa@gmail.com</li>
											<li class=" text-bold">ICT OFFICER</li>
										</ul>
									</div>
								</div>

								<div class="col-sm-5">
									<div class="content-group">
										<h6>Total due</h6>
										<div class="table-responsive no-border">
											<table class="table">
												<tbody>
													<tr>
														<th>Subtotal:</th>
														<td class="text-right">9,000 USD</td>
													</tr>
													<tr>
														<th>VAT: <span class="text-regular">(18%)</span></th>
														<td class="text-right">122.6 USD</td>
													</tr>
													<tr>
														<th>Total:</th>
														<td class="text-right text-primary"><h5 class="text-semibold">9,122.6 USD</h5></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

							<h6>Other information</h6>
							<p class="text-muted">Thank you for participarting 47<sup>th</sup> Dar es salaam Internationa Trade Fair. This invoice can be paid via Control number and Bank transfer Only. Payment is due within  date limit as started above. You are required to make payment on deadline otherwise your booked space will be canceled.</p>
						</div>
					</div>
					<!-- /invoice template -->




                    <div class=" text-right">
                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                    </div>
            </div>
        </div>
    </div>
</div>

@endforeach

{{--  --}}


<script>
    $(document).ready(function(){
        getParticulars();
        function getParticulars(){
            $.ajax({
                type:"GET",
                url:"/get_particulars",
                dataType:"json",
                success:function(response){
                    console.log(response.particulars);
                    $.each(response.particulars,function(key,item){
                        $('#data').append('
                        '+item.particularName+'
                        ');
                    });

                }
            })
        }
        });
</script>
