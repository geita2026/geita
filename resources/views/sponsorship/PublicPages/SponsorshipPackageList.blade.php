<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Tags -->
    <!-- Title -->
    <title>{{ env('APP_NAME') }} | Space Categories</title>
    @include('FrontEnd.Public_Pages.Layouts.CssDesigns')
    <style>
        .changeColor:hover {
            background-color: rgb(214, 212, 212);
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    @include('FrontEnd.Public_Pages.Layouts.PageLoader')
    <!-- End Preloader -->


    <!-- Get Pro Button -->

    <!-- Header Area -->
    <header class="header">
        <!-- Topbar -->
        @include('FrontEnd.Public_Pages.Layouts.TopHeader')
        <!-- End Topbar -->
        <!-- Header Inner -->
        @include('FrontEnd.Public_Pages.Layouts.Header')
        <!--/ End Header Inner -->
    </header>
    <!-- End Header Area -->

    <!-- Breadcrumbs -->
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('images/BackGround.jpg') }})">
        <div class="container">
            <div class="bread-inner">
                <div class="row">
                    <div class="col-12">
                        <h2 class=" text-uppercase">{{ $spaceCategories->packageGroupName }}</h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <section class=" col-md-12">
        <div class=" card-body">
            <div class="row col-md-12" style="padding: %">
                <div class=" col-md-12 text-justify p-2">
                    {{ $spaceCategories->packageGroupDescription }}
                    <h5>Select a package group below that interests you to view the packages.</h5>
                </div>
                
                @foreach ($packages as $item)
                    <div class="col-lg-3 col-md-6 col-12 zoom ml-3 mt-3"
                        style="background-color: {{ $spaceCategories->backgroundColor }};color:{{ $spaceCategories->textColor }}">
                        <!-- Single Blog -->
                        <div class="single-news">
                            <div class="news-body p-15">
                                <div class="news-content">
                                    <a href="{{ route('book-packages.edit',['book_package'=>$spaceCategories->packageGroupId]) }}"
                                        class=" " style="color: {{ $spaceCategories->textColor }}">
                                        <h5 class=" text-uppercase">{{ $item->package_name }} [ {{ number_format($item->package_amount) }}{{ $item->type_of_currency }} ]</h5>
                                        <div class="p-1 text-justify">{{ $item->package_description }}<br><br>
                                            
                                            <b
                                                class="text-white btn">Book packages . . . <i
                                                    class=" fa fa-arrow-circle-right"></i></b>
                                        </div>

                                    </a>
                                </div>
                                <br>
                            </div>
                        </div>
                        <!-- End Single Blog -->
                    </div>
                @endforeach
            </div>
            <br><br>
        </div>
    </section>

    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>
