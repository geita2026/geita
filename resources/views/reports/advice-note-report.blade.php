
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice-Report</title>
    <div class="page-header">
      <div class="page-header-content">
        @include('layouts.heading-elements')
      </div>
      <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Booked Vs Unbooked</a></li>
           <li class="active">View booked and unbooked space</li>
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
   <div class=" panel-body table-responsive">



       <div>
        <div class=" form-group">
            <a href="{{ route('reports.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left12"></i> Back</a>
        </div>
           <table class="table datatable-button-html5-columns table-responsive table-bordered">
               <thead>
                   <tr style="background-color: rgb(219, 217, 217);">
                       <th class=" text-bold text-center" colspan="4">GEITA MINERAL EXHIBITION GENERAL ADVICE NOTE REPORT.</th>
                       <th colspan="2">
                       <form action="{{ route('adviceNote-reports.index') }}" method="DELETE">
                        <select name="exhibition_year">
                            {{--  <option value="">{{ date('Y')-1976 }}</option>  --}}
                            @for ($y =45 ; $y<100 ; $y++)
                            <option>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit">Refresh <i class=" icon-database-refresh"></i></button>
                       </form>
                      </th>
                   </tr>
                   <tr >
                       <th class=" text-bold text-center">Total Advice Note</th>
                       <th class=" text-bold text-center">Total Submitted </th>
                       <th class=" text-bold text-center">Total Pending </th>
                       <th class=" text-bold text-center">Total Expired</th>
                       <th class=" text-bold text-center">Total Unexpired</th>
                       <th class=" text-bold text-center">Exhibition Year</th>
                   </tr>
               </thead>
               <tbody>
                   <tr class=" text-center">
                       <td>{{ $totalAdviceNote }}</td>
                       <td>{{ $submitted }}</td>
                       <td>{{ $pending }}</td>
                       <td>{{ $expired }}</td>
                       <td>{{ $unexpired }}</td>
                       <td>{{ $exhibition_year }}</td>
                   </tr>
               </tbody>
           </table>
       </div>
       {{--  end general booking  --}}

       {{--  specific pavilion booking status  --}}
       <div style="border-top: 10px solid rgb(211, 175, 75);">

       </div>
       <div class=" mt-20">
           <table class=" table datatable-button-html5-columns table-responsive table-bordered">
               <thead>
                   <tr style="background-color: rgb(219, 217, 217);">
                       <th class=" text-bold text-center" colspan="7">GEITA MINERAL EXHIBITION EXHIBITOR ADVICE NOTES REPORT.</th>
                   </tr>
                   <tr>
                       <th class=" text-bold text-center">Advice Note ID</th>
                       <th class=" text-bold text-center">Exhibitor Name</th>
                       <th class=" text-bold text-center">EXhibitor Email</th>
                       <th class=" text-bold text-center">EXhibitor Phone Number</th>
                       <th class=" text-bold text-center">Created At</th>
                       <th class=" text-bold text-center">Exhibition Year</th>
                       <th class=" text-bold text-center">Actions</th>
                   </tr>
               </thead>
               <tbody>

                   @foreach($adviceNotes as $item)
                   <tr>
                       <td>{{ $item->advice_note_id }}</td>
                       <td>{{ $item->companyName }}</td>
                       <td>{{ $item->companyEmail }}</td>
                       <td>{{ $item->companyPhone }}</td>
                       <td>{{ $item->created_at }}</td>
                       <td>{{ $item->exhibition_year }} <sub>Exhibition year</sub></td>
                       <td class="text-center">
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}" class=" text-primary"><i class="icon-folder-open"></i>Open Advice NOte</a></li>
                                    <li ><a href="#" class=" text-danger"><i class="icon-bin"></i>Cancel Advice Note</a></li>
                                    {{--  <li><a href="#"><i class="icon-file-word"></i></a></li>  --}}
                                </ul>
                            </li>
                        </ul>
                    </td>
                   </tr>
                   @endforeach
               </tbody>
           </table>
       </div>
       {{--  end specific booking status  --}}

   </div>
</div>
<!-- /content area -->
</div>
<!-- /main content -->
</x-app-layout>
<!-- /theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_bootstrap_select.js') }}"></script>

