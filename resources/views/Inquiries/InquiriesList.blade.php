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

                <span class=" text-danger">
                    @error('permission_name')
                        {{ $message }}
                    @enderror
                </span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">SPACE BOOKING REQUESTS</h4>
                </div>
                <table data-table-paginated class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">Enquiry No</th>
                            <th class=" text-bold">Company Name</th>
                            <th class=" text-bold">Company Email</th>
                            <th class=" text-bold">Company Phone</th>
                            <th class=" text-bold">Country Of Residence</th>
                            <th class=" text-bold">Type of Space Requested</th>
                            <th class=" text-bold">Request Date</th>
                            <th class=" text-bold">Inquiry Status</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($inquiries as $key=>$item)
                            <tr>

                                <td class=" text-uppercase">{{ $key+1 }}</td>
                                <td class=" text-uppercase">{{ $item->enquiryNo }}</td>
                                <td class=" text-uppercase">{{ $item->companyName }}</td>
                                <td class=" ">{{ $item->companyEmail }}</td>
                                <td class=" ">{{ $item->companyPhone }}</td>
                                <td class="">{{ $item->country }}</td>
                                <td class=" text-uppercase">{{ $item->spaceCategory }}</td>
                                <td>{{ date('d-m-Y h:i:s', strtotime($item->registeredAt)) }}</td>
                                <td class=" text-center">
                                    @if ($item->inquiryStatus == 0)
                                        <label class=" label label-warning">PENDING</label>
                                    @elseif ($item->inquiryStatus == 1)
                                        <label class=" label label-success p-10">APPROVED AND SPACE PROVIDED</label>
                                    @elseif ($item->inquiryStatus == 2)
                                        <label class=" label label-info p-10">APPROVED, WAITING FOR SPACE
                                            AVAILABILITY</label>
                                    @elseif ($item->inquiryStatus == 3)
                                        <label class=" label label-danger p-10">REJECTED, SPACE NOT AVAILABLE</label>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class=" btn btn-primary"
                                        href="{{ route('space_booking_inquiries.show', ['space_booking_inquiry' => $item->enquiryId]) }}">
                                        Attend Request</a>
                                </td>


                                {{-- edit role modal --}}
                                <div id="AttendRequest{{ $item->enquiryId }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">ATTEND <b>{{ $item->companyName }}'S</b> SPACE
                                                    REQUEST</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('space_booking_inquiries.update', ['space_booking_inquiry' => $item->enquiryId]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class=" form-group">
                                                        <label>Equiry FeedBack<i class=" text-danger">*</i></label>
                                                        <textarea rows="5" name="equiryFeedBack" class=" form-control" placeholder="Write feedback Here" required>{{ $item->equiryFeedBack }}</textarea>
                                                    </div>
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

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link"
                                                            data-dismiss="modal">Close</button>
                                                        <button type="submit"
                                                            class="btn btn-primary btn-ladda btn-ladda-spinner">Attend
                                                            Customer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /edit role modal -->

                                {{-- delete modal --}}
                                <div enquiryId="delete_permission{{ $item->enquiryId }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete this permission
                                                    ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('permissions.destroy', ['permission' => $item->enquiryId]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-danger ml-20">Yes,
                                                            Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- DELETE --}}

                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class=" form-group">
                {{ $inquiries->withQueryString()->links() }}
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form data-action="{{ route('permissions.store') }}" method="POST" enquiryId="add_pamission"
        enctype="multipart/form-data">
        @csrf
        <div enquiryId="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user permissions.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>permission name<i class=" text-danger">*</i></label>
                                <input type="text" enquiryId="permission_name" name="permission_name"
                                    class=" form-control" placeholder=" permission Name"
                                    value="{{ old('permission_name') }}">
                                <span class=" text-danger text-error permission_name_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Add-permission</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>

