<x-guest-layout>
    <title>Online Ticket</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description"
            content="Online Ticket, Sabasaba Ticket, Sabasaba Online Ticket, DITF Online Ticket, Tanzania Trade Fair, Sabasaba Fair, DITF Fair, Tanzania Trade Development Authority, TanTrade">
        <meta name="keywords"
            content="Online Ticket, Sabasaba Ticket, Sabasaba Online Ticket, DITF Online Ticket, Tanzania Trade Fair, Sabasaba Fair, DITF Fair, Tanzania Trade Development Authority, TanTrade">
        <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">
        <link rel="icon" href="{{ asset('images/nembo.png') }}" type="image/gif" sizes="16x16">
        <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
        <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
        <!-- Core JS files -->
        <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
        <!-- /core JS files -->
        <style>
            @media (max-width: 1000px) {
                #ticketTable thead {
                    display: none;
                    /* Hide table headers on small screens */
                }

                #ticketTable tbody tr {
                    display: block;
                    /* Make rows block-level for stacking */
                    margin-bottom: 15px;
                }

                #ticketTable tbody tr td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 3px;
                    border: 2px solid #0392ff;
                }

                #ticketTable tbody tr td::before {
                    content: attr(data-label);
                    /* Use `data-label` to display column name */
                    font-weight: bold;
                    margin-right: 2px;
                }
            }
        </style>
        <div class="">
            <div class=" ">
                <div class=" col-md-12 form-group">
                    <h3>Electronic Ticket Access Control System (e-TAC)</h3>
                    <p>
                        <b>"Secure your Sabasaba ticket effortlessly now! Experience the 50th Dar es Salaam
                            International Trade Fair with ease."
                        </b>
                    </p>

                    @if (session('page') == null)
                        <h5><b class=" text-warning">Step One:</b> Phone Number Verification | <space
                                class=" text-muted ">This is done only once for specific phone number</space>
                        </h5>
                    @elseif (session('page') == 'verifyPhone')
                        <h5><b class=" text-warning">Step two: </b> Enter Verification code | <space
                                class=" text-muted ">This is done once only</space>
                        </h5>
                    @elseif (session('page') == 'buyTicket')
                        <h5><b class=" text-warning">Step Three:</b> Buy Ticket | <space class=" text-muted ">Unlimited,
                                do it to your limit</space>
                        </h5>
                    @endif
                </div>

                @if (session('success'))
                    <div class=" alert alert-success col-md-12">
                        {{ session('success') }}
                    </div>
                @elseif (session('fail'))
                    <div class=" alert alert-danger col-md-12">
                        {{ session('fail') }}
                    </div>
                @endif

                <div class=" col-md-12 form-group">
                    @if (session('page') == null)
                        <form action="{{ route('send_verification_code') }}" method="POST">
                            @csrf
                            <div class="col-md-6 form-group">
                                <label>Customer Phone Number<i class=" text-danger">*</i> <span
                                        class="text-muted">Ensure
                                        you have access
                                        to it; it will be used to receive your ticket.</span></label>
                                <input type="text" id="phoneNumber" name="phoneNumber" class=" form-control"
                                    value="{{ old('phoneNumber') ?? session('phoneNumber') }}"
                                    placeholder="Phone Number, start with 255xxxxxxxxx" value="255" maxlength="12"
                                    onkeydown="preventDeletion(event)" oninput="validateInput()" required>
                                <span class=" text-danger">
                                    @error('phoneNumber')
                                        {{ $message }}
                                    @enderror
                                </span>
                                <p><b class=" text-danger">Note!</b> Only the 255 phone code will be saved.</p>
                            </div>
                            <input type="hidden" name="verifyPhone" value="1">
                            <div class=" form-group col-md-2">
                                <label>.</label>
                                <br>
                                <button class=" btn btn-primary btn-ladda btn-ladda-spinner">send verification Code<i
                                        class=" icon-arrow-right13"></i></button>
                            </div>
                        </form>
                    @elseif (session('page') == 'verifyPhone')
                        <form action="{{ route('change_phone_number') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-primary" id="changeNumber">Change phone number or resend Code <i
                                    class=" icon icon-arrow-right13"></i></button>
                        </form>
                        <form action="{{ route('verify_verification_code') }}" method="POST">
                            @csrf
                            <div class="col-md-6 form-group">
                                <label>Verification Code<i class=" text-danger">*</i> <span class="text-muted">Enter the
                                        code
                                        sent to your phone number ({{ session('phoneNumber') }}).</span></label>
                                <input type="text" name="verificationCode" class=" form-control"
                                    placeholder="Enter the code sent to your phone number" required>
                                <input type="hidden" name="phoneNumber" value="{{ session('phoneNumber') }}">
                            </div>
                            <div class=" form-group col-md-2">
                                <label>.</label><br>
                                <button class=" btn btn-primary btn-ladda btn-ladda-spinner">Verify Code<i
                                        class=" icon-arrow-right13"></i></button>
                            </div>
                        </form>
                    @elseif (session('page') == 'buyTicket')
                        <div class="  col-md-12">
                            <form action="{{ route('change_phone_number') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-primary" id="changeNumber">Change number [
                                    {{ session('phoneNumber') }} ]</button>
                            </form>
                            <br>

                            <form id="ticketForm" method="POST" action="{{ route('online_tickets.store') }}"
                                method="POST">
                                <input type="hidden" name="phoneNumber" value="{{ session('phoneNumber') }}">
                                @csrf
                                <div class=" col-md-12 table-responsive">
                                    <table id="ticketTable" class=" table table-bordered" style="width:100%"
                                        border="1">
                                        <tbody>
                                            <tr>
                                                <td class=" text-left" style="font-size: 1vmax"> Ticket Type:<br>
                                                    <select name="tickets[0][type]" class="ticket-type form-control" required>
                                                        <option value="">Select Type</option>
                                                        @foreach ($tickets as $type)
                                                            <option value="{{ $type->vip_package_id }}"
                                                                data-cost="{{ $type->package_cost }}">
                                                                {{ $type->package_discriptions }} [
                                                                {{ number_format($type->package_cost) }} TZS ]
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <input type="hidden" name="tickets[0][quantity]"
                                                    class="quantity form-control" value="1" >

                                                <td style="font-size: 1vmax">Name or Plate number:<br>
                                                    <input type="text" name="tickets[0][ticketFor]"
                                                        value="{{ old('plateNumber') }}" class=" form-control"
                                                        placeholder="Full Name / Plate number for car parking ticket"
                                                        required>
                                                </td>
                                                <td class="total-cost"></td>

                                                <td><button type="button" class="remove-row btn btn-danger"><b
                                                            class=" icon-bin"></b></button></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                    <div class=" text-right">
                                        <p style="color:blue;font-size:1vmax;">Overall Cost: <b id="overallCost"></b>
                                            TZS</p>
                                    </div>
                                </div>
                                <div class=" col-md-12 text-right">
                                    <button type="button" id="addRow" class=" btn btn-success">Add Ticket <i
                                            class=" icon-plus22"></i></button>
                                    <i class=" icon-arrow-left12"></i> <i class="icon-arrow-right13"></i>
                                    <button type="submit"
                                        class=" btn btn-primary btn-ladda btn-ladda-spinner">Request Control Number<i
                                            class=" icon-arrow-right13"></i></button>
                                    @php $isYasNumber = preg_match('/^255(?:(?:65|67|71|77)\d{7}|705\d{6})$/', (string) session('phoneNumber')); @endphp
                                    @if ($isYasNumber)
                                        <button type="button" id="payWithYas" class=" btn btn-warning">Pay with Yas (Mixx)
                                            <i class=" icon-mobile"></i></button>
                                    @endif
                                </div>

                                @if ($isYasNumber ?? false)
                                    <div id="yasStatusBox" class=" col-md-12" style="display:none; margin-top:15px;">
                                        <div class="alert" id="yasStatusAlert" role="alert"></div>
                                    </div>
                                @endif



                            </form>
                        </div>
                    @endif
                </div>


                <div class=" col-md-12">

                    <p style="font-size:1vmax">
                        1. <b>Ticket Purchase</b>: Attendees buy tickets online, receiving a digital ticket via
                        email,normal messages or an
                        app.<br>
                        2. <b>Entry Points</b>: At the venue, designated entry points are equipped with scanners or
                        readers
                        that can scan the electronic tickets.<br>
                        3. <b>Verification</b>: When a ticket is scanned, the system verifies its validity, ensuring it
                        hasn't been used or is not counterfeit.<br>
                        4. <b>Access Granted</b>: Once verified, the system allows entry, often recording the event for
                        security and attendance tracking.<br>
                        5. <b>Real-Time Data</b>: The system provides real-time data on attendance, ticket usage, and
                        other
                        metrics, helping organizers manage the event more effectively.

                        This system helps improve security, reduce fraud, streamline the entry process, and provide
                        real-time data on attendance.
                    </p>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                // Add new row
                $('#addRow').click(function() {
                    const rowCount = $('#ticketTable tbody tr').length;
                    const newRow = `
            <tr >
                <td>Ticket: 
                    <select name="tickets[${rowCount}][type]" class="ticket-type form-control" required>
                        <option value="">Select Type</option>
                        @foreach ($tickets as $type)
                            <option value="{{ $type->vip_package_id }}" data-cost="{{ $type->package_cost }}">
                                {{ $type->package_discriptions }} [ {{ number_format($type->package_cost) }} TZS ]
                            </option>
                        @endforeach
                    </select>
                </td>
                 <input type="hidden" name="tickets[${rowCount}][quantity]" class="quantity form-control"  value="1" readonly>
                <td> Name / Plate number: <br><input type="text" name="tickets[${rowCount}][ticketFor]" placeholder="Full Name / Plate number for car parking ticket" class="form-control" required></td>
                <td class="total-cost"></td>
                <td><button type="button" class="remove-row btn btn-danger"> <b class='icon-bin'></b></button></td>
            </tr>
        `;
                    $('#ticketTable tbody').append(newRow);
                });

                // Remove row
                $(document).on('click', '.remove-row', function() {
                    $(this).closest('tr').remove();
                });

                // Calculate cost dynamically
                $(document).on('change', '.ticket-type, .quantity', function() {
                    const row = $(this).closest('tr');
                    const cost = parseFloat(row.find('.ticket-type option:selected').data('cost')) || 0;
                    const quantity = parseInt(row.find('.quantity').val()) || 0;
                    row.find('.cost').val(cost.toFixed(2));
                    row.find('.total-cost').val((cost * quantity).toFixed(2));
                });
                $(document).on('change', '.ticket-type, .quantity', function() {
                    let overallCost = 0;
                    // Iterate through each row to sum up total costs
                    $('#ticketTable tbody tr').each(function() {
                        const totalCost = parseFloat($(this).find('.total-cost').val()) || 0;
                        overallCost += totalCost;
                    });
                    // Update the overall cost display
                    $('#overallCost').text(numberWithCommas(overallCost.toFixed(2)));
                });
            });

            $(document).ready(function() {
                $('#plateNumber').hide();
                $('#forCars').on('change', function() {
                    const selectedValue = $(this).val();
                    if (selectedValue == '0697cbcd-330d-4ab6-adaf-fcc616343925') {
                        $('#plateNumber').show();
                    } else {
                        $('#plateNumber').hide();
                    }

                });
            });

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        </script>

</x-guest-layout>

<script>
    function preventDeletion(event) {
        const input = document.getElementById('phoneNumber');
        // Prevent backspace or delete if the cursor is at the start (before '255')
        if ((event.key === 'Backspace' || event.key === 'Delete') && input.selectionStart <= 3) {
            event.preventDefault();
        }
    }

    function validateInput() {
        const input = document.getElementById('phoneNumber');
        // Ensure the prefix '255' is always present
        if (!input.value.startsWith('255')) {
            input.value = '255' + input.value.replace(/^255/, '').replace(/[^0-9]/g, '');
        }
        // Enforce that the digit after '255' is greater than zero
        if (input.value.length > 3) {
            const digit = input.value.charAt(3);
            if (digit === '0') {
                input.value = '255' + input.value.slice(4);
            }
        }
    }
</script>

<!-- YAS (Mixx) pay-by-push: fire the prompt, poll, show the ticket on success. -->
<script>
    (function () {
        var payBtn = document.getElementById('payWithYas');
        if (!payBtn) return; // only on Yas numbers
        var form = document.getElementById('ticketForm');
        var box = document.getElementById('yasStatusBox');
        var alertEl = document.getElementById('yasStatusAlert');
        var tokenInput = form.querySelector('input[name=_token]');

        function show(cls, msg) {
            box.style.display = 'block';
            alertEl.className = 'alert ' + cls;
            alertEl.innerHTML = msg;
        }

        payBtn.addEventListener('click', function () {
            if (typeof form.reportValidity === 'function' && !form.checkValidity()) {
                form.reportValidity();
                return;
            }
            payBtn.disabled = true;
            show('alert-info', 'Sending Yas prompt to your phone…');

            fetch("{{ route('buy_ticket_yas') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status === 1 && res.referenceId) {
                    var amt = res.amount ? (Number(res.amount).toLocaleString() + ' TZS') : '';
                    show('alert-warning', '&#128241; Approve the Yas prompt on your phone ' + amt + '. Waiting for confirmation…');
                    poll(res.referenceId, 0);
                } else {
                    payBtn.disabled = false;
                    show('alert-danger', res.message || 'Failed to send the Yas prompt.');
                }
            })
            .catch(function () {
                payBtn.disabled = false;
                show('alert-danger', 'Network error. Please try again.');
            });
        });

        function poll(ref, tries) {
            if (tries >= 30) { // ~90 seconds
                payBtn.disabled = false;
                show('alert-danger', 'Payment timed out. If you approved on your phone, your ticket will still arrive by SMS. Otherwise please try again.');
                return;
            }
            setTimeout(function () {
                var d = new FormData();
                d.append('_token', tokenInput.value);
                d.append('referenceId', ref);
                fetch("{{ route('yas_online_status') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: d
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    var st = res.paymentStatus;
                    if (st === 'SUCCESS') {
                        var link = res.download
                            ? '<br><a href="' + res.download + '" class="btn btn-success btn-sm" target="_blank">Download your ticket</a>'
                            : '';
                        show('alert-success', '&#9989; Payment received! Your ticket has been issued and sent by SMS.' + link);
                    } else if (st === 'FAILED') {
                        payBtn.disabled = false;
                        show('alert-danger', '&#10060; ' + (res.description || 'Payment was declined or cancelled.') + ' Please try again.');
                    } else {
                        poll(ref, tries + 1);
                    }
                })
                .catch(function () { poll(ref, tries + 1); });
            }, 3000);
        }
    })();
</script>
