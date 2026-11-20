

            {{--  message  --}}
            @foreach ($adviceNotes as $item)
    <div style=" border:2px solid rgb(212, 208, 208); font-family:cambria; height:99.5%;width:100%" >
        <style>
            #customers {
              font-family:arial;
              border-collapse: collapse;
              width: 100%;
              font-size: 80%;
            }

            #customers td, #customers th {
              border: 1px solid rgb(37, 37, 37);
              padding: 0.5%;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}


            #customers th {
              padding-top: 0.5%;
              padding-bottom: 1%;
              text-align: left;
              background-color: #ffffff;
              color: rgb(0, 0, 0);
            }
              #boxes {
                content: "";
                display: table;
                clear: both;
              }
            </style>
        <div style="display: flex;border-bottom:3px solid rgb(60, 87, 211);padding:0.5%;">

            <div style="margin-right: auto;margin-left: 0;">
                <img src="{{ url(asset('images/nembo.png')) }}" height="50" width="80">
            </div>


            <div style="text-align: center; font-size:90%">
                <p><b>TANZANIA TRADE DEVELOPMENT AUTHORITY (Tan Trade)<br></b>
                    P.O. BOX 5402, Dar Es Salaam, Tanzania<br>
                    Tel: +255 22 2850238, Fax: +255 22 2850539
                </p>
            </div>


            <div style="margin-left:auto;margin-right: 0;">
                <img src="{{ url(asset('images/logo.jpg')) }}" height="50" width="90">
            </div>
        </div>

        <h4 style=" text-align:center;"><b>CONTROL NO: <i style="font-family: monospace; color:rgb(20, 17, 226);">{{ $item->control_number }}</i></b>  | <b style="">INVOICE # {{ $item->advice_note_id }}</b></h4>

        <div style=" padding:1%; font-size:80%">
            <div style="display:flex;">
                <div style="margin-left:0;margin-right:auto">
                    <p style="color:rgb(60, 60, 61);">Invoiced To:</p>
                    <b > Name: {{ $item->companyName }} ,</b><br>
                    <b >Address:  {{ $item->companyAddress ?? '-'}} , {{ $item->companyLocation }}, </b><br>
                    <b>Email: {{ $item->companyEmail ?? '-' }}</b><br>
                    <b> Tel: {{ $item->companyPhone ?? '-' }}</b><br>

                <b style="margin-left:auto;margin-right:0">TIN NO: <b style="font-family:Chilanka">{{ $item->tinNo ?? '-' }}</b></b><br>
                <b style="margin-left:auto;margin-right:0">BRN: <b style="font-family:Chilanka">{{ $item->registrationNo ?? '-' }}</b></b>
            </div>

            {{-- right conner --}}
            <div style="margin-left:auto;margin-right:0; text-align:left">
                <p style="color:rgb(60, 60, 61);">Payment Details:</p>
                <b >Total Amount: <b style="">{{ number_format($item->total_amount,2) }} {{ $item->currency }}</b></b><br>
                {{-- <b >Control No: <b style="">{{ $item->control_number }}</b></b><br> --}}
                <b>Date Of Issue: <b style="">{{ date("d-m-Y h:i:s", strtotime($item->advice_note_created_at))}}</b></b><br>
                <b>Deadline Date: <b> {{date("d-m-Y", strtotime($item->advice_note_deadline))}}</b></b><br>
            </div>
        </div>
        <br>

            <p style=" color:rgb(60, 60, 61);">Invoice descriptions</p>
            <table id="customers" style="">
                <tr style="border: 1px solid rgb(28, 28, 31);">
                    <th style="text-align: center">Item Name</th>
                    <th style="text-align: center">Qty</th>
                    <th style="text-align: center">Unit Price</th>
                    <th style="text-align: center">SUB TOTAL</th>
                    <th style="text-align: center"> DESCRIPTIONS</th>
                </tr>
                <tbody>
                    @php $total=0; @endphp
                    @foreach ($particulars as $particular)

                    <tr style=" border:1px solid blue;">
                        <td>{{ $particular->particular_name }} - <i style="font-size: 10px;">{{ $particular->particular_category }}</i></td>
                        <td style="text-align: center">{{ number_format($particular->particular_quantity,2) }}</td>
                        <td style="text-align: right;">{{ number_format($particular->cost_for_one_particular,2) }} {{ $particular->type_of_currency }}</td>
                        <td style="text-align: right;">{{ number_format($subtotal=$particular->cost_for_one_particular*$particular->particular_quantity) }} {{ $particular->type_of_currency }}</td>
                        <td>{{ $description=$particular->particular_description }}</td>
                    </tr>
                    @php $total=$total+$subtotal; @endphp
                    @endforeach

                    <tr>
                        <td colspan="3" style=" text-align:right">Sub Total </td>
                        <td style=" text-align:right"><b>{{ number_format($total,2) }}</b> {{ $particular->type_of_currency }}</td>
                        <td></td>
                    </tr>


                    @if ($item->vat>0)
                    <tr>
                        <td colspan="3" style="text-align: right">VAT (18%)</td>
                        <td style="text-align: right"><b>{{ number_format(($vat=$total*$item->vat),2) }}</b> {{ $particular->type_of_currency }}</td>
                        <td></td>
                    </tr>
                    @else
                    @php $vat=0; @endphp
                    <tr>
                        <td colspan="3" style="text-align: right">VAT</td>
                        <td style="text-align: right;"><b>Null</b></td>
                        <td></td>
                    </tr>
                    @endif



                    <tr>
                        <td colspan="3" style="text-align:right"> TOTAL COST: </td>
                        <td style="text-align:right"><b>{{ number_format(($total+$vat),2) }}</b> {{ $particular->type_of_currency }}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="7" style="font-size: 80%;">Being charged for  {{ $item->event_name }}  </td>
                    </tr>

                    {{-- descriptions  --}}
                    @if ($item->advice_note_description)
                    <tr>
                        <td colspan="7"><i style="font-size: 10px">{{ $item->advice_note_description }}</i></td>
                    </tr>
                    @endif
                    {{-- end description  --}}

                </tbody>
            </table>
        </div>


        <div style="padding:1%; margin-top: 2%; font-size:60%">
            <p style="color:rgb(60, 60, 61);">AUTHORIZED BY:</p>
            <div >
                @if(empty($item->user_signature))
                <b  style=" text-transform: lowercase;font-family: 'Cedarville Cursive', cursive;"><b style="text-transform: uppercase;">@php echo substr($item->first_name,-8,1) @endphp</b> . {{ $item->last_name }}</b>
                @else
                <img src="{{ asset('signatures/'.$item->user_signature) }}" height="40" width="150" style="border-bottom: 2px dotted black;">
                @endif
            </div>
            <div>
                <b style="">{{ $item->first_name }} - {{ $item->last_name }}</b>
            </div>
            <div>
                <b style="text-align: center">{{ $item->name }}</b>
            </div>
        </div>


       <div style="padding: 1%;">
        <div style="color:rgb(60, 60, 61);">Other information</div><br>
        <div style="color:#424443;font-size:60%">
            Thank you for showing interest of participating {{ $item->event_name }},
            you can make payment as early as possible through control number on top of your invoice.
            Bank transfer can be made to ACC No: 1000 7450 507 - NMB bank. After this payment will now able to access all {{ $item->event_name }}
             service.<br> <b style="text-align: center;">KARIBU SANA</b>
        </div>
        <div style=" float: right; padding:1%;">
            <b style=" text-align:right;">{{ QrCode::size(80)->generate(env('APP_URL').'/adviceNotes/'.$item->advice_note_id)}}</b>
           </div>
    </div>

        @endforeach
    </div>

