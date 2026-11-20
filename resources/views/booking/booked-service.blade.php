<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
           @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Dashboard</li>
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
         <div class=" alert-success p-20" style="font-size:12px">
            <ul>
                <li>The bollow, are invoices which you have access to retrieve, click to view them in details.</li>
            </ul>
        </div>
        <div class=" row">
             {{--  boked vs Unbooked  --}}
             @can('view All Invoice')
             <a href="{{ route('book-services.create') }}">
                 <div class="form-group col-xs-4 mt-20">
                     <button class=" btn bg-purple btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to book service">
                         <h6 class=" text-uppercase text-bold">Book Service<i class=" icon-bookmark4"></i></h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan
              {{--  //end  --}}


              {{--  My space  --}}
             @can('View Paid Invoice')
             <a href="#">
                 <div class="form-group col-md-4 mt-20">
                     <button class=" btn bg-success btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view Your space">
                         <h6 class=" text-uppercase text-bold">PAID INVOICES <i class=" icon-credit-card"><b class="ml-20">9</b></i> </h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan
              {{--  //end  --}}


              {{--  Exhibitors  --}}
             @can('View Pending Invoice')
             <a href="#">
                 <div class="form-group col-md-4 mt-20 shadow">
                     <button class=" btn bg-warning-800 btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view Exhibitor">
                         <h6 class=" text-uppercase text-bold">PENDING INVOICES<i class=" icon-files-empty2"><b class="ml-20">9</b></i> </h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan

             @can('View Expired Invoice')
             <a href="#">
                 <div class="form-group col-md-4 mt-20">
                     <button class=" btn bg-grey-300 btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view Your space">
                         <h6 class=" text-uppercase text-bold">EXPIRED INVOICES <i class=" icon-stack-cancel"><b class="ml-20">9</b></i> </h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan

        </div>
 </div>
 </div>
 </div>
 <!-- /content area -->
 </div>
 <!-- /main content -->
 </x-app-layout>
 <!-- Theme JS files -->
