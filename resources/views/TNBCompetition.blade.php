<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Title -->
    <title>TNB Logo Competition</title>
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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('frontEndAssets/img/NationalBrand.png') }})">
        <div class="container">
            <div class="bread-inner">
                <div class="row">
                    <div class="col-12">
                        <h2 style="text-transform: capitalize;font-size:2vmax">Tanzania National Brand Logo Competition
                        </h2>
                        <h4 class=" text-warning">"Made In Tanzania Fahari ya Tanzania"</h4>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <br>
    <div class=" card-body" id="terms&conditions">
        <div class="row">
            <div class=" col-md-12">
                <p style="text-align:justify;color:black">
                    Are you a creative mind with a passion for design? Here’s your chance to make a lasting impact! Join
                    the Tanzania National Brand Logo Competition and showcase your talent by crafting a logo that
                    represents the heart and soul of Tanzania.
                </p>
                <p style="text-align:justify;color:black">
                    This competition invites designers, artists, and creatives with in the nation to design a brand logo
                    that captures Tanzania’s unique identity, values, and future vision. The winning logo will be used
                    as a national symbol, recognized both locally and globally, and will stand as an emblem of Tanzanian
                    pride.
                </p>
                <br>
                <p>
                <h4>Competition Guidelines</h4>
                <p style="text-align: justify">
                <ol>
                    1. The competition is open to designers, artists, and creative individuals or companies aged 18 and
                    above and Participants must be residents of Tanzania.
                </ol>
                <ol>
                    2. Must be original creations of the participant,
                    Each participant can submit up to three unique logo designs.
                </ol>
                <ol>
                    3. Submissions must be in digital format (<b>JPEG, PNG, or PDF</b>) with a minimum resolution of 300
                    DPI.
                </ol>
                <ol>
                    4. The logo should be accompanied by a brief description explaining the design concept and its
                    relevance to the “Made In Tanzania” brand.
                </ol>
                <ol>
                    5. The selected three finalists shall be required to submit the original master copy of the design.
                </ol>
                <ol>
                    6. Submitting an entry, participants grant the organizers the right to use, reproduce, and modify
                    the logo for promotional and marketing purposes related to the “Made In Tanzania” brand.
                </ol>
                <ol>
                    7. The winning entry will become the property of the organizers, who will have exclusive rights to
                    the logo design.
                </ol>
                <ol>
                    8. Entries will be judged by an online public poll on the best 3 entries which will be judged by a
                    panel of experts based on criteria such as creativity, originality, relevance to the brand, visual
                    appeal, and suitability for diverse applications.
                </ol>
                <ol>
                    9. The decision of the judges will be final and binding.
                </ol>
                <ol>
                    10. Entries that do not comply with the submission guidelines or contain inappropriate content will
                    be disqualified.
                </ol>
                <ol>
                    11. Participants found to be engaging in fraudulent or unethical practices will be disqualified.
                </ol>
                <ol>
                    12. The organizers will not be held responsible for any loss, damage, or liability arising from
                    participation in the competition.
                </ol>
                </p>
                <p style="text-align: justify">
                <h4>Prize Distribution</h4>
                The prizes will be distributed in Tanzanian Shillings among the winners as follows
                <ol><i class=" fa fa-star text-primary"></i> First Prize is 3,000,000 TZS</ol>
                <ol><i class=" fa fa-star text-primary"></i> Second Prize is 2,000,000 TZS</ol>
                <ol><i class=" fa fa-star text-primary"></i> Third Prize is 1,000,000 TZS</ol>
                </p>

                <p style="text-align: justify">

                </p>
                </p>

            </div>
        </div>

    </div>
    </div>

    <div class=" card-body">
        <h4>Submision of your Artwork</h4>
        <p>
        <ol>
            Create folder and name it as <b>TNB</b>, copy your logo files (Must not be greater than 3 files) include
            your logo description in your folder.
        </ol>
        <ol>
            2. Zip your folder with zip or rar File extension.
        </ol>
        <ol>
            3. Fill the form and upload your zipped file then Submit your work.
        </ol>
        <ol>
            4. You will receive confirmation Email from National Brand Logo Competition Committee of receiving your
            Artwork.
        </ol>
        <ol>
            <b style="color:black">5. Submision Deadline is: December 23, 2024.</b>
        </ol>
        </p>
        <form action="{{ route('submit_art_work') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class=" form-group col-md-12">
                @if (date('Y-m-d') > '2024-12-23')
                    <div class="alert alert-danger">
                        Tanzania National Brand Logo Competetion is now closed! shortlisted Applicants will be contacted, After review process completed!
                    </div>
                @else
                    <div class=" row">
                        <div class=" form-group col-md-6">
                            <label>Competetion Registration Number<i class=" text-danger">*</i></label>
                            <input type="text" name="competitionNumber" class=" form-control"
                                placeholder="eg. TNBC-000-000" value="{{ old('competitionNumber') }}">
                            <span class=" text-danger">
                                @error('competitionNumber')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class=" form-group col-md-6">
                            <label>Zipped File <i class="text-danger">*</i> <span>(Allowed File extension, .zip &
                                    .rar)</span></label><br>
                            <input type="file" name="zippedFile" class="form-control">
                            <span class=" text-danger">
                                @error('zippedFile')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>
                    <div class=" row">
                        <div class=" form-group col-md-12">
                            <label>Description</label>
                            <textarea class=" form-control" name="logoDescription" rows="5" placeholder="Provide description here">{{ old('logoDescription') }}</textarea>
                            <span class=" text-danger">
                                @error('logoDescription')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                    </div>
                    <div class=" col-md-12 text-right">
                        <button type="submit" class=" btn btn-primary">Submit your Artwork <i
                                class=" fa fa-arrow-right"></i></button>
                    </div>
                @endif
            </div>
        </form>
    </div>


    <!-- End Blog Area -->
    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>

<script></script>
