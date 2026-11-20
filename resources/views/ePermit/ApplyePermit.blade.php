<x-app-layout>
    <title>{{ config('global.MODULE_TWO') }} | Application</title>
    <style>
        .checkBox {
            width: 20px;
            height: 20px;
            transform: scale(1.5);
            border: 2px solid #000;
            border-radius: 5px;
        }
    </style>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> ePermit</a></li>
                <li class="active">Application</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    @php
        $i = 0;
    @endphp
    @foreach ($application as $item)
        <!-- /page header -->
        <div class="content">

            <!-- Basic modals -->
            <div class="panel panel-flat">
                <div class="panel-heading">

                    <h4 class=" text-uppercase text-monospace text-info">
                        <b>Application for</b> <b>
                            <q>{{ $item->eventTitle }}</q>
                            Permit</b>
                    </h4>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">
                    <div class=" form-group">

                        @php
                            $percentComplete=(7-$submitForReview->nullValues ?? 0)/7*100;
                        @endphp
                        <div class="progress">
                            <div class="progress-bar bg-teal" style="width: {{ $percentComplete }}%">
                                <span>{{ number_format($percentComplete) }} % Complete</span>
                            </div>
                        </div>
                    </div>

                    <div class=" form-group">
                        <div class="panel panel-flat">
                            <div class="panel-body">
                                <div class="tabbable">
                                    @include('ePermit.applyPermitCommons.Review&Approve')
                                    @include('ePermit.applyPermitCommons.StagesHeader')
                                    <div class="tab-content">
                                        @include('ePermit.applyPermitCommons.stageOne')
                                        @include('ePermit.applyPermitCommons.stageTwo')
                                        @include('ePermit.applyPermitCommons.StageThree')
                                        @include('ePermit.applyPermitCommons.StageFour')

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- /basic modals -->
        </div>
        @php
            $i++;
        @endphp
    @endforeach
</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>
<script type="{{ asset('text/javascript" src="assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_select2.js') }}"></script>

<script>
    function checkNumber() {
        const numberInput = document.getElementById('numberInput').value;
        const extraInputDiv = document.getElementById('extraInput');

        if (numberInput > 0) {
            extraInputDiv.style.display = 'block';
        } else {
            extraInputDiv.style.display = 'none';
        }
    }
</script>
