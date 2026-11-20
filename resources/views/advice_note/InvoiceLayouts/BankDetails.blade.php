
        <br>
        <div style=" font-size:80%">
                <div style="font-size:12px"><b>BANK TRANSFER:</b></div>
                <div style="color:red">Control Number: 
                    {{ trim(strrev(chunk_split(strrev($item->control_number), 1, ' '))) }}
                </div>

               {{--@foreach ($transfer as $key => $detail)
                    @if ($item->currency == $detail->account_currency) --}}
                        <div style=" font-size:12px; border-bottom:5px solid rgb(207, 206, 206);">
                           <!-- <p><b>Bank Name:</b> {{-- $detail->bank_name }}-({{ $detail->account_currency --}}) 
                            <b style="margin-left: 3%">Account Name:</b> {{-- $detail->account_name --}}</p>
                            <p><b>Account Number:</b>
                                {{-- trim(strrev(chunk_split(strrev($detail->account_number), 1, ' '))) }}
                            <b style="margin-left: 3%">Swift Code:</b> {{ trim(strrev(chunk_split(strrev($detail->swift_code), 1, ' '))) --}}
                            </p> --> 

                              <p><b>Bank Name:</b> Azania Bank-(TZS)
                            <b style="margin-left: 3%">Account Name:</b> TanTrade Revenue Account</p>
                            <p><b>Account Number:</b>
                               0 3 2 0 0 0 0 4 5 3 3 5
                            <b style="margin-left: 3%">Swift Code:</b> A Z A N T Z T Z
                            
                        </div>
                 {{--  @endif
                @endforeach --}}
        </div>
            <div>
                <div style=" font-size:60%">
                    <b>TIGO PESA</b><br>
                    1. Dial *150*01#
                    2. Choose Option 4 --- "Pay Bills"("Lipia Bili")
                    3. Choose Option 5 --- "Government Payment" ("Malipo ya
                    Serikali").
                    4. Enter Reference Number (Weka Kumbu kumbu namba) --
                    Control Number
                    5. Enter Amount (Ingiza kiasi)
                    6. Enter pin to confirm (Ingiza namba ya siri kuhakiki).<br>
                    <b>M-PESA PESA</b><br>
                    1. Dial *150*00#
                    2. Choose Option 4 --- “Pay by M-Pesa”(“Lipa kwa M-Pesa”)
                    3. Choose Option 5 --- "Government Payment" ("Malipo ya Serikali")
                    4. Enter Reference Number (Weka Kumbu kumbu ya Malipo) -- Control Number
                    5. Enter your pin (Weka namba yako ya siri)
                    6. Confirm payment (Thibitisha malipo)<br>
                    <b>AIRTEL MONEY</b><br>
                    1. Dial *150*60#
                    2. Choose Option 5 --- “Pay Bills”(“Lipia Bili”)
                    3. Choose Option 5 --- "Government Payment" ("Malipo ya
                    Serikali")
                    5. Enter Reference Number (Kumbu kumbu ya malipo) --
                    Control Number
                    6. Enter your pin (Weka namba yako ya siri)<br><br>
                   <!-- <b>NMB/NBC/NMB/NBC/BoT/CRDB/CRDB BANK</b><br>
                    1. Go to any NMB/NBC/NMB/NBC/BoT/CRDB/CRDB Branch or Wakala
                    2. Fill in Online Payment Bank Pay slip
                    3. Use Control Number ({{ $item->control_number }})<br> 
                   -->
                </div>
            </div>
        </div>
    
