<style>
    .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            
            font-size: 12px;
            color: #555;
            border-top: 1px solid #ccc;
        }
        
        /* @page {
            margin: 20mm;
        } */
        body {
            margin: 0;
            padding: 0;
        }
</style>
<div>
    <div style=" font-family:Arial, Helvetica, sans-serif">
        <table style="width: 100%;">
            @include('reports.reports_layouts.report_header')
        </table>
        <br>
        <div style="width: 100%; border-bottom:10px solid blue;"></div>
        <div style="width: 100%; border-bottom:10px solid green;"></div>
        <br>

        @foreach ($permit as $item)
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td>
                            <h2 style="text-align: center"> {{ $item->permit_type }} | {{ $item->permitNumber }}</h2>
                            <br>
                            <p style="text-align:justify; line-height:40px">
                                This is to Notify you that <b
                                    style=" border-bottom:3px dashed black;">{{ $item->companyName }}</b>
                                Has been officially recorginized as a 49<sup>th</sup> Dar es salaam International Trade Fair participant
                               and has been granted an
                               
                                    <b style=" border-bottom:3px dashed black;">{{ $item->permit_type }}</b>
                                with permit number <b style=" border-bottom:3px dashed black;">{{ $item->permitNumber }}</b>
                                On <b
                                    style=" border-bottom:3px dashed black;">{{ date('l M d, Y', strtotime($item->permit_updated_at ?? $item->permit_created_at)) }}</b>.
                            </p>
                            <p style="text-align: justify; color:red"> 
                               Please note that this permit is issued solely for the: <b style=" border-bottom:1px dashed rgb(255, 0, 0);color:blue">{{ $item->itemList }}</b> and is valid only for the 49th Dar es Salaam International Trade Fair.
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        
        <br><br>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td> 
                        <p style=" padding:2%;">
                            Digitaly Signed On:<br><br> <i style="font-family:monospace;">{{ $item->permit_updated_at ?? $item->permit_created_at }} </i>
                        </p>
                    </td>
                    <td>
                        <p style="text-align: right">
                          
                            <img
                                src="data:images/png;base64, {{ base64_encode(QrCode::size(90)->generate('SAMS|' . $item->permit_id)) }} ">
                            <br>
                            <b style="font-size:70%; text-align:center">{{ $item->downloadNumber }}</b>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        @endforeach
    </div>

</div>
<div class="footer">
    <span>Printed By; <i>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</i></span>
    <span class="page-number" style="float: right;"> {{ date('M d, Y h:i:s') }}</span>
</div>
