@foreach ($invoices as $item)
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
                    <th colspan="2">
                        <b>CONTROL NO: <b style="color:blue; letter-spacing:3px;">
                                @if ($item->controlNumber == null)
                                    <i class=" text-brown-300"> ----</i> @else{{ $item->controlNumber }}
                            </b>
                            @endif
                        </b>
                    </th>
                    <th colspan="2">
                        <b style=" text-align:center; float:right;"> INVOICE # <b
                                style="font-size:90%">{{ $item->invoiceNo }}</b></b>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <br>
                        <p><b>Invoiced To:</b></p>
                        <b>TSRN: </b>{{ $item->registrationID }}<br>
                        <b>Customer Name:</b> {{ $item->companyName }}<br>
                        <b>Address:</b> {{ $item->companyAddress ?? '' }} {{ $item->companyLocation }}<br>
                        <b>Email:</b> {{ $item->companyEmail ?? '-' }}<br>
                        <b> Tel:</b> {{ $item->companyPhone ?? '-' }}<br>
                        <b style="margin-left:auto;margin-right:0">TIN NO: <b
                                style="font-family:Chilanka">{{ $item->tinNo ?? '-' }}</b></b><br>
                        <b style="margin-left:auto;margin-right:0">VRN: <b
                                style="font-family:Chilanka">{{ $item->registrationNo ?? '-' }}</b></b>
                    </td>
                    <td style="text-align: right;" colspan="2">
                        <p style="color:rgb(28, 28, 29);"><b>Invoice Details:</b></p>
                        <b>Total Amount: </b> {{ number_format($item->invoiceAmount, 2) }}
                        {{ $item->currency }}<br>
                        <b>Date Of Issue:</b> {{ date('M d, Y', strtotime($item->invoiceDate)) }}<br>
                        <b>Deadline Date: </b> {{ date('M d, Y', strtotime($item->expireDate)) }}<br>
                        <b>Amount Paid: <b style="color: green;">{{ number_format($item->amountPaid, 2) }}
                                {{ $item->currency }}</b></b><br>
                        <b>Balance: <b
                                style="color: red;">{{ number_format($item->invoiceAmount - $item->amountPaid, 2) }}
                                {{ $item->currency }}</b></b><br>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        <p><b>Descriptions:</b> {{ $item->invoiceDescription }}.
            {{-- Booked Space in {{ $item->booked_venue }} --}}
        </p>
        <table border="1" cellpadding="2">
            <thead>
                <tr class=" thead">
                    <th style="text-align: center">Particular Name</th>
                    <th style="text-align: center">Qty</th>
                    <th style="text-align: center">Unit Price</th>
                    <th style="text-align: center">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @php $total=0; @endphp
                    <tr>
                        <td>{{ $item->eventTitle }} - <i
                                style="font-size: 10px;">({{  $item->promotionEventTypeName }})</i> Permit.</td>
                        <td style="text-align: center">1</td>
                        <td style="text-align: right;">{{ number_format($item->invoiceAmount, 2) }}
                            {{ $item->currency }}</td>
                        <td style="text-align: right;">
                            {{ number_format($item->invoiceAmount) }}
                            {{ $item->currency }}</td>

                    </tr>

                
                <tr>
                    <td colspan="3" style=" text-align:right">Sub Total </td>
                    <td style=" text-align:right">{{ number_format($item->invoiceAmount, 2) }} {{ $item->currency }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right"> TOTAL COST: </td>
                    <td style="text-align:right"><b>{{ number_format($item->invoiceAmount, 2) }}</b>
                        {{ $item->currency }}</td>
                </tr>

                {{-- descriptions  --}}
                @if ($item->invoiceDescription)
                    <tr>
                        <td colspan="4" style="font-size: 15px; padding:1%;">{{ $item->invoiceDescription }}
                        </td>
                    </tr>
                @endif
                {{-- end description  --}}

            </tbody>
        </table>
        {{-- ############# --}}
        @if ($item->controlNumber != null)
            {{-- ****** BANK TRANSFER DETAILS ******* --}}
            @include('advice_note.InvoiceLayouts.BankDetails')
            {{-- ******** /End Bank TRANSFER DETAILS ****** --}}
        @endif
    
        <br><br>
        <table>
            <tr> 
                <td style=" text-align:center;" colspan="4">
                    <img src="data:images/png;base64, {{ base64_encode(QrCode::size(90)->generate($item->invoiceNo)) }} ">
                    {{ QrCode::size(70)->generate($item->invoiceNo) }}
                    <br>
                    <b style="font-size:70%; font-famil:cambria; letter-spacing:7px;">{{ $item->advice_note_id }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>
@endforeach