<x-app-layout>
    <title>{{ env('APP_NAME') }} | VirtualPavilion</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-home position-left"></i> Home
                    </a></li>
                <li class="active">Confirm Participation</li>
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
                <div class=" form-group row">
                    @foreach ($exhibitors as $down)
                    @if ($down->exhibitedProducts != null && $down->exhibitionSpace != null)
                        <div class="col-md-12 text-right">
                            <a href="{{ route('participation_form', ['participationId' => $down->participation_id]) }}"
                                class=" btn btn-primary mr-20" target="_newtab">Download Form <i class=" icon icon-download4"></i></a> ||
                                <a href="{{ route('upload_form', ['participationId' => $down->participation_id]) }}"
                                    class=" btn btn-primary ml-20">Upload Signed Form <i class=" icon icon-upload4"></i></a>
                        </div>

                        
                    @endif
                @endforeach
                </div>
                <div class=" form-group">
                    <form action="{{ route('confirm_participation') }}" method="POST">
                        @csrf
                        @method('GET')
                        <label>Select your Company or Insititution<i class=" text-danger">*</i></label>
                        <select id="search-select" name="companyId" class=" form-control"
                        onchange="this.form.submit()">
                   </select>
                       

                    </form>
                </div>

                @php $i = 0; @endphp
                @foreach ($exhibitors as $item)
                    <form action="{{ route('confirm_participation') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('GET')
                        <div class=" form-group text-primary">
                            <b>Selected Exhibitor: </b>{{ $item->companyName }}
                        </div>
                        <input type="hidden" name="source" value="1">
                        <div class=" col-md-12 row form-group">
                            <input type="hidden" name="companyId" value="{{ $item->companyID }}">
                            <div class="form-group">
                                <label class="display-block text-semibold">Do you expect to make sales during the
                                    exhibition ?<i class=" text-danger">*</i></label>
                                <label class="radio-inline radio-right">
                                    <input type="radio" name="makeSales" value="Yes" class="styled"
                                        @if ($item->makeSales == 'Yes') checked @endif>
                                    YES
                                </label>

                                <label class="radio-inline radio-right">
                                    <input type="radio" name="makeSales" value="No" class="styled"
                                        @if ($item->makeSales == 'No') checked @endif>
                                    NO
                                </label>
                            </div>

                            <div class=" form-group">
                                <label>What are the products or service you expect to exhibits<i
                                        class=" text-danger">*</i></label>
                                <textarea name="exhibitedProducts" class=" form-control" placeholder=" Product or Servces to be exhibited" required>{{ $item->exhibitedProducts }}</textarea>
                                <span class=" text-muted">
                                    Create a list of the product categories you will showcase along with the list of
                                    products in each respective category; (for example: 1. Agricultural products ….
                                    Fertilizer, 2. Electrical products…..Electrical equipment,).
                                </span>
                            </div>

                            <div class=" form-group">
                                <label>Your Selected Exhibition Spaces or Area Name<i class="text-danger">*</i></label>
                                <textarea name="exhibitionSpace" class=" form-control" placeholder="Exhibition spaces" readonly required>{{ $item->exhibitionSpace }}</textarea>
                            </div>
                            <div class=" form-group text-right">
                                <button class=" btn btn-primary">Submit <i class="icon icon-arrow-right13"></i></button>
                            </div>
                        </div>
                    </form>
                    @php $i++; @endphp
                @endforeach

                @if ($i == 0)
                    <div class=" alert alert-info">The selected company does not exist in exhibitor list Please, Select your company above to fill out the form</div>
                @endif


            </div>
        </div>
    </div>

</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<!-- /theme JS files -->
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
                                text: item.registrationID+' || '+item.companyName+' || '+ item.companyPhone+' || '+ item.companyEmail,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search your company by Name or TSRN ',
            minimumInputLength: 0,
        });
    });
</script>