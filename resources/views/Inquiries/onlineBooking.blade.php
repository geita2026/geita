<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | Space Booking Enquiry</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <meta name="description"
        content="GeMEMIS — the Geita Mineral Exhibition Management Information System. Exhibitor registration, space allocation, invoicing, payment and check-in for the Geita mineral exhibitions.">
    <meta name="keywords"
        content="GeMEMIS, Geita, mineral exhibition, madini, maonesho ya madini, exhibition space, space allocation, Tanzania">
    <meta name="author" content="Geita Mineral Exhibition Secretariat">

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
                        <div class=" col-md-12">
                            <!-- == Exhibition Time Line == -->
                            @include('layouts.ExhibitionTime')
                            <!-- == Exhibition Time Line == -->
                        </div>

                        <div class=" row form-group col-md-12">





                            <div class=" col-md-12 ">
                                <div class=" ">
                                    <a href="{{ route('space_booking_inquiries.index') }}"
                                        class=" btn btn-default text-primary"><i class=" icon-arrow-left12"></i>Back</a>
                                </div>


                               
                                <div class=" form-group zoom">
                                    <p style="font-size:3vmax; text-align:center">

                                        <b class=" ml-20">Space Request Form No.1</b>
                                    </p>

                                    <div class=" form-group">
                                        @if (Session::get('success'))
                                        <div class="alert alert-info alert-styled-left alert-bordered">
                                            <button type="button" class="close"
                                                data-dismiss="alert"><span>&times;</span><span
                                                    class="sr-only">Close</span></button>
                                            <span class="text-semibold">Thank our valuable Customer!</span> We are Happy to
                                            inform you that we have received your request, We are working on it and we will
                                            be
                                            back to you in one two working days!
                                            <b>For more exhibition information visit: <a class="alert-link"
                                                    href="{{ route('About_ditf') }}">{{ route('About_ditf') }}</a></b>
    
                                        </div>
                                    @endif
    
                                    </div>

                                    <p class=" p-10 text-justify text-warning" style="font-size:1.5vmin;">
                                        <b class=" text-danger">Note!</b> The Space will be partially reserved by the
                                        customer within the payment period from when he/she receive an invoice, and fully reserved
                                        Only if you pay for the Space within the time frame that
                                        will be provided.
                                        @if ($exists == 1 && $pavilion->floor_plan)
                                            To view the floor Plan <button type="button" data-toggle="modal"
                                                data-target="#FlowPlan{{ $pavilion->pavilionID }}"
                                                class=" btn btn-default">Click here</button>
                                            Select the stand availability in the drop down then book for it.
                                        @endif
                                    </p>

                                    <form action="{{ route('space_booking_inquiries.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="source" value="1">

                                        @if ($exists == 1)
                                            <div class=" row">
                                                <div class="form-group col-md-12">
                                                    <label>Select Booths / Stand From List In
                                                        <b>{{ $pavilion->pavilionName }}</b><i
                                                            class=" text-danger">*</i></label>
                                                    <select name="spaceCategory[]" class="select border-blue"
                                                        multiple="multiple" required>
                                                        @foreach ($booths as $booth)
                                                            <option value=" {{ $booth->boothNo }}">
                                                                {{ $booth->boothNo }} ~ ({{ $booth->boothSize }}
                                                                    SQM) ~
                                                                    {{ $booth->category_name }} ~
                                                                    {{ $booth->category_cost }}
                                                                    {{ $booth->type_of_currency }} /1SQM
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        @error('spaceCategory')
                                                            {{ $message }}
                                                        @enderror
                                                </div>
                                               
                                            </div>
                                        @else
                                            <div class=" col-md-12">
                                                <label>Selected Stand</label>
                                                <input type="text" name="spaceCategory[]"
                                                    value="{{ $pavilion->pavilionName }} ({{ number_format($pavilion->pavilion_size, 2) }} Units / SQM)"
                                                    class=" form-control" readonly>
                                            </div>
                                        @endif


                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Company Name<i class=" text-danger">*</i></label>
                                                <input type="text" name="companyName"
                                                    class=" form-control border-blue" value="{{ old('companyName') }}"
                                                    required>
                                                <span class="text-danger">
                                                    @error('companyName')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Company Email<i class=" text-danger">*</i></label>
                                                <input type="text" name="companyEmail"
                                                    class=" form-control border-blue" value="{{ old('companyEmail') }}"
                                                    required>
                                                <span class="text-danger">
                                                    @error('companyEmail')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>

                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Company / Contact Person Phone Number<i
                                                        class=" text-danger">*</i></label>
                                                <input type="text" name="companyPhone"
                                                    class=" form-control border-blue" value="{{ old('companyPhone') }}"
                                                    required>
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
                                        <div class=" row">
                                            <div class="form-group col-md-6">
                                                <label>What is your Participation Experience ?<i
                                                        class=" text-danger">*</i></label>
                                                <select name="experienceStatus" class=" form-control border-primary"
                                                    required>
                                                    <option value="0">It's my first Time</option>
                                                    <option value="1">I have been participating</option>
                                                </select>
                                                <span class="text-danger">
                                                    @error('experienceStatus')
                                                        {{ $message }}
                                                    @enderror
                                            </div>
                                            <div class=" form-group col-md-6">
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
                                        </div>

                                        <div class=" form-group">
                                            <label>Any more Description if available</label>
                                            <textarea name="moreDescription" class=" form-control border-blue" value="{{ old('moreDescription') }}"
                                                placeholder="Any more description type here"></textarea>
                                            <span class="text-danger">
                                                @error('moreDescription')
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
                                                    abide by all the <a href="#" data-toggle="modal"
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
                    <b style="font-size:2.5vmin">GEITA MINERAL EXHIBITION RULES AND
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





<div id="FlowPlan{{ $pavilion->pavilionID }}" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button class=" close" data-dismiss="modal"><b>X</b></button>
                <h4 class="modal-title text-center text-uppercase">
                    <b style="font-size:2.5vmin"> {{ $pavilion->pavilionName }} FLOW PLAN FOR THE 48<sup>th</sup> DAR
                        ES SALAAM INTERNATIONAL TRADE FAIR RULES AND
                        REGULATIONS - 2024.</b></h5>
            </div>

            <div class=" modal-body">
                <iframe src="{{ asset('floorPlanFiles/' . $pavilion->floor_plan) }}"
                    style="width: 100%;height:1000px">

                </iframe>
            </div>

            <div class=" modal-footer">

                <div class=" text-right">

                    <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>

                </div>

            </div>



        </div>

    </div>

</div>
