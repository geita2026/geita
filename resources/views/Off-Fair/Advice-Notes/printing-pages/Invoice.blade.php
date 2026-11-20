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
    </style>

    <div style=" font-family:Arial, Helvetica, sans-serif">
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
                    <th colspan="1">
                        <b style=" text-align:left;font-size:15px;">CONTROL NO: <b style="color:blue;">
                                @if ($item->control_number == null)
                                @if ($item->sp_invoice_no!=Null)
                                <a href="{{ route('Off-Fair-Advice-Notes.show',['Off_Fair_Advice_Note'=>$item->off_fair_advice_note_id]) }}">Refresh<i class=" icon-reload-alt"></i></a> 
                                @else
                                <a data-toggle="modal" data-target="#Delete{{ $item->off_fair_advice_note_id }}" title="Cancel Advice Note" class=" text-danger">Something went wrong, Please click here to Cancel and Try again <i class=" icon-bin"></i></a> 
                                @endif
                                @else{{ trim(strrev(chunk_split(strrev($item->control_number), 1, ' '))) }}
                                    
                            </b>
                            @endif
                        </b>
                    </th>
                    <th colspan="1">
                        <b style=" text-align:center; float:right; font-size:15px"> INVOICE # {{ $item->off_fair_advice_note_id }}</b>
                    </th>
                </tr>

            </thead>
            <tbody>
                <tr>
                    <td style="padding:0.1%;font-size:12px">
                        <p><b style="font-size:14px;">Invoiced To:</b></p>
                        <b>Customer Name:</b> {{ $item->companyName ?? $item->customer_name }}<br>
                        <b>Address:</b>{{ $item->companyAddress }}, {{ $item->companyLocation }}<br>
                        <b> Tel:</b> {{ $item->companyPhone ?? $item->customer_phone }}<br>
                        <b>TIN:</b> {{ $item->tinNo ?? '-' }}<br>
                    </td>
                    <td style="text-align: right;font-size:12px;padding:0.1%">
                        <p style="font-size:14px;"><b>Payment Details:</b></p>
                        <b>Total Amount: </b> {{ number_format($item->total_amount, 2) }} {{ $item->currency }}<br>
                        <b>Date Of Issue:</b> {{ date('M d, Y', strtotime($item->advice_note_created_at)) }}<br>
                        <b>Deadline Date: </b> {{ date('M d, Y', strtotime($item->off_fair_advice_note_deadline)) }}<br>
                        @if ($item->amount_paid > 0)
                            <b>Balance: <b
                                    style="color: red;">{{ number_format($item->total_amount - $item->amount_paid, 2) }}
                                    {{ $item->currency }}</b></b><br>
                        @endif
                        <b>Amount Paid: <b style="color: green;">{{ number_format($item->amount_paid,2) }} {{ $item->currency }}</b></b><br>
                    <b>Balance: <b style="color: orange;">{{ number_format($item->balance,2) }} {{ $item->currency }}</b></b>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        <p><b>Descriptions:</b> Being charged for for off fair service.
            {{-- Booked Space in {{ $item->booked_venue }} --}}
        </p>
        <table border="1" cellpadding="2" style="font-size: 12px;">
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
                @foreach ($particulars as $particular)
                    <tr>
                        <td>{{ $particular->particular_name }}</td>
                        <td style="text-align: center">{{ number_format($particular->particular_quantity, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($particular->particular_amount, 2) }}
                            {{ $particular->type_of_currency }}</td>
                        <td style="text-align: right;">
                            {{ number_format($subtotal = $particular->particular_amount * $particular->particular_quantity * $particular->particular_duration) }}
                            {{ $particular->type_of_currency }}</td>

                    </tr>
                    @php $total=$total+$subtotal; @endphp
                @endforeach
                <tr>
                    <td colspan="3" style=" text-align:right">Sub Total </td>
                    <td style=" text-align:right">{{ number_format($total, 2) }} {{ $particular->type_of_currency }}
                    </td>
                </tr>

                @if ($item->vat > 0)
                    <tr>
                        <td colspan="3" style="text-align: right">VAT (18%)</td>
                        <td style="text-align: right">{{ number_format($vat = $total * 0.18, 2) }}
                            {{ $particular->type_of_currency }}</td>
                    </tr>
                @else
                    @php $vat=0; @endphp
                @endif

                {{-- TOTAL COSTS --}}
                @php
                    $total = $total + $vat;
                    $grand_total = $total - $item->discount;
                @endphp
                {{-- TOTAL COSTS --}}
                <tr>
                    <td colspan="3" style="text-align:right"> TOTAL COST: </td>
                    <td style="text-align:right"><b>{{ number_format($grand_total, 2) }}</b>
                        {{ $particular->type_of_currency }}</td>
                </tr>
            </tbody>
        </table>
        
        {{-- ****** BANK TRANSFER DETAILS ******* --}}
        @include('advice_note.InvoiceLayouts.BankDetails')
        {{-- ******** /End Bank TRANSFER DETAILS ****** --}}

        <br><br>
        <div style=" text-align:center">
            <img src="data:images/png;base64, {{ base64_encode(QrCode::size(50)->generate(env('APP_URL').'/verify/'.$item->off_fair_advice_note_id)) }} ">
            {{-- {{ QrCode::size(50)->generate(env('APP_URL').'/verify/'.$item->off_fair_advice_note_id) }}  --}}
            <p style="font-size:40%; font-family:monospace">{{ $item->off_fair_advice_note_id }}</p>
        </div>

    </div>

</div>
