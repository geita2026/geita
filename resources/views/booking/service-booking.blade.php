<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('book-services.index') }}"><i class="icon-home2 position-left"></i> Service Booking</a></li>
            <li class="active"> Service booking.</li>
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
        <div class="panel-heading">
            <h6 class="panel-title text-semibold">
                <a href="{{ route('booking.index') }}" class=" btn btn-default"><i class=" icon-arrow-left12"></i> Back</a>
                </h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
 <div class=" panel-body table-responsive">

    {{-- report points --}}

         <div class=" alert-success p-20" style="font-size:12px">
            <ul>
                <li><b>GeMEMIS - Geita Mineral Exhibition Management Information System</b> supports both space and service booking, to book for service click <a href="{{ route('book-services.create') }}">Book service</a> To make booking</li>
            </ul>
        </div>

        <div class=" row">
             {{--  boked vs Unbooked  --}}
             @can('VIEW_ANY_BOOKED_SERVICE')
             <a href="{{ route('book-services.create') }}">
                 <div class="form-group col-md-4 mt-20">
                     <button class=" btn bg-purple btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to book service">
                         <h6 class=" text-uppercase text-bold">Book Service<i class=" icon-bookmark4"></i></h6>
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
                         <h6 class=" text-uppercase text-bold"> Booked Services<i class=" icon-list"></i> </h6>
                             <p>{{ date('l, d-m-Y') }}</p>
                     </button>
                 </div>
              </a>
             @endcan
              {{--  //end  --}}
        </div>
 </div>
 </div>
 </div>
 <!-- /content area -->
 </div>
 <!-- /main content -->
 </x-app-layout>
