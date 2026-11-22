<x-app-layout>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>
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

     <div class="form-group">
         @if (Session::get('success'))
         <div class=" alert alert-success alert-bordered alert-rounded">
             <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
             <span class="text-semibold">Congraduration!</span> {{ session::get('success') }}
         </div>
         @elseif (Session::get('fail'))
         <div class=" alert alert-danger alert-bordered">
             <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
             <span class="text-semibold">Oops!</span> {{ session::get('fail') }}
         </div>
         @endif
      </div>
    {{-- report points --}}

         <div class=" alert-success p-20" style="font-size:15px">

            <ul>
                <li>The bollow, are invoices which you have access to retrieve, click to view them in details.</li>
            </ul>
        </div>
        <div class=" row">
             {{--  boked vs Unbooked  --}}
             @can('VIEW_ALL_INVOICE')
             <a href="#">
                 <div class="form-group col-xs-4 mt-20">
                     <button class=" btn bg-purple btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view list of invoices">
                         <h6 class=" text-uppercase text-bold">LIST OF ALL INVOICES<i class=" icon-list-numbered"></i></h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan
              {{--  //end  --}}

               {{--  My space  --}}
             @can('VIEW_PAID_INVOICE')
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
             @can('VIEW_PENDING_INVOICE')
             <a href="#">
                 <div class="form-group col-md-4 mt-20 shadow">
                     <button class=" btn bg-warning-800 btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view Exhibitor">
                         <h6 class=" text-uppercase text-bold">PENDING INVOICES<i class=" icon-files-empty2"><b class="ml-20">9</b></i> </h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan

             @can('VIEW_EXPIRED_INVOICE')
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
 <script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/charts/echarts/columns_waterfalls.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/charts/echarts/timeline_option.js') }}"></script>
 <!-- /theme JS files -->
