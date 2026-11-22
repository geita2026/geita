<x-app-layout>
    <title>{{ env('APP_NAME') }} | ASSOCIATE COMPANIES</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">Manage All associated companys</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">
                <div class=" form-group">
                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source" value="2">
                        <input type="hidden" name="associate_company" value="{{ $company_id ?? '' }}">
                        @include('company.RegCompany')
                    </form>
                </div>

                <div class=" form-group col-md-12 table-responsive">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr style="background-color: rgb(223, 215, 215);">
                                <th colspan="7" class=" text-bold text-center text-uppercase">
                                    <h3 class=" text-bold">
                                        REGISTERED ASSOCIATE COMPANIES
                                        @isset($company_id)
                                            [ AVAILABLE BADGES: {{ session('allowedAllocation') }} ]
                                        @endisset</h3>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-bold text-center">#</th>
                                </th>
                                <th class="text-bold text-center">TSRN</th>
                                <th class="text-bold text-center">COMPANY NAME</th>
                                <th class="text-bold text-center">COMPANY PHONE</th>
                                <th class="text-bold text-center">COMPANY EMAIL</th>
                                <th class="text-bold text-center">ALLOCATED BADGES</th>
                                <th class="text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php$i=1;@endphp
                            @foreach ($associates as $key => $item)
                                <tr>
                                    <td class=" text-center">{{ $key + 1 }}</td>
                                    <td><a>{{ $item->registrationID }}</a>
                                       
                                    </td>
                                    <th><a>{{ $item->companyName }}</a>
                                    </th>
                                    <td>{{ $item->companyPhone }}</td>
                                    <td>{{ $item->companyEmail }}</td>
                                    <td class=" text-center">
                                        <a @if(isset($company_id) && session('allowedAllocation') > 0) data-toggle="modal" data-target="#allocateBadges{{ $item->companyID }}"@endif
                                            class=" btn btn-primary"><b>
                                                @foreach ($associateWithAllocations as $value)
                                                    @if ($value->companyID == $item->companyID)
                                                        {{ number_format($value->free_badges) }}
                                                    @endif
                                                @endforeach
                                            </b> <i class="icon icon-add"></i>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="{{ route('company_profile', ['companyId' => $item->registrationID]) }}"
                                                            class=" text-primary"><i class="icon-pencil7"></i> View
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                    <div id="allocateBadges{{ $item->companyID }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to Add Badge
                                                        Allocations to
                                                        this
                                                        company
                                                        <b><q>{{ $item->companyName }}</q></b> company ?
                                                    </h5>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <h5>Current Allocated:
                                                            @foreach ($associateWithAllocations as $value)
                                                                @if ($value->companyID == $item->companyID)
                                                                    {{ number_format($value->free_badges) }}
                                                                @endif
                                                            @endforeach
                                                        </h5>
                                                    </div>
                                                    <form
                                                        action="{{ route('associate_companies.update', ['associate_company' => $item->companyID]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class=" row">
                                                            <input type="hidden" name="parentCompany" value="{{ $company_id ?? '' }}">
                                                            <div class=" col-md-12 form-group">
                                                                <label>Allocated Badges<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="number" name="allocatedBadges"
                                                                    value="1" min="1" class=" form-control">
                                                            </div>
                                                            <div class="col-md-12 form-group text-right">
                                                                <button type="button" data-dismiss="modal"
                                                                    class=" btn btn-default">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-ladda btn-ladda-spinner ml-20"
                                                                    data-style="slide-down">Yes, Add Badges</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>



            </div>



        </div>
    </div>
    <!-- /dashboard content -->



</x-app-layout>

<script>
    $(document).ready(function() {
        var form = '#add_company';
        $(form).on('submit', function(event) {
            event.preventDefault();
            var url = $(this).attr('data-action');
            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $('#add_company').find('span.text-error').text('');
                },
                success: function(response) {
                    if (response.status == 0) {
                        $.each(response.error, function(prefix, val) {
                            $(form).find('span.' + prefix + '_error').text(val[0]);
                        });
                    }
                    if (response.status == 1) {
                        $(document).ready(function() {
                            new PNotify({
                                title: 'Congratulations!',
                                text: response.success,
                                icon: 'icon-info3',
                                type: 'success'
                            });
                        });
                        $('#company').modal('toggle');
                        document.location.reload(); // reload page
                    }

                    if (response.status == 2) {
                        $(document).ready(function() {
                            new PNotify({
                                title: 'Congratulations!',
                                text: response.fail,
                                icon: 'icon-info3',
                                type: 'danger'
                            });
                        });
                        $('#company').modal('toggle'); // reload page
                    }


                }
            });
        });
    });
</script>


<script>
    $("#country").change(function() {
        if ($(this).val() == "Tanzania") { //for functional Halls

            $("#region").show();
        } else {
            $("#region").hide();

        }
    });
</script>
