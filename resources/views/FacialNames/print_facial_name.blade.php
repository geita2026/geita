<script>
    function printPageArea(areaID){
        var printContent = document.getElementById(areaID);
        var WinPrint = window.open('', '', 'width=900,height=650');
        WinPrint.document.write(printContent.innerHTML);
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
    }
</script>


{{-- <page size="A4"></page>
<page size="A4" layout="landscape"></page>
<page size="A5"></page>
<page size="A5" layout="landscape"></page>
<page size="A3"></page>
<page size="A3" layout="landscape"></page> --}}

<title>{{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
        @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('adviceNotes.index') }}"><i class="icon-home2 position-left"></i>ADVICE NOTE</a></li>
           <li class="active">view Advice note</li>
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
        <div class=" panel-body">

            <div class=" row form-group">
                <div class=" col-md-12 form-group">
                    <a href="{{ route('facial_name_companies.index') }}" class=" btn btn-default border-primary"><i class=" icon-arrow-left7"></i> Back</a>
                </div>
             </div>




                    {{-- edit role modal --}}
                     <div id="filter_badges" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title text-uppercase text-bold text-center">FILTER BADGES By Any of bellow details</h5>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('badges.show',['badge']) }}" method="POST">
                                        @csrf
                                        @method('GET')
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Select Company</label>
                                               <select name="company_id" class="bootstrap-select" data-live-search="true" data-width="100%" >
                                                <option value=""> - - - - Select Company - - - - - </option>

                                                       @foreach ($companies as $company)

                                                       <option value="{{ $company->company_id}}">{{ $company->company_name }}</option>

                                                       @endforeach
                                               </select>
                                            </div>
                                           </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Load Badges</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /edit role modal -->



             <div class=" row form-group  text-center bg-info-400">

                <p>
                    <div class=" col-md-6 form-group">
                        <div class=" col-md-10 text-right">
                            <a data-toggle="modal" data-target="#filter_badges" class=" btn btn-default border-primary">Filter <i class=" icon-filter3"></i></a>
                        </div>
                    </div>
                    <div class=" col-md-2 text-right form-grop">
                        <a onclick="printPageArea('printableArea')" class=" text-primary btn btn-default border-primary">Print Badges <i class=" icon-printer2"></i></a>
                    </div>
                </p>

             </div>

             @php $i=0; @endphp
             {{-- badges frame setting --}}
             <div id="printableArea" >
                @foreach ($companies as $item)
                <div style=" margin-top:20px;">
                    <div style="border:5px solid green; padding:2px; width:100%;">
                        <div style="border:5px solid yellow;padding:2px;">
                            <div style="border:5px solid black;padding:2px;">
                                <div style=" border:5px solid blue; padding:2px">
                                    <div style="text-align:center; height:900px;">
                                        <div style="background-color:{{ $item->background_color }};">
                                            <b style="text-transform: uppercase;color:{{ $item->text_color }};font-size:120%;">{{ $item->company_name }}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
                </div>
                @endforeach
            </div>
    </div>
  </div>
</div>
</x-app-layout>
