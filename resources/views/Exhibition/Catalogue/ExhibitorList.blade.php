<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} | DITF Catalogy</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    <style>
        .exhibitorRow {
            border: 1px solid rgb(61, 178, 199);
        }

        .exhibitorRow:hover {
            background-color: rgb(53, 124, 206);
            cursor: grab;
        }

        .sectorBtn:hover {
            border: 1px solid blue;
            background-color: blue;
        }

        .myButton:hover {
            background-color: rgb(247, 252, 0);
            color: black;

        }
    </style>
    @include('WelcomePageLayouts.welcomeCss')
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>

<body>

    <!-- Page container -->
    <div class="page-container">
        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">

                <!-- HEADER MENU-->
                @include('WelcomePageLayouts.welcomeHeaderMenu')
                <!-- END HEADER MENU-->

                <div class="panel panel-white table-responsive">
                    <div class=" panel-body">

                        <div class=" col-md-12 form-group">
                            
                            
                            <div class=" col-md-12">
                                <h3>
                                    More than <b>35 countries</b>, <b>3,500 companies</b> and <b>400,000+ Visitors</b>
                                    at one point, Enjoy immersive cultural experiences and discover what makes each
                                    country unique as
                                    you explore hundreds of pavilions.
                                </h3>
                                <h3>
                                  Filter {{ $event->event_name ?? '' }} exhibitor sectors to view

                                </h3>

                                <div class="table-responsive">    
                                    <form action="{{ route('discover_exhibitor.index') }}" method="POST">
                                        @csrf
                                        @method('GET')
                                        <div class="col-lg-10">
											<div class="input-group">
                                                <select name="sectorId" class=" select" multiple="multiple" placeholder="select " required>
                                                    @foreach ($sectors as $sector)
                                                    <option value="{{ $sector->sectorID }}">{{ $sector->sectorName }}</option>
                                                    @endforeach
                                                </select>
												<span class="input-group-btn">
													<button class="btn bg-teal" type="submit"><i class=" icon-search4"></i> Search Exhibitor</button>
												</span>
											</div>
										</div>
                                    </form>

                                </div>

                            </div>
                        </div>

                        <div class=" form-group col-md-12  ">
                            <h3 style="font-size:2.5vmin">Sector: <b
                                    style="text-transform: uppercase">{{ $sectorName }}.</b></h3>
                        </div>

                        @php
                            $j = 0;
                        @endphp
                        @foreach ($exhibitors as $item)
                            <div class=" col-md-4 form-group">
                                <ul class="media-list media-list-linked ">
                                    <li class="media zoom exhibitorRow">

                                        <a href="{{ route('discover_exhibitor.show', ['discover_exhibitor' => $item->exhibitor_id]) }}"
                                            class="media-link cursor-pointer">
                                            <div class="media-left">
                                                @if ($item->companyLogo)
                                                    <img src="{{ asset('profile_images/' . $item->companyLogo) }}"
                                                        class="img-circle" alt="">
                                                @else
                                                    <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                        class="img-circle" alt="">
                                                @endif

                                            </div>
                                            <div class="media-body text-primary">
                                                <div class="media-heading text-bold text-uppercase"
                                                    style="font-size:2vmin">{{ $item->companyName }}</div>
                                                <span class="text-muted">{{ $item->sectorName }}</span>
                                            </div>
                                            <div class="media-right media-middle text-nowrap">
                                                <i class=" icon-arrow-right13 display-block"></i>
                                            </div>
                                        </a>


                                    </li>
                                </ul>
                            </div>
                            @php
                                $j++;
                            @endphp
                        @endforeach

                        @if ($j == 0)
                            <div class=" alert alert-danger col-md-12 form-group">
                                <h3>No exhibitor under <b>{{ $sectorName }}</b> sector category! Please select other
                                    category above</h3>
                            </div>
                        @endif
                    </div>



                    {{-- *********** CATALOGUE SPONSORS ************* --}}
                    <div class=" form-group p-20 col-md-12">
                        <div>
                            <b style="font-size:1.5vmax">SPONSORED BY,</b>
                            <div class=" text-center">
                                @foreach ($sponsors as $value)
                                    <a class=" form-group col-md-2" href="{{ $value->sponsor_website }}"
                                        target="_tab">
                                        <div class=" zoom">
                                            <img src="{{ asset('VirtualImages/' . $value->sponsor_logo) }}"
                                                height="70" width="80"><br>
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
        </div>

        @include('layouts.footer')
    </div>


</body>

</html>

@include('FrontEnd.Public_Pages.Layouts.scripts')
<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_select2.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>

<script>
    /* ------------------------------------------------------------------------------
     *
     *  # Basic datatables
     *
     *  Specific JS code additions for datatable_basic.html page
     *
     *  Version: 1.0
     *  Latest update: Aug 1, 2015
     *
     * ---------------------------------------------------------------------------- */

    $(function() {


        // Table setup
        // ------------------------------

        // Setting datatable defaults
        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [0]
            }],
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {
                    'first': 'First',
                    'last': 'Last',
                    'next': '&rarr;',
                    'previous': '&larr;'
                }
            },
            drawCallback: function() {
                $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
            },
            preDrawCallback: function() {
                $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass(
                    'dropup');
            }
        });


        // Basic datatable
        $('.datatable-basic').DataTable();


        // Alternative pagination
        $('.datatable-pagination').DataTable({
            pagingType: "simple",
            language: {
                paginate: {
                    'next': 'Next &rarr;',
                    'previous': '&larr; Prev'
                }
            }
        });


        // Datatable with saving state
        $('.datatable-save-state').DataTable({
            stateSave: true
        });


        // Scrollable datatable
        $('.datatable-scroll-y').DataTable({
            autoWidth: true,
            scrollY: 300
        });



        // External table additions
        // ------------------------------

        // Add placeholder to the datatable filter option
        $('.dataTables_filter input[type=search]').attr('placeholder', 'Type to filter...');


        // Enable Select2 select for the length option
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });

    });
</script>

