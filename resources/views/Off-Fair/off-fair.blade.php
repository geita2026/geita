
<x-app-layout>
    {{--  <meta http-equiv="refresh" content="1" class="content" />  --}}
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('Off-Fair.index') }}"><i class="icon-home2 position-left"></i>Off-Fair</a></li>
           <li class="active">Off-Fair Dashboard</li>
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
            <h6 class="panel-title text-bold">THE DEVELOPMENT TRADE PROMOTION (DTP) DEPARTMENT OFFERS OFF-FAIR SERVICES TO CUSTOMER</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload" onclick="updateDiv()"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>

<div class=" panel-body table-responsive">

   {{-- report points --}}
        <div class="alert-success p-20" style="font-size:10px" style="position: absolute;">
            <p>
                <b>Off-Fair service, </b>This are services which are provided by Development Trade Promotion Department
                out of Exhibition period. This services are provide within ten months from 1st  of August to 30th  Of may,
                 this services may involve Space and Hall renting for different specified uses such as ceremonies, offices, e.t.c.
            </p>
        </div>

        {{--  Register / registered companies  --}}
        @can('Create Off-Fair Customer')
        <a href="{{ route('Off-Fair-Customers.create') }}">
            <div class="form-group col-md-3 mt-20">
                <button class=" btn btn-primary btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view off fair Customer">
                    <h6 class=" text-uppercase text-bold">OFF FAIR CUSTOMERS<i class=" icon-users4"></i></h6>

                </button>
            </div>
         </a>
        @endcan
         {{--  //end  --}}

        {{--  boked vs Unbooked  --}}
        @can('View Any Off-Fair Space')
        <a href="{{ route('Off-Fair-Spaces.index') }}">
            <div class="form-group col-md-3 mt-20">
                <button class=" btn bg-success btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view off fair spaces">
                    <h6 class=" text-uppercase text-bold">Off-Fair Spaces<i class=" icon-home7"></i></h6>

                </button>
            </div>
         </a>
        @endcan
         {{--  //end  --}}

             {{--  My space  --}}
             @can('View All Off-Fair Utilities')
             <a href="{{ route('Off-Fair-Utilities.index') }}">
                 <div class="form-group col-md-3 mt-20">
                     <button class=" btn bg-brown btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to view off fair utilities">
                         <h6 class=" text-uppercase text-bold">OFF FAIR UTILITIES<i class=" icon-server"></i></h6>

                     </button>
                 </div>
              </a>
              {{--  reports  --}}
             @endcan

              {{--  My space  --}}
              @can('View Off-Fair Booking Page')
              <a href="{{ route('off-fair-space-booking.index') }}" >
                  <div class="form-group col-md-3 mt-20">
                      <button class=" btn bg-indigo btn-block btn-float btn-float-lg" type="button" data-popup="tooltip" title="Click to Book Now">
                          <h6 class=" text-uppercase text-bold">SPACE BOOKING<i class=" icon-cart"></i></h6>
                      </button>
                  </div>
               </a>
               {{--  reports  --}}
              @endcan

               {{--  My space  --}}
               @can('View Off-Fair Space Category')
               <a href="{{ route('space_categories.index') }}">
                   <div class="form-group col-md-3 mt-20">
                       <button class=" btn bg-purple btn-block btn-float btn-float-lg" type="button" title="Click to view space Categories">
                           <h6 class=" text-uppercase text-bold">SPACE CATEGORIES<i class=" icon-tree5"></i></h6>
                       </button>
                   </div>
                </a>
                {{--  reports  --}}
               @endcan


                {{--  My space  --}}
                @can('View Off Fair Activities')
                <a href="{{ route('off-fair-activities.index') }}">
                    <div class="form-group col-md-3 mt-20">
                        <button class=" btn  bg-info btn-block btn-float btn-float-lg" type="button" title="Click to view off fair activities">
                            <h6 class=" text-uppercase text-bold">OFF FAIR ACTIVITIES<i class=" icon-task"></i></h6>
                        </button>
                    </div>
                 </a>
                 {{--  reports  --}}
                @endcan

                  {{--  My space  --}}
                  @can('View Off-Fair Advice Note')
                  <a href="{{ route('Off-Fair-Advice-Notes.index') }}">
                      <div class="form-group col-md-3 mt-20">
                          <button class=" btn bg-violet btn-block btn-float btn-float-lg" type="button" title="Click to view generated Advice Notes">
                              <h6 class=" text-uppercase text-bold">OFF FAIR ADVICE NOTES<i class="icon-stack-check"></i></h6>
                          </button>
                      </div>
                   </a>
                   {{--  reports  --}}
                  @endcan

                    {{--  My space  --}}
                    @can('View Space Size Costs')
                    <a href="{{ route('space_size_costs.index') }}">
                        <div class="form-group col-md-3 mt-20">
                            <button class=" btn btn-warning btn-block btn-float btn-float-lg" type="button" title="Click to view space cost categories">
                                <h6 class=" text-uppercase text-bold">SPACE COST CATEGORIES<i class=" icon-wallet"></i></h6>
                            </button>
                        </div>
                     </a>
                     {{--  reports  --}}
                    @endcan

            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>

  {{-- /loaded data from database --}}
  <div id="new_customer" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-bold text-center">NEW CUSTOMER REGISTRATION</h5>
                </div>
                <div class="modal-body">
                    <div class=" form-group">
                        <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                    </div>
                    <form action="{{ route('Off-Fair-Customers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                     <div class="row form-group">
                        <div class=" form-group col-md-12">
                            <label>Customer Name</label>
                           <select name="companyID" class="bootstrap-select " data-live-search="true" data-width="100%">
                               <option value="">- - - Select Customer from list - - -</option>
                                   @foreach ($customers as $item)
                                   <option value="{{ $item->customer_id}}">{{ $item->customer_name }}</option>
                                   @endforeach
                           </select>
                        </div>
                          <div class=" form-group col-md-12">
                              <label>Customer Name<i class=" text-danger">*</i></label>
                              <input type="text" name="customer_name" class=" form-control" placeholder="Customer Name" value="{{ $data->companyName ?? old('customer_name') }}">
                              <span class="text-danger"> @error('customer_name'){{ $message }}@enderror</span>
                           </div>
                     </div>
                    </div>

                <div class=" modal-footer">
                    <div class="  form-group text-right">
                        <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                        <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register Off-Fair Customer</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- // new customer modal --}}
