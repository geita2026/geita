
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ env('APP_NAME') }} | VirtualTour</title>

    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">

</head>

<style>
    .welcome{
        background-image: url('{{ asset('images/Tz.gif') }}');
        background-size: cover;
        background-size: 100% 100%;
    }
</style>

<body>



<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4 ><b id="h1"><img src="{{ asset('images/tz-flag.jpg') }}" height="50" width="50" class=" img-rounded"> <span class="text-semibold">D I T F</span> - Virtual Tour</b></h4>

            <ul class="breadcrumb breadcrumb-caret position-right">
                <li class="active">
                    <q class=" text-bold text-primary" id="h3">Enjoy immersive cultural and business experiences and discover what makes each exhibitor unique as you explore hundreds of pavilions.</q>
                </li>
            </ul>
        </div>

        <div class="heading-elements">
            <div class="heading-btn-group">
                <a href="{{ route('preview_programs.index') }}" class="btn btn-link btn-float has-text"><i class=" icon-calendar text-primary"></i> <span>Programs</span></a>
                <a href="{{ route('virtual_exhibition.index') }}" target="_tab" class="btn btn-link btn-float has-text"><i class=" icon-sphere3 text-primary"></i><span class=" text-bold">Virtual Exhibition </span></a>
            </div>
        </div>
    </div>
</div>


<!-- Page container -->
<div class="page-container content">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">


            <div class="panel panel-body">
                <h2 class=" text-center text-primary text-uppercase" id="h2"> You are Viewing {{ $pavilion->pavilionName }} PAVILION PARTICIPANT(S).</h2>
                <div class=" text-center"><i class=" text-center text-muted">{{ $pavilion->pavilionDescriptions }}</i></div>
            </div>

            <div class="row col-md-12 form-group">

                @php $i=0; @endphp
                @foreach ($both_members as $data)
                <div class="col-lg-4 col-md-8" title="{{ $data->pavilionName }}"  data-trigger="hover"  data-popup="popover-custom" data-placement="top" title="{{ $data->category_descriptions }}" data-content="{{ $data->category_descriptions }}">
                    <div class="panel panel-body">
                       <div class=" form-group col-md-12">
                         <div class="media">
                            <div class="media-body">
                                <a href="{{ route('discover_exhibitor.show',['discover_exhibitor'=>$data->companyID]) }}">
                                <h6 class="media-heading text-uppercase text-teal-400 text-center text-bold">{{ $data->companyName }} PAVILION.</h6>
                                <hr>
                                 <b class=" icon-location3 text-muted"> Location: <b class=" text-warning">{{ $data->boothNo }}</b></b>
                                </a>
                            </div>
                            <div class="media-right media-middle">
                                <ul class="icons-list icons-list-vertical">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('discover_exhibitor.show',['discover_exhibitor'=>$data->companyID]) }}"><i class=" icon-users4 pull-right"></i>View My Pavilion</a></li>
                                            <li><a href="#"><i class=" note-icon-video pull-right"></i> 360 Pavilion Preview</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                       </div>

                        <div class=" img-rounded col-md-12">
                            <img src="{{ asset('images/pavilion.jpg') }}" style="width: 100%" class="zoom">
                        </div>

                    </div>
                </div>
                @php $i++; @endphp
                @endforeach



                @foreach ($pav_members as $data)

                <div class="col-lg-3 col-md-6" title="{{ $data->pavilionName }}"  data-trigger="hover"  data-popup="popover-custom" data-placement="top" title="{{ $data->category_descriptions }}" data-content="{{ $data->category_descriptions }}">
                    <div class="panel panel-body">
                        <div class="media">
                            <div class="media-left">
                                <a href="#" data-popup="lightbox">
                                    <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle img-lg" alt="">
                                </a>
                            </div>

                            <div class="media-body">
                                <a href="{{ route('virtual_exhibition.edit',['virtual_exhibition'=>$data->pavilionID]) }}">
                                <h6 class="media-heading text-uppercase text-teal-400" id="h3">{{ $data->companyName }} PAVILION.</h6>
                                <b class=" icon-location3 text-muted"> Location: <b class=" text-warning">{{ $data->pavilionName }}</b></b>
                                </a>
                            </div>
                            <div class="media-right media-middle">
                                <ul class="icons-list icons-list-vertical">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('virtual_exhibition.edit',['virtual_exhibition'=>$data->companyID]) }}"><i class=" icon-users4 pull-right"></i>View Our Services</a></li>
                                            <li><a href="#"><i class=" note-icon-video pull-right"></i> 360 Pavilion Preview</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @php $i++; @endphp
            @endforeach

            @if ($i==0)
            <div class=" alert alert-warning">
                No exhibitor Pavilion!
            </div>
            @endif
        </div>

        </div>
        <!-- /search results -->
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

 <script type="text/javascript" src="{{ asset('assets/js/pages/components_popups.js') }}"></script>
