<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship Booking</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-server position-left"></i> Home</a></li>
                <li class="active"> Book Sponsorship Packages </li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body">


                <div class="category-content">
                    <div class="row row-condensed col-md-12">
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($packageGroups as $item)
                            {{-- Booking --}}
                            <div class="col-md-4 form-group zoom" >
                                <a
                                    href="{{ route('book-packages.edit', ['book_package' => $item->packageGroupId]) }}" >
                                    <button type="submit"
                                        class="btn btn-block btn-float btn-float-lg  text-bold border-primary" style="background-color: {{ $item->backgroundColor }};color:{{ $item->textColor }}">
                                        <i class=" icon-coin-dollar"></i> <span
                                            class=" text-uppercase">{{ $item->packageGroupName }}</span>
                                        <hr>
                                        <div style="text-align: left; font-size:90%">
                                            <p class=" text-capitalize">
                                                {{ Str::limit($item->packageGroupDescription, 200, '...') }}</p>
                                        </div>
                                    </button>
                                </a>
                            </div>
                            {{-- /Booking --}}
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </div>
                </div>



                @php $i=0; @endphp
                @foreach ($booked_package as $data)
                    @php $i++; @endphp
                @endforeach


            </div>
        </div>
    </div>
    <!-- /content area -->
    </div>
    <!-- /main content -->
</x-app-layout>

{{--  --}}

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
            placeholder: 'Search Companies by company name or Tantrade Service Registration Number',
            minimumInputLength: 0,
        });
    });
</script>
