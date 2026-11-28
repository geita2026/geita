@foreach ($payments as $item)
    <div >
        <style>
            table {
                width: 100%;
            }

            .thead {
                background-color: rgb(231, 229, 240);
            }

            .invoice_status {
                font-size: 120%;
                text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
            }

            body {
                font-family: 'Arial';
            }

            .invoiceMessage {
                position: absolute;
                top: 10%;
                /* left: 50%; */
                right: 10% transform: translate(-50%, -50%);
                font-size: 3vmin;
            }
        </style>

        <div style=" padding:1%; font-family:Arial, Helvetica, sans-serif">
            <table style="width: 100%;">
                @include('reports.reports_layouts.report_header')
            </table>
            <br>
            <div style="width: 100%; border-bottom:10px solid blue;"></div>
            <div style="width: 100%; border-bottom:10px solid green;"></div>


            <br>
            <table style="width: 100%;">
                <thead>

                    <tr>
                        <th colspan="4">

                            <h1 style="text-align: center">PAYMENT RECEIPT</h1>
                        </th>
                    </tr>

                </thead>
                <tbody style="font-size:15px">
                    <tr>
                        <td colspan="2">
                            <p style="font-size: 18px;"><b>CUSTOMER DETAILS</b></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>TSRN: </b>
                        </td>
                        <td>
                            {{ $item->registrationID }}
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Customer Name:</b></td>
                        <td>{{ $item->companyName }}</td>
                    </tr>
                    <tr>
                        <td><b>Email Address:</b></td>
                        <td> {{ $item->companyEmail ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><b> Phone Number:</b> </td>
                        <td>{{ $item->companyPhone ?? '-' }}</td>
                    </tr>
                    {{-- <tr>
                        <td><b>TIN NO:</b></td>
                        <td>{{ $item->tinNo ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>VRN: <b></td>
                        <td>{{ $item->registrationNo ?? '-' }}</td>
                    </tr> --}}
                    


                    <tr>
                        <p style="font-size: 17px;"><b>RECEIPT DETAILS</b></p>
                    </tr>
                    <tr>
                    
                    <tr>
                        <td><b>Receipt No: </b></td>
                        <td>{{ $item->receipt }}</td>
                    </tr>
                    <tr>
                        <td><b>Bank Reference:</b> </td>
                        <td>{{ $item->bank_receipt }}</td>
                    </tr>
                    <tr>
                        <td><b>Amount Received:</b> </td>
                        <td style="color:green;">{{ number_format($item->amount_paid, 2) }} {{ $item->currency }}</td>
                    </tr>
                    <tr >
                        <td><b>Invoice Balance:</b></td>
                        <td style="color:red;">
                            {{ number_format($item->total_amount - $item->amount_paid, 2) }}
                            {{ $item->currency }}
                        </td>
                    </tr>
                    <tr>
                        <td><b>Payment Method:</b></td>
                        <td>Government Payment Gateway (Control Number).</td>
                    </tr>
                    <tr>
                        <td><b>Control Number:</b></td>
                        <td>{{ $item->control_number }}</td>
                    </tr>
                    <tr>
                        <td><b>Payment Date:</b></td>
                        <td>{{ date('l M d Y, h:i:s', strtotime($item->payment_made_on)) }}</td>
                    </tr>

                    <tr>
                        <td><b>Description: </b></td>
                        <td>Being Exhibition Fee for the {{ $item->event_name }}</td>
                    </tr>
                    
                    </tr>
                </tbody>
            </table>

           

            <br><br>

            <table>

                <tr>
                    <td style=" text-align:center;" colspan="4">
                        <img
                            src="data:images/png;base64, {{ base64_encode(QrCode::size(90)->generate(env('APP_URL').'/verify/'.substr($item->payment_id,0,8))) }} ">
                        <br>
                        <b
                            style="font-size:70%; font-famil:cambria; letter-spacing:7px;">{{ $item->in_fair_invoice_id }}</b>
                    </td>
                </tr>
            </table>



        </div>
<div style="bottom:0; position:absolute;font-size:10px; width:100%">
    <table style="border-top:1px solid black;">
        <tr>
            <td style="text-transform:uppercase">Printed By {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} on {{ date('l M d, Y') }}</td>
            <td>gememis.go.tz</td>
        </tr>
    </table>
</div>
    </div>
@endforeach
