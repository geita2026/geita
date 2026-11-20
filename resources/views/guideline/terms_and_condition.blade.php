<x-guest-layout>
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body">

               <div class=" container">
                <div class=" row table-responsive">
                    <div class=" col-md-12 text-right form-group">
                        <a href="{{ url()->previous() }}" class=" btn btn-primary"> <i class=" icon icon-arrow-left12"></i> Go Back</a>
                    </div>
                    <div class=" col-md-12 alert alert-info form-group">
                        <p> <i class=" icon icon-info22"></i> You are required to read , understand and accept the terms of participation for the Dar es salaam International Trade Fair in order to proceed with booking. </p>
                    </div>

                    <div class=" col-md-12 ">
                        <iframe src="{{ asset('images/TERMS_OF_PARTICIPATION.pdf') }}" width="1000" height="600" loading="lazy" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
               </div>
            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-guest-layout>
