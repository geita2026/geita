<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Trade Fair is the web application system wich manages and control all largest trade
    Fair in Tanzania, such as Dar es salaam International Trade Fair (DITF), Tanzania industirial exhibition, e.t.c ">
    <meta name="keywords"
        content="trade fair, sabasaba, DITF, maonesho, viwanja vya maonesho, fair grounds, tradefair,Tanzania,Exhibition">
    <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">
    <title>{{ env('APP_NAME') }} | SabaSaba visitors </title>
    <style>
        .video-container {
            height: 50px;
            width: 100%;
            position: relative;
        }

        .video-container video {
            width: 100%;
            position: absolute;
            object-fit: cover;
            z-index: 0;
        }

        /* Just styling the content of the div, the *magic* in the previous rules */
        .video-container .caption {
            z-index: 1;
            position: relative;
            background-color: #0026fc54;
            width: 100%;
            color: rgb(255, 255, 255);
            padding: 5%
        }
    </style>
    @include('WelcomePageLayouts.welcomeCss')
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>
<h1>HELLOW</h1>
<body>

</body>

</html>
@include('WelcomePageLayouts.welcomeJs')

