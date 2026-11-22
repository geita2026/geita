<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permit Processing</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i> HOME</a></li>
                <li class="active">View Permits</li>
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
            <div class=" panel-body ">

                <div class=" form-group">
                    <h2 class=" text-center">EXHIBITION ENTRANCE & EXIT PERMITS</h2>
                </div>

                {{-- FOR STAPH --}}

                <div class=" col-md-12 p-10">
                    <p class=" text-justify">Permits for importing and exporting goods at an exhibition grounds are
                        official documents issued by <b>TanTrade</b>  to allow goods to be brought into and
                        taken out of the exhibition area. These permits mean that the process of entering and leaving
                        the goods has been approved and complies with the established rules and procedures. They are
                        important for security, tracking the goods, and ensuring that activities are carried out
                        efficiently and in an orderly manner.</p>
                </div>

                <div class=" col-md-12 form-group">
                    <form action="{{ route('iopermits.store') }}" method="POST">
                        @csrf
                        <div class=" form-group col-md-6">
                            <select id="search-select" name="company_id" class=" form-control" required>
                            </select>
                            <span class="text-danger"> @error('role_name')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>


                        <div class="col-md-3">
                            <div class="checkbox" style="font-size:2vmin">
                                <label>
                                    <input type="radio" name="permitType" value="ENTRANCE_PERMIT" class="styled">
                                    Entrance Permit
                                </label>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="radio" style="font-size:2vmin">
                                <label>
                                    <input type="radio" name="permitType" class="styled" value="EXIT_PERMIT">
                                    Exit Permit
                                </label>
                            </div>

                        </div>
                        <span class=" text-danger">
                            @error('permitType')
                                {{ $message }}
                            @enderror
                        </span>

                        <div class=" form-group col-md-12">
                            <div class="form-group">
                                <label class="display-block">List of <b>goods</b>  that needs permit (Eg. 6 TV, 3 Refrigerators)
                                    <i class=" text-danger">*</i></label>
                                <textarea class=" form-control" name="exhibitedProducts" placeholder="Eg Logistics Services,Tractors, clothes .. .etc"
                                    required></textarea>
                            </div>
                        </div>
                        <div class=" col-md-12 form-group">
                            <p class=" text-danger"><b>NOTE!</b> Only the products or services you have specified will
                                be allowed to enter or leave the exhibition grounds.</p>
                        </div>
                        <div class=" col-md-12 form-group text-right">
                            <button type="submit" class=" btn btn-primary btn-ladda btn-ladda-spinner">Request
                                Permit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
<script>
    $(document).ready(function() {
        $('#search-select').select2({
            ajax: {
                url: '{{ route('search_companies') }}', // URL for the AJAX request
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
                                id: item.companyID,
                                text: item.registrationID + ' || ' + item.companyName +
                                    ' || ' + item.companyPhone + ' || ' + item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies (write company name or Service Registration Number) . . .',
            minimumInputLength: 0,
        });
    });
</script>
