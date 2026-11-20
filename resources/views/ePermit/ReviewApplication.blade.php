<x-app-layout>
    <title>{{ config('global.MODULE_TWO') }} | Application Review</title>

    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> ePermit</a></li>
                <li class="active">Application</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    @foreach ($application as $item)
        <!-- /page header -->
        <div class="content">

            <!-- User profile -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="tabbable">
                        <div class="tab-content">


                            <div class="tab-pane fade in active" id="activity">

                                <!-- Timeline -->
                                <div class="timeline timeline-left content-group">
                                    <div class="timeline-container">

                                        <!-- Sales stats -->
                                        <div class="timeline-row">
                                            <div class="timeline-icon">
                                                <a
                                                    href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}">
                                                    @if ($item->companyLogo)
                                                        <img src="{{ asset('profile_images/' . $item->companyLogo) }}"
                                                            alt="">
                                                    @else
                                                        <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                            alt="">
                                                    @endif
                                                </a>
                                            </div>

                                            <div class="panel panel-flat timeline-content">
                                                <div class="panel-heading">
                                                    <h6 class="panel-title"><b class=" bg-info p-10 btn-rounded">COMPANY
                                                            REGISTRATION & COMPLIANCE</b></h6>
                                                    <div class="heading-elements">
                                                        <span class="heading-text"><i
                                                                class="icon-history position-left text-success"></i><b>Submited:</b>
                                                            {{ date('M d , Y', strtotime($item->applicationDate)) }}</span>

                                                        <ul class="icons-list">
                                                            <li><a data-action="reload"></a></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="panel-body">
                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Company Name:</b> {{ $item->companyName }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Company Email:</b> {{ $item->companyEmail }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Company Phone:</b> {{ $item->companyPhone }}</h4>
                                                        </div>
                                                    </div>
													<hr>

                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Business Registration No:</b>
                                                                {{ $item->registrationNo }} | 
                                                               <label class=" label label-default">{{ $item->brnStatus }}</label>
                                                                </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Tax Identification No:</b> {{ $item->tinNo }} |
                                                                <label class=" label label-default">{{ $item->tinStatus }}</label>
                                                                
                                                            </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Company Sector:</b> {{ $item->sectorName }}
                                                            </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Company Location:</b> {{ $item->companyLocation }}
                                                            </h4>
                                                        </div>
                                                        
                                                        <div class=" col-md-4">
                                                            <h4><a href="{{ route('company_profile',['companyId'=>$item->registrationID]) }}">View full Profile <i class=" icon icon-arrow-right13"></i></a> 
                                                            </h4>
                                                        </div>
                                                    </div>
													<hr>
                                                    <div class=" col-md-12 text-center p-10"
                                                        style="background-color: rgb(191, 241, 241);">
                                                        <i>Contact Person Information</i>
                                                    </div>

                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Name:</b> {{ $item->personalName }} </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Phone Number:</b> {{ $item->personalPhone }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Email Address:</b> {{ $item->personalEmail }}</h4>
                                                        </div>
                                                    </div>
													
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /sales stats -->


                                        <!-- Blog post -->
                                        <div class="timeline-row">
                                            <div class="timeline-icon">
                                                <a
                                                    href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}">
                                                    @if ($item->companyLogo)
                                                        <img src="{{ asset('profile_images/' . $item->companyLogo) }}"
                                                            alt="">
                                                    @else
                                                        <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                            alt="">
                                                    @endif
                                                </a>
                                            </div>

                                            <div class="panel panel-flat timeline-content">
                                                <div class="panel-heading">
                                                    <h6 class="panel-title"><b class=" bg-info p-10 btn-rounded">TRADE
                                                            PROMOTION EVENT DETAILS</b></h6>
                                                        <div class="heading-elements">
                                                            <span class="heading-text"><i
                                                                    class="icon-checkmark-circle position-left text-success"></i>
                                                                </span>

                                                        </div>
                                                </div>

                                                <div class="panel-body">
                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Title:</b> {{ $item->eventTitle }} </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Theme:</b> {{ $item->eventTheme }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Objectives:</b> {{ $item->eventObjectives }}
                                                            </h4>
                                                        </div>
                                                    </div>
													<hr>
                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Type:</b> {{ $item->promotionEventTypeName }}
                                                            </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Sector:</b> {{ $item->sectorName }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Website:</b><a target="_newtab"
                                                                    href="{{ $item->eventWebsite }}">{{ $item->eventWebsite }}</a>
                                                            </h4>
                                                        </div>
                                                    </div>
													<hr>
                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b><i class=" icon-calendar"></i> Start Date:</b>
                                                                {{ date('M d, Y', strtotime($item->startFrom)) }} </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b><i class=" icon-calendar"></i> End Date:</b>
                                                                {{ date('M d, Y', strtotime($item->endDate)) }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Approximate No Of Local Participants:</b>
                                                                {{ number_format($item->localParticipants) }} </h4>
                                                        </div>
                                                    </div>
													<hr>
                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Approximate No Of Foreign Participants:</b>
                                                                {{ number_format($item->foreignParticipants) }} </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Event Entry Fee</b>
                                                                {{ number_format($item->entryFee) }} TZS</h4>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>


                                        </div>
                                        <!-- /blog post -->


                                        <div class="timeline-row">
                                            <div class="timeline-icon">
                                                <a
                                                    href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}">
                                                    @if ($item->companyLogo)
                                                        <img src="{{ asset('profile_images/' . $item->companyLogo) }}"
                                                            alt="">
                                                    @else
                                                        <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                            alt="">
                                                    @endif
                                                </a>
                                            </div>

                                            <div class="panel panel-flat timeline-content">
                                                <div class="panel-heading">
                                                    <h6 class="panel-title"><b class=" bg-info p-10 btn-rounded">TRADE
                                                            PROMOTION EVENT VENUE DETAILS</b></h6>
                                                        <div class="heading-elements">
                                                            <span class="heading-text"><i
                                                                    class="icon-checkmark-circle position-left text-success"></i>
                                                                </span>

                                                        </div>
                                                </div>

                                                <div class="panel-body">
                                                    <div class=" row">
                                                        <div class=" col-md-4">
                                                            <h4><b>Venue Name / Address:</b> {{ $item->venueAddress }}
                                                            </h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Type Of Venue:</b> {{ $item->typeOfBuilding }}</h4>
                                                        </div>
                                                        <div class=" col-md-4">
                                                            <h4><b>Venue Owner / Contact Person Phone Number:</b>
                                                                {{ $item->venueOwnerAddress }}</h4>
                                                        </div>
                                                    </div>

                                                </div>


                                            </div>


                                        </div>


                                        <div class="timeline-row">
                                            <div class="timeline-icon">
                                                <a
                                                    href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}">
                                                    @if ($item->companyLogo)
                                                        <img src="{{ asset('profile_images/' . $item->companyLogo) }}"
                                                            alt="">
                                                    @else
                                                        <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                            alt="">
                                                    @endif
                                                </a>
                                            </div>

                                            <div class="panel panel-flat timeline-content">
                                                <div class="panel-heading">
                                                    <h6 class="panel-title"><b class=" bg-info p-10 btn-rounded">
                                                            APPLICATION REVIEW / VERIFY & APPROVAL MENU</b> <b>Action Officer:</b> <i class=" text-danger">{{ $item->first_name }} {{ $item->last_name }}</i></h6>
                                                        <div class="heading-elements">
                                                            <span class="heading-text"><i
                                                                    class="icon-checkmark-circle position-left text-success"></i>
                                                                </span>

                                                        </div>
                                                </div>

                                                <div class="panel-body">
                                                   @include('ePermit.applyPermitCommons.ReviewLogics')
                                                </div>


                                            </div>


                                        </div>



                                        <!-- Invoices -->
                                        @foreach ($invoices as $data)
                                        <div class="timeline-row">
                                            <div class="timeline-icon">
                                                <div class="bg-warning-400">
                                                    <a
                                                        href="{{ route('review_applications.show', ['review_application' => $item->applicationId]) }}">
                                                        @if ($item->companyLogo)
                                                            <img src="{{ asset('profile_images/' . $item->companyLogo) }}"
                                                                alt="">
                                                        @else
                                                            <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                                alt="">
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
												<div
													class="panel border-left-lg border-left-danger invoice-grid timeline-content">
													<div class="panel-body">
														<div class="row">
															<div class="col-sm-6">
																<h6 class="panel-title"><b
																		class=" bg-info p-10 btn-rounded">PAYMENT
																		DETAILS</b></h6>
																	<ul class="list list-unstyled">
																		<br>
																		<li>Invoice #: &nbsp;{{ $data->invoiceNo }}</li>
																		<li>Issued on: <span
																				class="text-semibold">{{ date('M d, Y',strtotime($data->invoiceDate)) }}</span>
																		</li>
																	</ul>
															</div>

															<div class="col-sm-6">
																<h6 class="text-semibold text-right no-margin-top">
																	<b>Invoice Amount:</b> {{ number_format($data->invoiceAmount,2) }} {{ $data->currency }}</h6>
																<ul class="list list-unstyled text-right">
																	<li><b>Payment Method:</b> <span
																			>GePG</span></li>
                                                                            <li><b>Control Number:</b> 
                                                                                <span>
                                                                                    @if ($data->controlNumber==Null)
                                                                                    <code>UNREQUESTED</code>    
                                                                                    @else
                                                                                        {{ $data->controlNumber }}
                                                                                    @endif
                                                                                    
                                                                                </span></li>
																	<li class="dropdown">
																		Status: 
                                                                        @if ($data->amountPaid==$data->invoiceAmount)
                                                                        <label class=" label label-rounded bg-success">PAID</label>
                                                                        @else
                                                                        <label class=" label label-rounded bg-success">UNPAID</label> 
                                                                        @endif
                                                                        
																	</li>
																</ul>
															</div>
														</div>
													</div>

													<div class="panel-footer panel-footer-condensed">
														<div class="heading-elements">
															<span class="heading-text">
																<span
																	class="status-mark border-danger position-left"></span>
																Due: <span class="text-semibold">{{ date('M d, Y',strtotime($data->expireDate)) }}</span>
															</span>

															

                                                            @if ($item->applicationStatus==6)
                                                            <ul
                                                            class="list-inline list-inline-condensed heading-text pull-right">
                                                            <li><a href="{{ route('apply_permit.edit',['apply_permit'=>$item->applicationId]) }}" target="_newtab" class=" btn btn-success">Export Permit <i
                                                                        class=" icon icon-certificate"></i></a>
                                                                    </li>
                                                        </ul>
                                                            @endif
                                                            @if ($data->controlNumber!=Null)
                                                            <ul
                                                            class="list-inline list-inline-condensed heading-text pull-right">
                                                            <li><a href="{{ route('app_invoices.show',['app_invoice'=>$data->invoiceNo]) }}" target="_newtab" class=" btn btn-primary">Download Invoice <i
                                                                        class="icon-file-download2"></i></a>
                                                                    </li>
                                                        </ul>
                                                            @endif
														</div>
													</div>
												</div>
											</div>
                                        </div>
                                        @endforeach
                                        <!-- /invoices -->


                                    </div>
                                </div>
                                <!-- /timeline -->

                            </div>


                            
                        </div>
                    </div>
                </div>


            </div>
            <!-- /user profile -->
        </div>
    @endforeach
</x-app-layout>

<script>
    function checkNumber() {
        const numberInput = document.getElementById('numberInput').value;
        const extraInputDiv = document.getElementById('extraInput');

        if (numberInput > 0) {
            extraInputDiv.style.display = 'block';
        } else {
            extraInputDiv.style.display = 'none';
        }
    }
</script>

<script>
    @if ($item->applicationStatus >= 2)
        const inputs = document.querySelectorAll('.applicationForm');
        inputs.forEach(input => input.disabled = true);
    @endif
</script>
