<title>{{ env('APP_NAME') }} | Invoices</title>

<x-app-layout>
    <style>
        .zoom{
            padding: 2%;
            transition: transform .5s;
            font-size: 20px;
        }
        .zoom:hover {
          -ms-transform: scale(1.1); /* IE 9 */
          -webkit-transform: scale(1.1); /* Safari 3-8 */
          transform: scale(1.1);
        }
        </style>
   {{-- page header --}}
    <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
       <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-stack-check position-left"></i>Invoices List</a></li>
            <li class="active">View all Invoices</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
</div>
{{--  <!-- Content area -->  --}}
<div class="content">
    <div class="panel panel-white">

        <div class=" panel-body">

   {{-- report points --}}
        <div class="alert-success p-20" style="font-size:10px" style="position: absolute;">
            <p>
                <b> An Advice Note, </b>is a document that confirms the receipt and dispatched of customer order.
                The document is sent by a supplier to a customer to inform him that the space or service ordered has been invoiced.
                The aim of this document is to keep the customer informed about the status of his order, so that he or she can make payment process.
            </p>
        </div>

       {{--  off Fair Advice Notes  --}}
       @can('View Confirmed Invoice')
       <a href="{{ route('controlNoRequests.create') }}" class="form-group col-md-4 zoom">
               <button class=" btn btn-success btn-block btn-float btn-float-lg" type="button"  title="Click to view on fair Advice notes">
                   <h4 class=" text-uppercase text-bold">ON FAIR ADVICE NOTES<i class="  icon-stack-text"></i>
                  new {{ number_format($new_on_fair_advice_note_count) }}, Total {{ number_format($on_fair_advice_note_count) }}
                   </h4>
               </button>
        </a>
       @endcan
        {{--  //end  --}}

         {{--  off Fair Advice Notes  --}}
        @can('View Confirmed Invoice')
        <a href="{{ route('Generated-Advice-Note.index') }}" class="form-group col-md-4 zoom">
                <button class=" btn btn-primary btn-block btn-float btn-float-lg" type="button" title="Click to view Off fair Advice notes">
                    <h4 class=" text-uppercase text-bold">OFF FAIR ADVICE NOTES<i class="  icon-stack-text"></i>
                   new {{ number_format($new_off_fair_advice_note_count) }}, Total {{ number_format($off_fair_advice_note_count) }}
                    </h4>
                </button>
         </a>
        @endcan
         {{--  //end  --}}


            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>
