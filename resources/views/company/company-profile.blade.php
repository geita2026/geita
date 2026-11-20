<x-app-layout>
<title>{{ env('APP_NAME') }} | Company Profile</title>
<div class="page-header">
    <div class="page-header-content">
        @include('layouts.heading-elements')
    </div>
    <div class="breadcrumb-line breadcrumb-line-component">
    <ul class="breadcrumb">
        <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>COMPANIES REGISTRATIONS</a></li>
        <li class="active">View-All Companies</li>
    </ul>
    <ul class="breadcrumb-elements">
       @include('layouts.help')
    </ul>
</div>
</div>


<!-- Content area -->
<div class="content">

   <div class=" panel panel-flat">
    <div class=" panel-body">
        <div class=" row">
            <div class=" col-md-12">
                <ul class="nav navbar-nav">
                    <li class="btn btn-default"><a href="{{ route('companies.index') }}"><i class=" icon-arrow-left12 position-left"></i> Back </a></li>
                    <li class="active btn btn-default ml-20"><a href="#profiles" data-toggle="tab"><i class=" icon-profile position-left"></i> Company Profiles</a></li>
                    <li class="btn btn-default"><a href="#schedule" data-toggle="tab"><i class=" icon-inbox position-left"></i> Messages <span class="badge badge-success badge-inline position-right">32</span></a></li>
                </ul>
            </div>
        </div>
    </div>
   </div>

    <!-- User profile -->
    <div class="row">
        <div class="col-lg-9">
            <div class="tabbable">
                <div class="tab-content">


                        <div class="tab-pane fade in active" id="profiles">
                            <!-- Profile info -->
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title text-bold">COMPANY INFORMATIONS</h6>
                                    <div class="heading-elements">
                                        <span class="heading-text text-muted"><i class="icon-history position-left text-success "></i>Last Update: @foreach($companies as $data) {{ date('M d,Y h:i:s', strtotime($data->updated_at)) }} @endforeach</span>

                                        <ul class="icons-list">
                                            <li><a data-action="reload"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">

                                    @foreach ($companies as $item)
                                    <form  action="{{ route('companies.update',['company'=>$item->companyID]) }}" method="POST" class="stepy-clickable">
                                        @csrf
                                        @method('PATCH')
                                    @include('company.EditCompany')
                                    </form>
                                    @endforeach
                                </div>
                            </div>
                            <!-- /profile info -->
                        </div>

                        <div class="tab-pane fade " id="schedule">
                            <!-- Available hours -->
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h6 class="panel-title">Available hours</h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                            <li><a data-action="reload"></a></li>
                                            <li><a data-action="close"></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="panel-body">

                                        <!-- Messages -->
                                        <div class="timeline-row">
                                            <div class="timeline-icon">
                                                <div class="bg-info-400">
                                                    <i class="icon-comment-discussion"></i>
                                                </div>
                                            </div>

                                            <div class="panel panel-flat timeline-content">
                                                <div class="panel-heading">
                                                    <h6 class="panel-title"><b>Conversation with:</b> <i class=" text-muted">{{ $item->companyName ?? '' }}</i></h6>
                                                    <div class="heading-elements">
                                                        <ul class="icons-list">
                                                            <li class="dropdown">
                                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                                    <i class="icon-menu"></i>
                                                                </a>

                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                    <li><a href="#"><i class="icon-user-lock"></i> Hide user posts</a></li>
                                                                    <li><a href="#"><i class="icon-user-block"></i> Block user</a></li>
                                                                    <li><a href="#"><i class="icon-blocked"></i> Report this post</a></li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="panel-body">
                                                    <ul class="media-list chat-list content-group">
                                                        <li class="media date-step">
                                                            <span>Today</span>
                                                        </li>

                                                        <li class="media reversed">
                                                            <div class="media-body">
                                                                <div class="media-content">Naomba kujua, nawezaje kushiriki kwenye Judging ?</div>
                                                                <span class="media-annotation display-block mt-10">Tue, 8:12 am <a href="#"><i class="icon-pin-alt position-right text-muted"></i></a></span>
                                                            </div>

                                                            <div class="media-right">
                                                                <a href="assets/images/placeholder.jpg">
                                                                    <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle" alt="">
                                                                </a>
                                                            </div>
                                                        </li>

                                                        <li class="media">
                                                            <div class="media-left">
                                                                <a href="assets/images/placeholder.jpg">
                                                                    <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle" alt="">
                                                                </a>
                                                            </div>

                                                            <div class="media-body">
                                                                <div class="media-content">karibu M/S {{ $item->companyName }},
                                                                    Ili uweze kushiriki judging ni rahisi kabisa Bonyeza linki hii hapa <a href="https://www.tanzaniafair.com" target="_tab">https://www.tanzaniafair.com</a> upate maelezo na namna ya kupiga kura.</div>
                                                                <span class="media-annotation display-block mt-10">Wed, 4:20 pm <a href="#"><i class="icon-pin-alt position-right text-muted"></i></a></span>
                                                            </div>
                                                        </li>

                                                    </ul>

                                                    <textarea name="enter-message" class="form-control content-group" rows="3" cols="1" placeholder="Enter your message..."></textarea>

                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <ul class="icons-list icons-list-extended mt-10">
                                                                <li><a href="#" data-popup="tooltip" title="Send photo" data-container="body"><i class="icon-file-picture"></i></a></li>
                                                                <li><a href="#" data-popup="tooltip" title="Send video" data-container="body"><i class="icon-file-video"></i></a></li>
                                                                <li><a href="#" data-popup="tooltip" title="Send file" data-container="body"><i class="icon-file-plus"></i></a></li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-xs-6 text-right">
                                                            <button type="button" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i class="icon-circle-right2"></i></b> Send</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /messages -->



                                </div>
                            </div>
                            <!-- /available hours -->

                        </div>




                </div>
            </div>
        </div>


        @foreach ($companies as $value)
        <div class="col-lg-3">
            <!-- User thumbnail -->
            <div class="thumbnail">
                <div class="thumb thumb-rounded thumb-slide">
                    <img src="{{ asset('assets/images/placeholder.jpg') }}" alt="">
                    <div class="caption">
                        <span>
                            <a href="#" class="btn bg-success-400 btn-icon btn-xs" data-popup="lightbox"><i class="icon-plus2"></i></a>
                            <a href="#" class="btn bg-success-400 btn-icon btn-xs"><i class="icon-link"></i></a>
                        </span>
                    </div>
                </div>

                <div class="caption text-center">
                    <h6 class="text-semibold no-margin text-capitalize"> {{ $value->companyName }} <small class="display-block">{{ $item->sectorName }}</small></h6>
                    <ul class="icons-list mt-15">
                        <li><a href="#" data-popup="tooltip" title="Instagram"><i class=" icon-instagram"></i></a></li>
                        <li><a href="#" data-popup="tooltip" title="Twitter"><i class="icon-twitter"></i></a></li>
                        <li><a href="#" data-popup="tooltip" title="Facebook"><i class=" icon-facebook"></i></a></li>
                    </ul>
                </div>
            </div>
            <!-- /user thumbnail -->



            <!-- Navigation -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title text-bold text-uppercase"> associated Companies</h6>
                    <div class="heading-elements">
                        <a href="#" class="heading-text">Add New <i class=" icon-plus2"></i></a>
                    </div>
                </div>

                <div class=" panel panel-body">
                    <table class="table datatable-button-html5-columns table-bordered table-striped table-responsive table-lg text-nowrap">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Company Email</th>
                                <th>Company Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- /navigation -->

            @endforeach

        </div>
    </div>
    <!-- /user profile -->

</div>
<!-- /content area -->



</x-app-layout>
{{--  --}}
