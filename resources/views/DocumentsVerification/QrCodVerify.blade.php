<div style="padding: 2%; text-align:justify">
    @if ($data != null)
        <!DOCTYPE html>
        <html>
        <header>
            <title> GEITA MINERAL EXHIBITION QR CODE VERIFICATION RESPONSE</title>
        </header>
        <body>
            <div>
                <h4 style="text-align: center;font-size:2.5vmax">GEITA MINERAL EXHIBITION eDOCUMENT VERIFICATION RESPONSE</h4>
            </div>
            <p style="color: green;font-size:2vmax">
                This is to certify that the bearer of this document has been granted to <b></b>
                as the necessary permissions as instracted to the document.
            </p>
            <br> 

            <table style="width:100%;font-size:2vmax;text-transform:uppercase;" border="1">
                <thead>
                    <tr>
                        <th>Document Title</th>
                        <th>Document OWNER</th>
                        <th>Document DESCRIPTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $data['documentTitle'] }}</td>
                        <td>{{ $data['ownerName'] ?? 'UNRECOGINAZED' }}</td>
                        <td>{{ $data['description'] }}</td>
                    </tr>
                </tbody>
            </table>
            <br><br><br>
            <p style="font-size: 0.8vmax; ">If you have any inquiries regarding this Document , please
                contact the Geita Mineral Exhibition Secretariat via <a
                    href="tel:{{ config('global.CALL_CENTER') }}">{{ config('global.CALL_CENTER') }}</a> or <a href="mailto:info@gememis.go.tz">info@gememis.go.tz</a> regarding the information provided above.</p>

        </body>

        </html>
    @endif

    @if ($data == null)
        <br><br>
        <h2 style=" color:red;">
            This Document is not recognized by the Geita Mineral Exhibition Secretariat, Please contact Us via <a
                href="tel:{{ config('global.CALL_CENTER') }}">{{ config('global.CALL_CENTER') }}</a>  For help and Advice before Making any decision regading this document.
        </h2>
    @endif
</div>
