<x-app-layout>
    <!-- dashboard -->
    <title>{{ env('APP_NAME') }} | Catalogue</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('users.index') }}"><i class=" icon-users2 position-left"></i>EXHIBITOR CATALOGUE</a>
                </li>
                <li class="active">View exhibitor catalogue</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class=" panel">
            <div class=" panel-body">

                <div class=" form-group col-md-12">
                    <a href="{{ route('catalogy.index') }}" class=" text-bold"><i class=" icon-arrow-left12"></i> Back
                    </a>
                </div>

                <div class=" col-md-12">
                    <form action="{{ route('catalogy.show', ['catalogy' => Auth::user()->user_uuid]) }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" row">
                            <div class="col-xs-8 form-group" id="usersType">
                                <select name="event" class="bootstrap-select" data-live-search="true"
                                    data-width="100%">
                                    <option value=""> - - - Filter catalogy by events - - - </option>
                                    @foreach ($events as $item)
                                        <option value="{{ $item->event_id }}" class=" text-capitalize">
                                            {{ $item->event_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger"> @error('event')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-xs-4 form-group text-right">
                                <button class=" btn btn-default text-primary btn-ladda btn-ladda-spinner">Load Catalogy
                                    <i class=" icon-database-refresh"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="panel panel-white table-responsive">
            <div class=" panel-body">
                <div class=" col-md-12 row form-group">
                    <div class=" col-md-12 form-group text-right">
                        <a href="#" class=" btn btn-default border-primary text-primary">Export Catalogy <i
                                class=" icon-file-download"></i></a>
                    </div>
                </div>

                <div class=" col-md-12 form-group table-header">
                    @php $count=0; @endphp
                    @foreach ($exhibitors as $event)
                        @php $count++; @endphp
                    @endforeach
                    <h3 class=" text-center text-bold text-primary text-uppercase">{{ $event->event_name ?? '' }},
                        EXHIBITOR CATALOGUE</h3>
                </div>

                <!-- Horizontal icons -->
                <h6 class="content-group text-bold text-uppercase">
                    LEADERS'S WORD TO {{ $event->event_name }}.
                    <small class="display-block">{{ date('M d, Y') }}.</small>
                </h6>

                <div class="row">
                    @php $i=0; @endphp
                    @foreach ($catalogueSettings as $leader)
                        @if ($i == 0)
                            <div class="col-lg-12 col-md-6 zoom">
                                <div style="border:5px solid blue;">
                                    <div style="border:5px solid rgb(251, 255, 0);">
                                        <div style="border:5px solid rgb(0, 0, 0);">
                                            <div style="border:5px solid rgb(34, 207, 11);">
                                                <div class="panel panel-body">

                                                    <div class="media">
                                                        <div class="media-left">
                                                            <a href="{{ asset('CatalogueImages/' . $leader->leader_image) }}"
                                                                data-popup="lightbox">
                                                                <img src="{{ asset('CatalogueImages/' . $leader->leader_image) }}"
                                                                    style="width: 70px; height: 70px;"
                                                                    class="img-circle" alt="">
                                                            </a>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="media-heading text-bold">
                                                                {{ $leader->leader_name }}</h6>
                                                            <p class="text-muted text-primary"
                                                                style="font-size:1.5vmin">
                                                                {{ $leader->leader_title }}
                                                            </p>

                                                            <ul class="icons-list">
                                                                <li class=" text-justify">
                                                                    {{ $leader->appriciation_word }}</li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-6 col-md-6 zoom">
                                <div class="panel panel-body">
                                    <div class="media">
                                        <div class="media-left">
                                            <a href="{{ asset('assets/images/placeholder.jpg') }}"
                                                data-popup="lightbox">
                                                <img src="{{ asset('CatalogueImages/' . $leader->leader_image) }}"
                                                    style="width: 70px; height: 70px;" class="img-circle"
                                                    alt="">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h6 class="media-heading text-bold">{{ $leader->leader_name }}</h6>
                                            <p class="text-muted text-primary" style="font-size:1.5vmin">
                                                {{ $leader->leader_title }}</p>

                                            <ul class="icons-list">
                                                <li class=" text-justify">{{ $leader->appriciation_word }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <!-- /horizontal icons -->


                <div class=" form-group col-md-12" style="border:1px solid rgb(219, 205, 205);">
                    <h1 class=" text-center text-primary text-bold" style="font-size: 2.5vmin">48<sup>th</sup> DAR ES
                        SALAAM INTERNATIONAL TRADE FAIR - 2024 EXHIBITOR CATALOGUE.</h1>
                    <h3>TOTAL EXHIBITORS: <b class=" text-primary">{{ $count }}.</b></h3>
                    {{-- <h3>EVENT DATE: <b class=" text-primary">{{ date('M d,',strtotime($event->event_start_date ?? date('M-d'))) }} TO {{ date('M d, Y', strtotime($event->event_end_date)) }}.</b></h3> --}}
                </div>

                @foreach ($exhibitors as $item)
                    <div class=" col-md-12 form-group zoom"
                        style="border: 1px solid rgb(184, 181, 181); font-size:1.8vmin">
                        <div class=" p-5 col-md-4">
                            EXHIBITOR NAME: <b class=" text-primary text-uppercase">{{ $item->companyName }}</b>
                        </div>
                        <div class=" p-5 col-md-4">
                            EXHIBITOR EMAIL: <b class=" text-primary">{{ $item->companyEmail }}</b>
                        </div>
                        <div class=" p-5 col-md-4">
                            EXHIBITOR PHONE: <b class=" text-primary text-uppercase">{{ $item->companyPhone }}</b>
                        </div>
                        <div class=" p-5 col-md-4">
                            EXHIBITOR CATEGORY: <b class=" text-primary text-uppercase">
                                @if ($item->companyCategory == 1)
                                    Government Institution
                                @elseif ($item->companyCategory == 2)
                                    Private Companies
                                @elseif ($item->companyCategory == 3)
                                    SMEs
                                @else
                                    {{ $item->companyCategory }}
                                @endif
                            </b>
                        </div>
                        <div class=" p-5 col-md-4">
                            EXHIBITOR SECTOR: <b class=" text-primary text-uppercase">{{ $item->sectorName }}</b>
                        </div>
                        <div class=" p-5 col-md-4">
                            EXHIBITOR WEBSITE: <b class=" text-primary"><a href="{{ $item->website }}"
                                    target="new_tab">{{ $item->website }}</a></b>
                        </div>
                    </div>
                @endforeach
                <div class=" col-md-12 p-10 text-right">
                    {{ $exhibitors->links() }}
                </div>
            </div>



            {{-- *********** CATALOGUE SPONSORS ************* --}}
            <div class=" form-group p-20 col-md-12">
                <div>
                    <b style="font-size:1.5vmax">SPONSORED BY,</b>
                    <div class=" text-center">
                        @foreach ($sponsors as $value)
                            <a class=" form-group col-md-2" href="{{ $value->sponsor_website }}" target="_tab">
                                <div class=" zoom">
                                    <img src="{{ asset('VirtualImages/' . $value->sponsor_logo) }}" height="70"
                                        width="80"><br>
                                    <b style="font-size:1vmax">{{ $value->sponsor_name }}</b>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- *************** END CATALOGUE SPONSOR *********** --}}

        </div>


    </div>
</x-app-layout>
