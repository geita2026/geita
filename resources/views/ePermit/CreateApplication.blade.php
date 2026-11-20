<x-app-layout>
    <title>{{ config('global.MODULE_TWO') }} | Company Profile</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permitDashboard') }}"><i class="icon-home2 position-left"></i> ePermit</a></li>
                <li class="active">Dashboard</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <div class="content">
        <!-- Basic modals -->
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h4 class="panel-title text-bold"> ePermit Application</h4>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">

                {{-- add role modal --}}
                <form action="{{ route('apply_permit.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="">
                            <div class=" form-group col-md-12">
                                <label>Select Company Name<i class=" text-danger">*</i> || If your Company is not in the List  <a href="{{ route('company_profile',['companyId'=>date('dmsi')]) }}">Click here to Register<i class=" icon icon-plus22"></i></a></label>
                                <select id="search-select" name="companyId" class=" form-control" required>
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>What type of Promotion Event you are requesting permit ?<i
                                        class=" text-danger">*</i></label>
                                <select name="typeOfPromotionEvent" class=" form-control" required>
                                    <option value=""> - - - Select Promotion Event - - -</option>
                                    @foreach ($proEvents as $value)
                                        <option value="{{ $value->eventTypeId }}">{{ $value->promotionEventTypeName }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>

                            <div class=" form-group col-md-12">
                                <label>Trade Promotion Audience<i class=" text-danger">*</i></label>
                                <select name="typeOfApplication" class=" form-control" required>
                                    <option value=""> - - - Select Promotion Audience - - -</option>
                                    <option value="1">LOCAL PARTICIPANTS ONLY</option>
                                    <option value="2">FOREIGN PARTICIPANTS ONLY</option>
                                    <option value="3">INTERNATIONAL PARTICIPANTS</option>
                                </select>
                                <span class=" text-danger text-error eventType"></span>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Save and Continue <i
                                    class=" icon-arrow-right13"></i></span></button>
                    </div>

                </form>
                {{--  end  --}}

            </div>
        </div>
        <!-- /basic modals -->

    </div>
</x-app-layout>
<script>
    const checkbox = document.getElementById('TSRN');
    const toggleDiv = document.getElementById('TSRNFIELD');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            toggleDiv.style.display = 'block'; // Show the div
        } else {
            toggleDiv.style.display = 'none'; // Hide the div
        }
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
<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

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
                                text: item.registrationID + ' || ' + item.companyName
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Companies (write company name or Tantrade Service Registration Number) . . .',
            minimumInputLength: 0,
        });
    });
</script>
