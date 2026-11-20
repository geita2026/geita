<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space Booking Inquiries</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Space Requests</a></li>
                <li class="active">View All Space Requests</li>
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

                <div class="form-group">
                    <a href="{{ route('space_booking_inquiries.create') }}" class="btn btn-default text-primary"><i
                            class=" icon-arrow-left12"></i> Back</a>
                </div>

                @foreach ($inquiries as $item)
                    <span class=" text-danger">
                        @error('permission_name')
                            {{ $message }}
                        @enderror
                    </span>
                    <div class=" col-md-12 table-header form-group">
                        <h2 class=" text-center text-uppercase"><b>{{ $item->companyName }}</b> SPACE BOOKING REQUESTS.
                        </h2>
                    </div>

                    <div class="form-group col-md-6">
                        <h4><b>Request Reg Number:</b> {{ $item->enquiryNo }}</h4>
                        <h4>
                            <b>Company Name:</b> {{ $item->companyName }}
                        </h4>
                        <h4>
                            <b>Company Email:</b> {{ $item->companyEmail }}
                        </h4>
                        <h4>
                            <b>Company Phone: </b> {{ $item->companyPhone }}
                        </h4>
                        <h4>
                            <b>Country: </b> {{ $item->country }}
                        </h4>
                        <h4>
                            <b>Product Expected to be exhibited: </b> {{ $item->products }}
                        </h4>
                        <h4>
                            <b>Space Of Interest:</b> {{ $item->spaceCategory }}
                        </h4>
                        <h4>
                            <b>Request Status: </b>
                            @if ($item->inquiryStatus == 0)
                                <label class=" label label-warning">PENDING</label>
                            @elseif ($item->inquiryStatus == 1)
                                <label class=" label label-success p-10">APPROVED AND SPACE PROVIDED</label>
                            @elseif ($item->inquiryStatus == 2)
                                <label class=" label label-info p-10">APPROVED, WAITING FOR SPACE AVAILABILITY</label>
                            @elseif ($item->inquiryStatus == 3)
                                <label class=" label label-danger p-10">REJECTED, SPACE NOT AVAILABLE</label>
                            @endif
                        </h4>

                    </div>

                    <div class="form-group col-md-6">
                        <div class=" col-md-4 ">
                            <button class=" btn btn-primary" data-toggle="modal" data-target="#ViewCompanyProfile">View
                                Company Profile <i class=" icon-file-pdf"></i></button>
                        </div>

                        <div class=" col-md-4  ml-10 ">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#ViewCompanyRegCert"> View
                                Certificate Of Registration <i class=" icon-file-pdf"></i></button>
                        </div>
                    </div>
                    <div class=" col-md-12">
                        <h4>
                            <b>MOREDESCRIPTION:-</b>
                            <code>
                                {{ $item->moreDescription }}
                            </code>
                        </h4>
                    </div>


                    <div class=" form-group col-md-12">
                        <form
                            action="{{ route('space_booking_inquiries.update', ['space_booking_inquiry' => $item->enquiryId]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class=" form-group">
                                <label>Request Acceptance<i class=" text-danger">*</i></label>
                                <select name="inquiryStatus" class=" form-control" required>
                                    <option value="">- - - Select Acceptance Status - - -
                                    </option>
                                    <option value="1">APPROVED AND SPACE PROVIDED</option>
                                    <option value="2">APPROVED, WAITING FOR SPACE
                                        AVAILABILITY</option>
                                    <option value="3">REJECTED, SPACE NOT AVAILABLE
                                    </option>

                                </select>
                            </div>
                            <div class=" form-group">
                                <label>Equiry FeedBack<i class=" text-danger">*</i></label>
                                <textarea rows="5" name="equiryFeedBack" class=" form-control" placeholder="Write feedback Here" required>{{ $item->equiryFeedBack }}</textarea>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Attend
                                    Customer</button>
                            </div>
                        </form>
                    </div>



                    <div id="ViewCompanyRegCert" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-blue">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title"> COMPANY CERTIFICATE OF REGISTRATION</h4>
                                </div>
                                <div class="modal-body">
                
                                    <div class=" table-responsive">
                                        <iframe src="{{ asset('OnlineBookingFiles/'.$item->registrationCertificate) }}" style="width: 100%;height:800px;">
                
                                        </iframe>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /dashboard content -->
    <div id="ViewCompanyProfile" class="modal fade" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> COMPANY PROFILE</h4>
                </div>
                <div class="modal-body">

                    <div class=" table-responsive">
                        <iframe src="{{ asset('OnlineBookingFiles/'.$item->companyProfile) }}" style="width: 100%;height:800px;">

                        </iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



            </div>
            @endforeach

        </div>
    </div>
    

    {{--  end  --}}
</x-app-layout>
