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
<title>{{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
    {{-- contents on dashboards --}}
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
          @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="#"><i class="icon-home2 position-left"></i>ADVICE NOTE</a></li>
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
<div class=" panel-body table-responsive">



     @foreach ($adviceNotes as $item)

   <div class=" row">
    <div class=" form-group col-md-2 text-left">
        <a href="{{ route('Generated-Advice-Note.index') }}" class=" btn btn-default"><i class=" icon-arrow-left12"></i> Back</a>
    </div>

    <div class=" form-group col-md-4 text-right">

        @if($item->off_fair_advice_note_status==2)
        @can('Approve Invoice')
        <a onclick=" event.preventDefault();document.getElementById('ApproveInvoice').submit();" class=" btn btn-warning "> Approve Invoice</a>
        @endcan
        @else
        <label class=" bg-success p-10">Approved Invoice</label>
        @endif

        <form action="{{ route('Off-Fair-Advice-Notes.update',['Off_Fair_Advice_Note'=>$item->off_fair_advice_note_id]) }}" method="POST" id="ApproveInvoice">
            @csrf
            <input type="hidden" name="approve_invoice" value="1">
            @method('PATCH')
        </form>
    </div>


  <div class=" form-group col-md-6 text-right">
      <button class="btn btn-primary" onclick="printPageArea('printableArea')">Print <i class=" icon-printer2"></i></button>
  </div>
   </div>


<div id="printableArea">
        @include('Off-Fair.Advice-Notes/printing-pages/Invoice')

        </div>
    </div>
</div>
</div>

@endforeach
<!-- /main content -->
</x-app-layout>
