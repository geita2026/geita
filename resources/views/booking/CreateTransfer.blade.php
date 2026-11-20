<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking Transfer</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-home position-left"></i>Home</a></li>
                <li class="active">View All Booked space</li>
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
            <div class="panel-heading">
                <h6 class="panel-title">Create Transfer Exhibitor transfere Between Booths</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body">
                <div class=" row form-group">
                    <div class=" col-md-6">
                        <form action="{{ route('booking_transfer.create') }}" method="POST">
                            @csrf
                            @method('GET')
                            <label for="search-select">Switch Company</label>
                            <select id="search-select" name="companyName" onchange="this.form.submit()">
                            </select>
                        </form>
                    </div>
                    <div class=" col-md-6">
                        <form action="{{ route('booking_transfer.create') }}" method="POST">
                            @csrf
                            @method('GET')
                            <label for="search-select">Switch Company</label><br>
                            <select name="pavilionId" class=" bootstrap-select" onchange="this.form.submit()">
                                <option value=""> Select Pavilion</option>
                                @foreach ($pavilions as $value)
                                    <option value="{{ $value->pavilionID }}">
                                        {{ $value->pavilionName }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
                <div class="form-group">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Allocated Space</th>
                                <th>Payment Balance</th>
                                <th>Event Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking as $company)
                                <tr>
                                    <td>{{ $company->companyName }}</td>
                                    <td>{{ $company->spaceName }}</td>
                                    <td> <a
                                            href="{{ route('adviceNotes.show', ['adviceNote' => $company->advice_note_id]) }}">{{ $company->advice_note_id }}</a>
                                        || {{ number_format($company->total_amount - $company->amount_paid, 2) }}
                                        {{ $company->currency }}</td>
                                    <td>{{ $company->event_name }}</td>

                                    <td>
                                        @if ($company->transferStatus == 'APPROVED')
                                            <span class="label label-success">Transfer Approved</span>
                                        @elseif ($company->transferStatus == 'UNDER REVIEW')
                                            <span class="label label-warning">Transfer Under Review</span>
                                        @else
                                            <a data-toggle="modal" data-target="#transfer{{ $company->bookingID }}"
                                                class="btn btn-primary">Transfer Exhibitor <i
                                                    class=" icon-arrow-right13"></i></a>
                                        @endif
                                    </td>

                                    <div id="transfer{{ $company->bookingID }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">INITIATE EXHIBITION SPACE TRANSFER</h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form action="{{ route('booking_transfer.store') }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class=" row">
                                                            <input type="hidden" name="bookingId"
                                                                value="{{ $company->bookingID }}">
                                                            <div class=" form-group col-md-12">
                                                                <label>Please select the booth where you will be moving
                                                                    your
                                                                    customer<i class=" text-danger">*</i></label>
                                                                <select name="newBoothId" class=" form-control select"
                                                                    required>
                                                                    <option value="">Select Booth / Partition
                                                                    </option>
                                                                    @foreach ($booths as $value)
                                                                        <option value="{{ $value->boothID }}">
                                                                            {{ $value->boothNo }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class=" form-group col-md-12">
                                                                <textarea name="transferReasons" class=" form-control" placeholder="Please state the reason for the transfer" required></textarea>
                                                            </div>

                                                            <div class="form-group col-md-12 text-right">
                                                                <button type="button" class="btn btn-link"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Initiate
                                                                    Transfer <i
                                                                        class=" icon-arrow-right13"></i></button>

                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_companies') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.companyID,
                                text: item.registrationID + ' || ' + item.companyName +
                                    ' || ' + item.companyPhone + ' || ' + item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies by company name or Tantrade Service Registration Number',
            minimumInputLength: 0,
        });
    });
</script>
