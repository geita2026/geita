
<style>
    
</style>


<div style=" display:block">
    @foreach ($badges as $item)
    <table style="width:50%; border: 10px dotted black; padding:2%; margin-top:5%; height:00px;">
        <tbody>
            <tr style="width: 100%" >
            <tr>
                <th>
                    <div style="text-align: left;">
                        <img src="{{ asset('images/nembo.png') }}" width="50" width="50">
                    </div>
                </th>
                <th colspan="2">
                    <div style="text-align: right">
                        <img src="{{ asset('images/logo.jpg') }}" width="80" height="50">
                    </div>
                </th>
            </tr>
            <tr>
                <th colspan="3">
                    <b>GEITA MINERAL EXHIBITION</b>
                </th>
            </tr>
            <tr>
                <th colspan="3">
                    <div style=" border-bottom:5px solid green;"></div>
                   <div style=" border-bottom:5px solid blue;margin-top:2px;"></div>
                </th>
            </tr>
            <tr>
                <th colspan="3">
                    <div style="text-align: center; font-size:90%">28<sup>th</sup>June - 13<sup>th</sup> July  2023</div>
                </th>
            </tr>
            <tr>
                <td colspan="3">
                   <div style="text-transform: uppercase;"><b>{{ $item->companyName }}.</b></div>
                   <div style="text-align:center;padding:1%; background-color: {{ $item->group_background_color }};color:{{ $item->text_color }};"><b>{{ $item->group_name }}</b></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="text-align:left">
                        <img src="data:images/png;base64, {{ base64_encode(QrCode::size(70)->generate($item->member_id)) }} ">
                    </div>
                </td>
                <td colspan="2">
                    <div style="text-align:right;">
                        <img src="{{ asset(asset('Members/'.$item->member_image)) }}" height="75" width="85" style="border-radius: 1%; border:1px solid blue;">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                   <div><b style="text-transform: uppercase; font-size:100%; font-family:monospace;">{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}</b></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                   <div style=" font-size:100%;font-family:monospace;"><b>Visit: </b> <i style="color:blue">www.gememis.go.tz</i></div>
                </td>
            </tr>
        </tr>
        </tbody>
       </table>
       @endforeach
   </div>





