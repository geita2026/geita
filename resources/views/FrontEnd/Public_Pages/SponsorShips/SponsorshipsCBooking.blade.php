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
    <style>
        .video-container {
            /* height: 300px;
    width: 300px; */
            position: relative;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: cover;
            z-index: 0;
        }

        /* Just styling the content of the div, the *magic* in the previous rules */
        .video-container .caption {
            z-index: 1;
            position: relative;
            text-align: center;
            color: #dc0000;
            padding: 10px;
        }
    </style>
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

                       <!-- == Exhibition Time Line == -->
                       @include('layouts.ExhibitionTime')
                       <!-- == Exhibition Time Line == -->
                        <div class=" row form-group col-md-12">
                            <div class=" form-group">
                                <p style="text-align: justify">
                                    Sponsoring Dar es salaam International Trade Fair (DITF) event which collects, more
                                    than 4,000
                                    companies, 26 Countries and 400,000 visitors can offer numerous benefits for
                                    individuals or businesses. Here
                                    are some common reasons why you might choose to sponsor Dar es salaam International
                                    Trade Fair
                                    (DITF):-
                                    <li><b>Brand Exposure and Visibility: </b> Dar es salaam International Trade Fair
                                        (DITF) provide an excellent platform for
                                        increased brand visibility. Sponsors often get exposure through signage, logos,
                                        and mentions in various promotional materials.
                                    </li>
                                    <li>
                                        <b>Marketing and Promotion: </b>Sponsorship offers opportunities for extensive
                                        marketing and promotional efforts. Your brand can be featured in event-related
                                        advertising, social media campaigns, press releases, and more
                                    </li>
                                    <li>
                                        <b>Community Engagement: </b> Sponsoring Dar es salaam International Trade Fair
                                        (DITF)  demonstrates your commitment to the community and can enhance your brand's image as a
                                        socially responsible entity.
                                    </li>

                                </p>
                            </div>

                            @if (Session::get('success'))
                                <div class="alert alert-info alert-styled-left alert-bordered">
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span
                                            class="sr-only">Close</span></button>
                                    <span class="text-semibold">Thank our valuable Customer!</span> We are pleased to
                                    inform you that we have received your request with:
                                    <b>{{ Session::get('success') }}</b>
                                    We are working on it and we will be back to you in one two working days!
                                    <b>For more exhibition information visit: <a class="alert-link"
                                            href="{{ route('About_ditf') }}">{{ route('About_ditf') }}</a></b>

                                </div>
                            @endif

                            <div class=" col-md-4 ">

                                <h3 class=" text-center text-uppercase  bg-brown p-10">SPONSORSHIP PACKAGES</h3>
                                @foreach ($packages as $data)
                                    <div class=" form-group">
                                        <div class=" card zoom" style="border:1px solid rgb(196, 194, 194);">
                                            <div class=" card-header text-uppercase text-bold text-center"
                                                style="border:1px dashed rgb(153, 211, 208); ">
                                                <h2><b>{{ $data->package_name }}</b> <br> <b
                                                        class=" text-warning">({{ number_format($data->package_amount, 2) }}
                                                        {{ $data->type_of_currency }})</b></h2>

                                            </div>
                                            <div class=" card-body">
                                                <h3 class=" text-bold">Package Benefits</h3>
                                                @foreach ($benefit as $item)
                                                    @if ($item->package_id == $data->package_id)
                                                        <h5><i
                                                                class=" icon-arrow-right13 text-primary text-bold"></i>{{ $item->benefitTitle }}
                                                        </h5>
                                                    @endif
                                                @endforeach

                                            </div>
                                            <div class=" card-footer">

                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <div class=" col-md-8 ">

                                <div class=" form-group zoom">
                                    <p style="font-size:2vmax; text-align:center">
                                        <b>Sponsorship Request Form No.1</b>
                                    </p>
                                    <p class=" p-10 text-justify text-warning"
                                        style="font-size:1.8vmin; text-align:center">
                                        <b class=" text-danger">Note!</b> This form should be completed by the company's
                                        representative and will be responded within one to two working days.
                                    </p>
                                    <p style="font-size:2vmin" class="">
                                        After submission of your inquiry you will be asigned a <a data-popup="popover"
                                            title="Inquiry Number" data-trigger="hover"
                                            data-content="Is a unique enquiry Number generated by system to identify your request uniquely">Request
                                            Number</a>. This will be used for easily track for the feedback.
                                    </p>
                                    <form action="{{ route('space_booking_inquiries.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="source" value="1">
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Company Name<i class=" text-danger">*</i></label>
                                                <input type="text" name="companyName"
                                                    class=" form-control border-blue" value="{{ old('companyName') }}" required>
                                                <span class="text-danger">
                                                    @error('companyName')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Company Email<i class=" text-danger">*</i></label>
                                                <input type="text" name="companyEmail"
                                                    class=" form-control border-blue" value="{{ old('companyEmail') }}" required>
                                                <span class="text-danger">
                                                    @error('companyEmail')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>

                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Company Phone<i class=" text-danger">*</i></label>
                                                <input type="text" name="companyPhone"
                                                    class=" form-control border-blue"
                                                    value="{{ old('companyPhone') }}" required>
                                                <span class="text-danger">
                                                    @error('companyPhone')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Country of Residence<i class=" text-danger">*</i></label>
                                                <select name="ResindenceCountry" class="select border-blue"
                                                    multiple="multiple" required>
                                                    @foreach ($country as $item)
                                                        <option value="{{ $item->country_name }}">
                                                            {{ $item->country_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">
                                                    @error('ResindenceCountry')
                                                        {{ $message }}
                                                    @enderror
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class="form-group col-md-6">
                                                <label>What is your Participation Experience ?<i
                                                        class=" text-danger">*</i></label>
                                                <select name="experienceStatus" class=" form-control border-primary" required>
                                                    <option value="0">It's my first Time</option>
                                                    <option value="1">I have been participating</option>
                                                </select>
                                                <span class="text-danger">
                                                    @error('experienceStatus')
                                                        {{ $message }}
                                                    @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>What space category do you prefer to get your stand during
                                                    exhibition
                                                    ?</label>
                                                <div class="multi-select-full">
                                                    <select name="spaceCategory[]" class="select border-blue"
                                                        multiple="multiple" required>
                                                        @foreach ($packages as $item)
                                                            <option value="{{ $item->package_name }}">
                                                                {{ $item->package_name }} {{ number_format($item->package_amount) }} TZS
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        @error('spaceCategory')
                                                            {{ $message }}
                                                        @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Files -->
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Certificate of Registration / Certificate of Compliance<i
                                                        class=" text-danger">*</i></label>
                                                <input type="file" name="registrationCertificate"
                                                    class=" form-control border-blue"
                                                    value="{{ old('registrationCertificate') }}" required>
                                                <span class="text-danger">
                                                    @error('registrationCertificate')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class=" form-group col-md-6">
                                                <label>Company Profile / Brochure / Business Card / Company Leterhead /
                                                    Catalogue <i class=" text-danger">*</i></label>
                                                <input type="file" name="companyProfile"
                                                    class=" form-control border-blue"
                                                    value="{{ old('companyProfile') }}" required>
                                                <span class="text-danger">
                                                    @error('companyProfile')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class=" form-group">
                                            <label>Product expected to be exhibited<i
                                                    class=" text-danger">*</i></label>
                                            <textarea name="productsToBeExhibited" class=" form-control border-blue" value="{{ old('productsToBeExhibited') }}"
                                                placeholder="List Products to be exhibited Here" required></textarea>
                                            <span class="text-danger">
                                                @error('productsToBeExhibited')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>

                                        <div class=" form-group">
                                            <label>Any more Description if available</label>
                                            <textarea name="moreDescription" class=" form-control border-blue" value="{{ old('productsToBeExhibited') }}"
                                                placeholder="List Products to be exhibited Here"></textarea>
                                            <span class="text-danger">
                                                @error('productsToBeExhibited')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>


                                        <div class=" row ">
                                            <div class=" col-md-10">
                                                <label>
                                                    <input type="checkbox" required>
                                                    I accept that the selection committee’s decision is final and if
                                                    selected, my firm will participate in the trade fair and I accept to
                                                    abide by all the  <a href="#" data-toggle="modal"
                                                    data-target="#termsAdCondition"> Rules
                                                    and regulations</a> governing this participation.
                                                    We have adequate funds for participating in this event.
                                                </label>
                                            </div>
                                            <div class=" col-md-2">
                                                <button type="submit"
                                                    class=" btn btn-primary btn-ladda btn-ladda-spinner">Submit Request
                                                    <i class=" icon-arrow-right13"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
               
                <!-- /main content -->

            </div>
            <!-- /page content -->
            @include('layouts.footer')

        </div>
        <!-- /page container -->

</body>

</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}
