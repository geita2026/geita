<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Site keywords here">
    <meta name="description" content="">
    <meta name='copyright' content=''>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Exhibitor List</title>
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
    <div class="breadcrumbs overlay" style="background-image: url({{ asset('VirtualImages/'.$sector->sectorImage ?? 'images/Exhibitor2.jpeg') }})">
        <div class="container">
            <div class="bread-inner">

                <div class="row">
                    <div class="col-12">
                        <h2 class=" text-uppercase">{{ $sector->sectorName }} EXHIBITORS</h2>
                        <br>
                        <b class=" text-black">Total Exhibitor: {{ number_format($totalExhibitorCount) }}</b>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <div class=" col-md-12">
        <section class="blog section" id="blog" >
			<div class="card-body col-md-12">
                <div class=" col-md-12">
                    <div class=" form-group ">
                        <label>Select sector to view Exhibitors<i class="text-danger">*</i></label>
                        <div id="search-select" onchange="location = this.value;" style="width: 100%">
                        </div>
                    </div>
                </div>
				<div class="row">
                    <p class=" text-info">
                        List of Exhibitors under <b>{{ $sector->sectorName }}</b> Exhibitors
                    </p>
                    @php $i=0; @endphp
					@foreach ($exhibitors as $item)
                    <div class="col-lg-6 col-md-6 col-12" style="padding: 1%;">
						<!-- Single Blog -->
						<div class="single-news">
							<div class="news-heada">
                                @if ($item->companyLogo==Null)
                                <img src="{{ asset('images/coat-of-arms.svg') }}" alt="#" style="height: 10%; width:10%;border-radius:3%" >
                                @else
                                <img src="{{ asset('profile_images/'.$item->companyLogo) }}" alt="#" style="height: 10%; width:10%;border-radius:3%">
                                @endif
								<a href="{{ route('discover_exhibitor.show', ['discover_exhibitor' => $item->participation_id]) }}" style="font-size: 2vmax">{{ $item->companyName }}</a>
							</div>
							<div class="news-body">
								<div class="news-content">
									
									<p class="text">{{ $item->welcome_word }}</p>
                                    <a href="{{ route('discover_exhibitor.show', ['discover_exhibitor' => $item->participation_id]) }}" class=" text-info">READ MORE <i class="fa fa-long-arrow-right"></i></a>
								</div>
							</div>
                           
						</div>
						<!-- End Single Blog -->
					</div>
                    @php $i++; @endphp
                    @endforeach
                    @if ($i==0)
                        <div class=" alert alert-danger col-md-12">
                            No Exhibitor(s) Registered under this Category!
                        </div>
                    @endif
				</div>
			</div>
		</section>
    </div>
    <!-- Footer Area -->
    @include('FrontEnd.Public_Pages.Layouts.Footer')
    <!--/ End Footer Area -->
</body>

</html>

<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_sectors') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: '{{ env('APP_URL') }}/discover_exhibitor/'+item.sectorID+'/edit',
                                text: item.sectorName,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search sector Name . . .',
            minimumInputLength: 0,
        });
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>