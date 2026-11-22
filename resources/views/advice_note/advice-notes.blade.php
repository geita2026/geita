<title>{{ env('APP_NAME') }} | AdviceNotes</title>

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
            <li><a href="#"><i class="icon-stack-check position-left"></i>All Invoices</a></li>
            <li class="active">View all invoices</li>
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

            <div class=" table-header p-20" style="font-size:10px" style="position: absolute;">
                <p>
                    <b> An Invoice, </b>is a document that confirms the receipt and dispatched of customer order.
                    The document is sent by a supplier to a customer to inform him that the space or service ordered has been invoiced.
                    The aim of this document is to keep the customer informed about the status of his order, so that he or she can make payment process.
                </p>
                <p><b>ON Fair Invoices,</b> This are invoices provided specific for exhibitions and trade fairs such as, DITF and Industrial Exhibitions.</p>
            </div>

            <div class="row">
            {{-- report points --}}
            @can('View All Advice Note')

                @can('View On Fair Advice Notes')
                <a href="{{ route('adviceNotes.create') }}" class=" form-group col-md-4 zoom ">
                    <button class=" btn btn-success btn-block btn-float btn-float-lg"  title="Click to view on fair Advice notes">
                  <h4 class=" text-uppercase text-bold" id="h2">ON FAIR INVOICES<i class="  icon-stack-text"></i>
                    Total &colon; {{ number_format($on_fair_advice_notes ) }}
                </h4>
                    </button>
               </a>
            @endcan
          {{--  //end  --}}


         {{--  //end  --}}
        </div>
        @endcan

    </div>
</div>
</div>
<!-- /main content -->
</x-app-layout>
