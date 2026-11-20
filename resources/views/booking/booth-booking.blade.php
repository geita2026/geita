<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>

    <style>
        .booking-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,.08);
            margin-bottom: 25px;
        }

        .legend span {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 8px;
            display: inline-block;
        }

        .legend-available { background:#c8e6c9; color:#1b5e20; }
        .legend-booked { background:#e0e0e0; color:#424242; }

        .floating-confirm-wrapper {
            position: fixed;
            right: 24px;
            bottom: 120px;
            z-index: 9999;
            text-align: right;
        }

        .floating-confirm-wrapper .btn {
            min-width: 320px;
            box-shadow: 0 12px 28px rgba(0,0,0,.25);
        }

        .pulse {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .clear-selection {
            margin-top: 8px;
            font-size: 12px;
            cursor: pointer;
            color: #d32f2f;
        }
    </style>

    <!-- ===================== PAGE HEADER ===================== -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home position-left"></i> Home</a></li>
                <li class="active">Make Booking</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <!-- ===================== CONTENT ===================== -->
    <div class="content">
        <div class="panel panel-white">
            <div class="panel-body">

                <!-- ===================== PAVILION INFO ===================== -->
                <div class="booking-card text-center">
                    <h2 class="text-uppercase text-muted">
                        SELECTED PAVILION
                    </h2>
                    <h1 class="text-bold">
                        {{ $pavilion->pavilionName }}
                    </h1>

                    <div class="legend mt-10">
                        <span class="legend-available">Available Space</span>
                        <span class="legend-booked">Booked Space</span>
                    </div>
                </div>

                <!-- ===================== ALLOWED CATEGORIES ===================== -->
                <div class="booking-card">
                    <div class="media-link cursor-pointer"
                         data-toggle="collapse"
                         data-target="#allowedCategories"
                         style="border:1px solid #d5eeeb;padding:15px">
                        <h5 class="text-warning text-bold">
                            <i class="icon-warning"></i>
                            NOTE! Allowed Sectors
                        </h5>
                        <p class="text-muted">
                            Ensure your company category is allowed before booking.
                        </p>
                    </div>

                    <div class="collapse" id="allowedCategories">
                        <hr>
                        <div class="text-uppercase">
                            {{ $pavilion->allowedForBooking }}
                        </div>
                    </div>
                </div>

                <!-- ===================== BOOKING FORM ===================== -->
                <form id="bookingForm"
                      action="{{ route('booking.store') }}"
                      method="POST">
                    @csrf

                    <!-- ===================== COMPANY ===================== -->
                    <div class="booking-card">
                        <label>
                            Company / Institution
                            <span class="text-danger">*</span>
                        </label>
                        <select id="search-select"
                                name="company_name"
                                required></select>
                    </div>

                    <!-- ===================== BOOTHS ===================== -->
                    @if ($number_of_booths > 0)
                        <div class="booking-card">
                            <h4 class="text-bold text-uppercase">
                                Select Booth(s)
                            </h4>

                            <div class="table-responsive">
                                @if (strtoupper($pavilion->pavilionName)=='KARUME HALL')
                                    @include('space.FloorPlans.KarumeHall')
                                @elseif (strtoupper($pavilion->pavilionName)=='MANYARA PAVILION' || strtoupper($pavilion->pavilionName)=='MIKUMI PAVILION')
                                    @include('space.FloorPlans.40StandPavilions')
                                @elseif (strtoupper($pavilion->pavilionName)=='KILIMANJARO HALL')
                                    @include('space.FloorPlans.KilimanjaroHall')
                                @elseif (strtoupper($pavilion->pavilionName)=='KIJIJI CHA WIZARA YA UTAMADUNI SANAA NA MICHEZO')
                                    @include('space.FloorPlans.117Booth')
                                @else
                                    @include('space.FloorPlans.FourTierFloorMap')
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- ===================== WHOLE PAVILION ===================== -->
                        <input type="hidden"
                               name="spaceName[]"
                               value="{{ $id }}">
                    @endif

                    <!-- IMPORTANT: ORIGINAL INPUT PRESERVED -->
                    <input type="hidden" name="space" value="booth">

                    <!-- ===================== FLOATING CONFIRM ===================== -->
                    <div class="floating-confirm-wrapper">
                        <button type="button"
                                id="confirmBookingBtn"
                                class="btn btn-primary btn-lg btn-ladda btn-ladda-spinner"
                                {{ $number_of_booths > 0 ? 'disabled' : '' }}>
                            Confirm Booking
                        </button>

                        @if ($number_of_booths > 0)
                            <div id="clearSelection"
                                 class="clear-selection">
                                Clear Selection
                            </div>
                        @endif
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>

<!-- ===================== SCRIPTS ===================== -->
<script src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>

<script>
$(document).ready(function () {

    /* ===================== SELECT2 ===================== */
    $('#search-select').select2({
        ajax: {
            url: '{{ route('search_companies') }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: $.map(data, function(item) {
                    return {
                        id: item.companyID,
                        text: item.registrationID + ' || ' +
                              item.companyName + ' || ' +
                              item.companyPhone + ' || ' +
                              item.companyEmail
                    };
                })
            }),
            cache: true
        },
        placeholder: 'Search company by name or TSRN',
        minimumInputLength: 0,
        width: '100%'
    });

    const hasBooths = {{ $number_of_booths > 0 ? 'true' : 'false' }};

    function selectedCount() {
        return $('.booth-select:checked').length;
    }

    function updateButton() {
        const btn = $('#confirmBookingBtn');
        const count = selectedCount();

        if (!hasBooths) {
            btn.prop('disabled', false).text('Confirm Booking');
            return;
        }

        if (count > 0) {
            btn.prop('disabled', false)
               .addClass('pulse')
               .text(`Confirm Booking`);
        } else {
            btn.prop('disabled', true)
               .removeClass('pulse')
               .text('Confirm Booking');
        }
    }

    $(document).on('change', '.booth-select', updateButton);

    $('#clearSelection').on('click', function () {
        $('.booth-select:not(:disabled)').prop('checked', false);
        updateButton();
    });

    $('#confirmBookingBtn').on('click', function () {

        if (!$('#search-select').val()) {
            swal({
                title: 'Company Required',
                text: 'Please select a company before confirming booking.',
                type: 'error'
            });
            return;
        }

        swal({
            title: 'Confirm Booking',
            text: hasBooths
                ? `You are about to confirm selected booth(s).`
                : 'You are about to confirm this booking.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a73e8',
            confirmButtonText: 'Yes, Confirm',
            cancelButtonText: 'Cancel'
        }, function (isConfirm) {
            if (isConfirm) {
                $('#confirmBookingBtn')
                    .prop('disabled', true)
                    .removeClass('pulse');
                $('#bookingForm').submit();
            }
        });
    });

});
</script>
