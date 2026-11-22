<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Space Booking Enquiry</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <meta name="description"
        content="Trade Fair is the web application system wich manages and control all largest trade
Fair in Tanzania, such as Dar es salaam International Trade Fair (DITF), Tanzania industirial exhibition, e.t.c ">
    <meta name="keywords"
        content="trade fair, sabasaba, DITF, maonesho, viwanja vya maonesho, fair grounds, tradefair,Tanzania,">
    <meta name="author" content="Tanzania Trade Development Authority (TanTrade)">

    <title>{{ config('app.name', 'Login') }}</title>
    @include('WelcomePageLayouts.welcomeCss')

<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body>



    <!-- Page container -->
    <div class="page-container ">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">

                @include('WelcomePageLayouts.welcomeHeaderMenu')

                <!-- Search field -->
                <div class="panel table-responsive ">

                    <div class=" panel-body">
                        <div class=" col-md-12">
                            <!-- == Exhibition Time Line == -->
                            @include('layouts.ExhibitionTime')
                            <!-- == Exhibition Time Line == -->
                        </div>

                        <div class=" row form-group col-md-12">

                            @if (Session::get('success'))
                                <div class="alert alert-info alert-styled-left alert-bordered">
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span
                                            class="sr-only">Close</span></button>
                                    <span class="text-semibold">Thank our valuable Customer!</span> We are Happy to
                                    inform you that we have received your request, We are working on it and we will be
                                    back to you in one two working days!
                                    <b>For more exhibition information visit: <a class="alert-link"
                                            href="{{ route('About_ditf') }}">{{ route('About_ditf') }}</a></b>

                                </div>
                            @endif

                            <div class=" col-md-12 form-group">
                                <div class=" text-justify form-group">
                                    <div>
                                        The Dar es salaam Internation Fair Ground has about 44,000 M<sup>2</sup> ground
                                        space, it collects more than 3,000 exhibitors and
                                        40,000 visitors at a Time.This make it the bigest exhibition in East and Central
                                        Africa.
                                    </div>
                                    <div>
                                        The space for exhibition are sold by the mode of first come first saved and cost
                                        differ depending to the space category selected. bellow is space costs
                                        description.
                                        <li>International Hall: 210 USD (Conner Stand) 160 USD (Normal Stand) / 1
                                            M<sup>2</sup> </li>
                                        <li>Open Space: 50,000 TZS / 1 M<sup>2</sup></li>
                                        <li>Local Shared Pavilions: 100,000 TZS / 1 M<sup>2</sup></li>
                                        <li>Local Shared Pavilions: 100,000 TZS / 1 M<sup>2</sup></li>
                                        <li>Stand Alone Space: 80,000 TZS / 1 M<sup>2</sup></li>
                                    </div>
                                    <div class=" text-warning">
                                        For more information call: +255 733-002-014
                                    </div>
                                </div>

                                
                                @foreach ($spaceCategories as $data)
                                    <ul class="media-list media-list-linked col-md-4 "
                                        style="border:1px solid rgb(233, 230, 230);">

                                        <li class="media zoom">

                                            <div class="media-link cursor-pointer" data-toggle="collapse"
                                                data-target="#{{ $data->category_id }}"
                                                title="click here to view pavilions">
                                                <div class="media-left"><img src="{{ asset('images/logo.jpg') }}"
                                                        class="img-circle" alt=""></div>
                                                <div class="media-body">
                                                    <div class="media-heading text-bold text-uppercase">
                                                        {{ $data->category_name }}
                                                    </div>
                                                </div>
                                                <div class="media-right media-middle text-nowrap">
                                                   <form action="{{ route('space_booking_inquiries.index') }}" method="POST">
                                                    @method('GET')
                                                    <input type="hidden" name="categoryId" value="{{ $data->category_id }}">
                                                    <button class=" btn btn-primary"> View Pavilions <i class=" icon-arrow-right13"></i></button>
                                                   </form>
                                                </div>
                                            </div>

                                          
                                        </li>
                                    </ul>
                                @endforeach
                            </div>



                            <div class=" row form-group col-md-12">
                                <div class=" col-md-12 form-group" style="background-image: url({{ asset('images/ditf1.png') }})">
                                        <h1 style="font-size: 5vmin; color:blue" class="text-uppercase text-center"><b>{{ $category->category_name }}</b></h1>
                                    <p style="font-size: 2vmin;color:blue"><b>{{ $category->category_descriptions }}</b></p> 
                                </div>
                                
                                @foreach ($pavilions as $item)
                                    <div class="col-lg-4 col-md-6 zoom">
                                        <div class="thumbnail no-padding"
                                            style="background-image: url({{ asset('images/Banda.jpeg') }})">
                                            <div class="thumb">
                                                <img src="{{ asset('images/Banda.jpeg') }}" alt="">
                                                <div class="caption-overflow">
                                                    @if ($item->floor_plan!=Null)
                                                    <span>
                                                        <a href="{{ asset('floorPlanFiles/' . $item->floor_plan) }}"
                                                            class="btn btn-primary btn-icon"
                                                            data-popup="lightbox"><i class=" icon-eye"></i> View
                                                            Floow Plan</a>
                                                    </span>
                                                    @endif
                                                    
                                                </div>
                                            </div>

                                            <div class="caption text-center">
                                                <h6 class=" text-bold no-margin text-uppercase " style=" font-size:4vmin;color:rgb(2, 66, 5);background-color:rgba(255, 255, 255, 0.575);"> <b>{{ $item->pavilionName }}</b>
                                                    <small class="display-block" style="color:rgb(66, 65, 65);text-transform:capitalize"><b>{{ $item->pavilionDescription }}</b></small>
                                                </h6>
                                                <ul class="icons-list mt-15">
                                                    <li><a href="{{ route('space_booking_inquiries.edit',['space_booking_inquiry'=>$item->pavilionID]) }}" class=" btn btn-warning text-white"
                                                            data-popup="tooltip" title="Click to Book"
                                                            data-container="body">Book Space Now <i class=" icon-arrow-right13"></i></a></li>

                                                            <li><a href="#" class=" btn btn-primary text-white"
                                                                data-popup="tooltip" title="Click to Book"
                                                                data-container="body">View Exhibitors <i class=" icon-arrow-right13"></i></a> </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                </div>


            </div>
            <!-- /page content -->
            @include('layouts.footer')

        </div>
        <!-- /page container -->

</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}


<div id="termsAdCondition" class="modal fade" aria-modal="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-primary">

                <h4 class="modal-title text-center">
                    <b style="font-size:2.5vmin">48<sup>th</sup> DAR ES SALAAM INTERNATIONAL TRADE FAIR RULES AND
                        REGULATIONS - 2024.</b></h5>

            </div>

            <div class=" modal-body">
                @include('Support.DITFRules&Regulations')
            </div>

            <div class=" modal-footer">

                <div class=" text-right">

                    <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>

                </div>

            </div>



        </div>

    </div>

</div>

