<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Download Programs</title>
    <style>
        table {
            width: 100%;

        }

        .programs {
            width: 100%;
            padding: 1%;
            border: 1px double green;
            height: 100%;
        }

        .title {
            text-transform: uppercase;
            text-align: center;

        }

        th {
            text-align: center;

        }

        td {
            text-align: justify;

        }

        .virtual-content {
            background-color: rgb(134, 218, 148);
            border: 1px solid rgb(151, 206, 194);
            padding: 1%;
        }
    </style>
</head>

<body>
    <div class="programs">

        <table>
            <thead>
                <tr style="border: 1px solid rgb(3, 3, 255);">
                    <th style=" text-align:left">
                        <img src="{{ $base64_nembo }}" height="70" width="90">
                    </th>
                    <th style="text-align: center">
                        TANZANIA TRADE DEVELOPMENT AUTHORITY (Tan Trade)<br>
                        P.O. BOX 5402, Dar Es Salaam, Tanzania<br>
                        Tel: +255 22 2850238, Fax: +255 22 2850539
                    </th>
                    <th style=" text-align:right">
                        <img src="{{ $base64_logo }}" height="70" width="90">
                    </th>
                </tr>
                <tr>
                    <th colspan="3">
                        <div style="border: 5px solid blue;"></div>
                        <div style="border: 5px solid green;"></div>
                    </th>
                </tr>
            </thead>
        </table>

        <div>
            <h5 class="title">THE {{ $eventName ?? '' }} PROGRAMS - {{ date('Y') }}.</h5>
        </div>

        @foreach ($programs as $key => $item)
            <div style="border:1px solid rgb(225, 225, 228);padding:1%">
                <b style="border-radius:100%;background-color:rgb(0, 238, 255); padding:1.5%">{{ $key+1 }}</b>
                <b style="background-color: rgb(0, 238, 255);padding:0.5%">{{ date('l, d M, Y', strtotime($item->program_date)) }}</b>
                <p><b>PROGRAM TITLE:</b> {{ $item->program_title }}</p>
                <p><b>PROGRAM DESCRIPTION:</b> {{ $item->program_description }}</p>
                <p><b>PARTICIPANTS:</b> {{ $item->characters }}</p>
                <p><b>PROGRAM STATUS:</b>
                    @if ($item->program_date > date('Y-m-d'))
                        <b style="color:rgb(231, 161, 29);text-align:center;">WAITING</b>
                    @elseif ($item->program_date == date('Y-m-d'))
                        <b style="color: rgb(0, 68, 255);">INPROGRESS</b>
                    @elseif ($item->program_date < date('Y-m-d'))
                        <b style="color: green;">COMPLETED</b>
                    @endif
                </p>
            </div> <br>
        @endforeach
        <br><br>
        <p>
            <b style="color:red;">NOTE:</b> Date or program may be updated at any time,
            remember to visit our site frequently to be updated with new Time table.
            <br><br>
        </p>
        <div style=" position: fixed;bottom:0; text-align:center;width:100%;">
            <b style="color:green"><q>TanTrade Foostering Business</q></b> <b style="margin-left: 2%;">Visit: <a
                    href="www.tantrade.go.tz">www.tantrade.go.tz</a></b>
        </div>

    </div>
</body>

</html>
