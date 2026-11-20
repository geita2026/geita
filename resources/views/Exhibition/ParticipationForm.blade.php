@foreach ($exhibitor as $item)
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Participation Form</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            .content {

                padding-bottom: 10px;
                /* Avoid overlap with footer */
            }


            .dotted-line {
                border: none;
                border-top: 2px dotted #000;
                /* Adjust the width */
                margin: 20px 0;
            }

            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #f1f1f1;
                text-align: center;
                padding: 1px;
                border-top: 1px solid #ddd;
                font-size: 8px;
            }
        </style>
    </head>

    <body style="text-align: center">
        <div class="content">
            @php
                #Logo
                $logo = public_path('images/logo.jpg');
                $image = Image::make($logo);
                $base64_logo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
                #end logo

                #Nembo
                $ufi = public_path('/images/UFI.png');
                $image = Image::make($ufi);
                $base64_ufi = 'data:image/png;base64,' . base64_encode($image->encode('png'));
                #End Nembo
                // ***********************
                #DITF LOGO
                $nembo = public_path('/images/DITF.png');
                $image = Image::make($nembo);
                $base64_nembo = 'data:image/png;base64,' . base64_encode($image->encode('png'));
            @endphp


            <table style="text-align: center; width:100%">
                <thead style="font-size:15px;">
                    <tr>
                        <th style=" text-align:left">
                            <img src="{{ $base64_nembo ?? '' }}" height="50" width="70">
                        </th>
                        <th style="text-align: center; ">
                            <h4 style="font-size:12px">
                                THE 49<sup>th</sup> DAR ES SALAAM INTERNATIONAL
                                TRADE FAIR (DITF)
                            </h4>
                            <i style="font-size:8px">[IT WILL HAPPEN IN DAR-ES-SALAAM FROM JUNE 28 TO JULY 13, 2025]</i>
                        </th>

                        <th style=" text-align:right">
                            {{-- {{ $base64_logo }} --}}
                            <img src="{{ $base64_ufi ?? '' }}" height="50" width="70">
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align: center;" colspan="3">
                            <img src="{{ $base64_logo ?? '' }}" height="50" width="100">
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <i style="font-size:10px">
                                APPLICATION FORM FOR PARTICIPATION IN THE INTERNATIONAL TRADE FAIR - 2024/2025
                            </i>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3" style="font-size:10px">
                            <i>
                                (Form No. 2A has been prepared under Regulation No. 11, the information you provide will
                                be used in the participants' booklet.)
                            </i>
                        </th>
                    </tr>
                </thead>
            </table>
            <br><br>

            {{-- Company Information --}}

            <table style="width: 100%;" border="1">
                <tbody>
                    <tr>

                        <td style="border: 1px solid black" colspan="3">
                            APPLICANT/COMPANY INFORMATION
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Company/Institution/Business Name</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->companyName }}</d>
                        </td>
                        <td>
                            <b>Mobile Phone</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->companyPhone }}</d>
                        </td>
                        <td>
                            <b>Address </b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->companyAddress }}</d>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Email</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->companyEmail }}</d>
                        </td>
                        <td>
                            <b>Website</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->website }}</d>
                        </td>

                        <td>
                            <b>TIN Number</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->tinNo }}</d>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            {{-- Contact Person Information --}}

            <table style="width: 100%" border="1">
                <tbody>
                    <tr>
                        <td style="border: 1px solid black" colspan="3">
                            CONTACT PERSON INFORMATION
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size:12px">
                            <b>Confirmation/Statement:</b> I agree that the decisions made by the Committee will be
                            final
                            decisions, and that the Company/Institution will participate in the Exhibitions, and I agree
                            to adhere to the Rules and Regulations of the Exhibitions as they will be set.
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Full Name </b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->personalName }}</d>
                        </td>
                        <td>
                            <b>Phone Number</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->personalPhone }}</d>
                        </td>

                        <td>
                            <b>Email</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->personalEmail }}</d>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Title</b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->personalTitle }}</d>
                        </td>

                        <td>
                            <b>Signature & Stamp</b>
                            <hr class="dotted-line">
                        </td>
                        <td>
                            <b>Date: </b>
                            {{ date('M d Y h:i:s',strtotime($item->served_at)) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            {{-- Space Chosen --}}
            <table style="width: 100%" border="1">
                <tbody>
                    <tr>
                        <td style="border: 1px solid black" colspan="3">
                            SELECTION OF THE EXHIBITION AREA
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <b>Area/Building/Tent/Booth that was chosen and it's size or unit</b><br>
                            <d style="border-bottom: 1px dashed black;font-size:10px">{{ $item->exhibitionSpace }}</d>
                        </td>

                        
                        <td>
                            <b>You expect to make sales during the exhibition </b><br>
                            <d style="border-bottom: 1px dashed black;">{{ $item->makeSales }}</d>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            <table style="width: 100%;" border="1">
                <tbody>
                    <tr>
                        <td style="border: 1px solid black" colspan="3">
                            LIST OF PRODUCTS TO BE EXHIBITED
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <d style="border-bottom: 1px dashed black;">{{ $item->exhibitedProducts }}</d>
                        </td>
                    </tr>



                </tbody>
            </table>

            <br><br>
            <table style="width: 100%" border="1">
                <tbody>
                    <tr>
                        <td style="border: 1px solid black" colspan="3">
                            TO BE FILLED BY OFFICE
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Name of the officer who received </b>
                            <hr class="dotted-line" style="width: 200px">
                        </td>

                        <td>
                            <b>Signature </b>
                            <hr class="dotted-line" style="width: 200px">
                        </td>
                        <td>
                            <b>Date </b>
                            <hr class="dotted-line" style="width: 200px">
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        {{-- <footer>
            Uwanja wa Mwl.J.K.Nyerere, Barabara ya Kilwa, PlotNo.436,BlockA, S.L. P5402, Dar es Salaam, <br>Tanzania
            Tel:
            (SAC) +255 733 00 20 14,
            E-mail: sac@tantrade.go.tz or info@tantrade.go.tz; Website:http://www.tantrade.go.tz
        </footer> --}}
    </body>

    </html>
@endforeach
