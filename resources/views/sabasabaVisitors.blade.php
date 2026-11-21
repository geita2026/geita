<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="GeMEMIS — the Geita Mineral Exhibition Management Information System. Exhibitor registration, space allocation, invoicing, payment and check-in for the Geita mineral exhibitions.">
    <meta name="keywords"
        content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="author" content="Geita Mineral Exhibition Secretariat">
    <title>{{ env('APP_NAME') }} | Exhibition visitors</title>
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
</head>
<h1>HELLOW</h1>
<body>

</body>

</html>
@include('WelcomePageLayouts.welcomeJs')
