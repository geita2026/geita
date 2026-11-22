<x-guest-layout>
    <title>{{ env('APP_NAME') }} | User Manual</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-info22 position-left text-bold"></i>USER GUIDE</a></li>
                <li class="active"> View user guide</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body table-responsive">
               
                {{-- report points --}}
                <div class=" table-responsive">
                    <h1 class=" text-bold text-center text-primary" id="h1"><b>THE SPACE ALOCATION MANAGEMENT
                            SYSTEM Version 1.02 USER MANUAL. </b></h1>
                    <div class=" form-group col-md-12">
                        <a href="{{ route('index') }}" class=" btn btn-default text-primary border-primary"> <i
                                class=" icon-arrow-left12"></i> Back To Home</a>
                    </div>
                    <ul>
                        <p>
                            <b class=" text-pink">USER REGISTRATION AND AUTHENTICATION.</b><br>
                            User will be able to register his or her own by clicking the button named <a
                                href="{{ route('registration') }}">Register <i class=" icon-user-plus"></i></a>
                            and he or she must have <b>Service Registration Number (SRN) to register to the
                                system <i class=" text-primary icon-question3" data-popup="popover"
                                    title="Service Registration Number" data-trigger="hover"
                                    data-content="The number issued to your company when it first registered for an exhibition service."></i></b>
                            This number appears on every invoice at the <b>Invoice To</b> Informations. or
                            call <a href="tel:255 733-002-014">255 733-002-014</a>
                            for help.
                        </p>
                        <b>If you have TSRN, then follow the following proceedure to register!</b>

                        <li>Click register button, Enter your TSRN Then click next</li>
                        <li>If TSRN entered is correct you will be redirected to enter your person information, fill it
                            correctly then click <b>create Account</b></li>
                        <li>After successfully registration <a href="{{ route('index') }}">Go to Login</a>, username
                            will be your Email and Password is your password you created. </li>
                            <li>Enter your username and Password then click Login.</li>
                    </ul>

                    <ul>
                        <b class=" text-pink">HOW TO GET EXHIBITION BADGES ?</b><br>
                        <li>To submit your Exhibition Badges you must be paid all balances </li>
                        <li>Select <b>Exhibition Badges</b> Menu then click <b>Manage Badges</b></li>
                        <li>Click <b>Previous member</b> to reuse existing information and remove unneccessary previous badge information.</li>
                        <li>Click <b>New Member Badge</b> to add new badge informations.</li>
                        <li>After submiting your all badge information visit the Exhibition Secretariat office to collect your badge ready for {{ date('Y') }} - Exhibition.</li>
                    </ul>

                    
                   
                </div>
                {{-- //end report point --}}
            </div>
        </div>
    </div>
    <!-- /content area -->
    </div>
    <!-- /main content -->
</x-guest-layout>
