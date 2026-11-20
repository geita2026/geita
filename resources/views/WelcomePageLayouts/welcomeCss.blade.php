<link rel="icon" href="{{ asset('images/nembo.PNG') }}" type="image/gif" sizes="16x16">
<link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
<link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">

<style>

    #logo {
        font-size: 250%;
        padding: 5%;
        text-shadow: 1px 1px 1px rgb(0, 0, 0), 2px 2px 1px rgb(0, 0, 0);
        text-align: center;
    }

    .zoom {
        padding: 2%;
        transition: transform .5s;

    }

    .zoom:hover {
        -ms-transform: scale(1.02);
        /* IE 9 */
        -webkit-transform: scale(1.02);
        /* Safari 3-8 */
        transform: scale(1.02);

        background-color: rgba(228, 226, 226, 0.349);
    }

    .marquee-container {
        width: 100%;
        overflow: hidden;
    }

    .marquee-content {
        white-space: nowrap;
        animation: marqueeAnimation 10s linear infinite;
    }

    .marquee-content:hover {
        animation-play-state: paused;
    }

    @keyframes marqueeAnimation {
        0% {
            transform: translateY(100%);
        }

        100% {
            transform: translateY(-100%);
        }
    }

    .h1 {
        font-size: 8vmin;
    }

    .h2 {
        font-size: 6vmin;
    }

    .h3 {
        font-size: 4vmin;
    }

    .h4 {
        font-size: 3vmin;
    }

    .h5 {
        font-size: 2vmin;
    }

    .h5 {
        font-size: 1.5vmin;
    }

    .headerBackground {
        border-bottom-right-radius: 100px;
        border-top-left-radius: 100px;
        box-shadow: 2px 2px 5px grey;
        box-sizing: content-box;
        background-image: radial-gradient(rgb(250, 250, 249), rgba(177, 226, 215, 0.24), rgba(0, 128, 0, 0.295));
    }

    .headerBackground:hover {
        background-color: rgb(62, 158, 196);
    }

    .myButton:hover {
        background-color: rgb(251, 255, 0);
    }
</style>
