<x-app-layout>
    <title>{{ env('APP_NAME') }} | Site Visitors</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> PERMISSIONS</a>
                </li>
                <li class="active">View All Permissions</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body table-responsive">


                <h2 class=" text-center"> SITE VISITORS COUNT</h2>
                <div class="category-content">
                    <div class="row row-condensed">
                        <a>
                            <div class="col-md-2 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($totalVisitors) }}</i>
                                    <span>Total Visitors</span>
                                </button>
                            </div>
                        </a>

                        <a>
                            <div class="col-md-2 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($today) }}</i>
                                    <span>Today</span>
                                </button>
                            </div>
                        </a>
                        <a>
                            <div class="col-md-2 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($yesterday) }}</i>
                                    <span>Yesterday</span>
                                </button>
                            </div>
                        </a>
                        <a>
                            <div class="col-md-2 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($thisWeek) }}</i>
                                    <span>This Week</span>
                                </button>
                            </div>
                        </a>
                        <a>
                            <div class="col-md-2 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($thisMonth) }}</i>
                                    <span>This Month</span>
                                </button>
                            </div>
                        </a>
                        <a>
                            <div class="col-md-2 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($thisYear) }}</i>
                                    <span>This Year</span>
                                </button>
                            </div>
                        </a>

                    </div>
                </div>



                {{-- serach data --}}
                <form action="{{ route('vistors.index') }}" method="POST">
                    @method('GET')
                    <div class=" row col-md-12">
                        <div class=" col-md-3 form-group">
                            <label>From Date<i class=" text-danger">*</i></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                <input type="text" name="from_date" class="form-control daterange-single"
                                    value="01/01/2023">
                            </div>
                        </div>
                        <div class=" col-md-3 form-group">
                            <label>To Date<i class=" text-danger">*</i></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                <input type="text" name="to_date" class="form-control daterange-single"
                                    value="{{ date('d-m-Y') }}">
                            </div>
                        </div>
                        <div class=" col-md-3 form-group">
                            <label>Data Limit<i class=" text-danger">*</i></label>
                            <select name="limit" class=" form-control">
                                <option value="50">20</option>
                                <option value="50">50</option>
                                <option value="75">75</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                        </div>
                        <div class=" col-md-1 form-group text-right">
                            <button class=" btn btn-default text-primary mt-20">Load Data</button>
                        </div>
                    </div>
                </form>
                {{-- end --}}
               

                <table data-table-paginated class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-center text-bold">#</th>
                            <th class=" text-center text-bold">VISTOR IP ADDRESS</th>
                            <th class=" text-center text-bold">Country</th>
                            <th class=" text-center text-bold">Region</th>
                            <th class=" text-center text-bold">PAGE VISTED</th>
                            <th class=" text-center text-bold">DATE OF VISTATIONS</th>
                            <th class=" text-center text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($vistors as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td class=" text-success">{{ $item->vistor_ip_address }}</td>
                                <td>{{ $item->country }}</td>
                                <td>{{ $item->region }}</td>
                                <td><a href="{{ $item->page_visted }}" target="_tab">page visited
                                        <i class=" icon-link"></i></a></td>
                                <td>{{ date('d-m-Y h:i:s', strtotime($item->visted_on)) }}</td>
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="" data-toggle="modal"
                                                        data-target="#delete_permission{{ $item->vistor_id }}"
                                                        class="text-danger"> Delete Permission <i
                                                            class=" icon-bin"></i></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>

                                {{-- delete modal --}}
                                <div id="delete_permission{{ $item->vistor_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to
                                                    delete this data ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('permissions.destroy', ['permission' => $item->vistor_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-danger ml-20">Yes,
                                                            Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
                <div class=" text-right">
                    {{ $vistors->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
<script type="text/javascript" src="assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/picker_date.js') }}"></script>
