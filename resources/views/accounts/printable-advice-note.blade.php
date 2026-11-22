<title>{{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
        @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="#"><i class="icon-home2 position-left"></i></a> Invoice Preview</li>
           <li class="active">Preview Invoice</li>
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
            {{--  message  --}}
            @foreach ($adviceNotes as $item)

            <div class=" row form-group">
                <div class=" col-md-2">
                    <a href="{{ route('controlNoRequests.create') }}" class=" btn btn-default border-primary"><i class=" icon-arrow-left12"></i> Back</a>
                </div>
                <div class=" col-md-4 text-center" >

                    @if ($item->advice_note_status==2)

                    @if ($item->change_currency==1 AND $item->exchange_rate!=null)
                    <form action="{{ route('adviceNotes.update',['adviceNote'=>$item->advice_note_id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="request_control_no" value="request_control_no">
                        <button type="submit" class=" btn btn-warning">Approve Invoice</button>
                    </form>
                    @elseif ($item->change_currency==0)
                    <form action="{{ route('adviceNotes.update',['adviceNote'=>$item->advice_note_id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="request_control_no" value="request_control_no">
                        <button type="submit" class=" btn btn-warning">Approve Invoice</button>
                    </form>
                    @endif

                    @elseif ($item->advice_note_status==3)
                    <a class=" text-bold  label label-success btn btn-success" onclick="RefreshPage();">Approved Invoice</a>
                    @elseif ($item->advice_note_status==4)
                    <a class=" text-bold  label label-success  btn bg-pink" onclick="RefreshPage();">REJECTED INVOICE</a>
                    @endif
                </div>

                {{-- ********* CHANGE PAYMENT CURRENCY ************ --}}
                <div class=" col-md-4 text-right">
                   @if($item->change_currency==1 && $item->exchange_rate==null && $item->advice_note_status==2)
                   <button class=" btn btn-primary" data-toggle="modal" data-target="#ChangeCurrency{{ $item->advice_note_id }}"> Change Currency</button>
                   @elseif ($item->change_currency==1 && $item->exchange_rate!=null)
                   <b class=" text-success" >Amount exchanged from USD to TZS at exchange rate of 1 USD = {{ $item->exchange_rate }} TZS.</b>
                   @endif
                </div>

                <div id="ChangeCurrency{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title text-bold text-center text-uppercase">CHANGE CURRENCY FOR <b>{{ $item->advice_note_id }}</b> Invoice.</h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('controlNoRequests.update', ['controlNoRequest'=>$item->advice_note_id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class=" row">
                                        <div class=" form-group com-md-12">
                                            <input type="text" name="exchange_rate" class=" form-control" min="1" value="{{ $item->exchange_rate ?? 0 }}">
                                        </div>
                                        <input type="hidden" name="change_amount" value="1">
                                        <div class=" form-group col-md-12 text-right">
                                            <button type="submit" class=" btn btn-primary">Change Currency to TZS</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ********* CHANGE PAYMENT CURRENCY *********** --}}


                <div class="col-md-2 text-right">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-primary text-bold btn btn-default border-success" data-toggle="dropdown">
                                Actions <i class="icon-menu4"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                {{-- download invoice --}}
                                @can('DOWNLOAD_INVOICE')
                                @if ($item->control_number!=Null)
                                <li>
                                 <a onclick=" event.preventDefault();document.getElementById('download_invoice').submit();" class=" text-primary text-bold" title="Download Invoice">Print Invoice<i class=" icon-printer2"></i></a>
                                </li>
                                @endif
                                @endcan
                                {{-- end download invoice --}}
                                @can('CANCEL_ADVICE_NOTE')
                                @if($item->advice_note_status==3 || $item->advice_note_status==2)
                                <li>
                                    <a  data-toggle="modal" data-target="#SendBack{{ $item->advice_note_id }}" title="return Advice Note" class=" text-warning text-bold"><i class=" icon-backspace"></i> Reject Invoice</a>
                                </li>
                                @endif
                                @endcan
                            </ul>
                        </li>
                    </ul>
                </div>
                {{-- delete modal --}}
                <div id="SendBack{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">You are about to Reject <b>{{ $item->advice_note_id }}</b> Invoice?</h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('controlNoRequests.update', ['controlNoRequest'=>$item->advice_note_id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                   <div class=" row form-group">
                                    <div class="col-md-12 form-group">
                                        <textarea name="rejection_comment" class=" form-control" placeholder=" Provide resons for rejecting this invoice" required>{{ $item->rejection_comment }}</textarea>
                                    </div>
                                    <div class=" col-md-12 text-right form-group">
                                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-warning" >Reject Invoice <i class=" icon-arrow-right13"></i></button>
                                    </div>
                                   </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- delete modal --}}
            </div>



<div id="printableArea">
    <style>
        </style>
   @include('advice_note.invoice')
        @endforeach
    </div>
</div>

</div>

</div>

</div>
<!-- /content area -->
</div>
<!-- /main content -->
</x-app-layout>

{{-- download form --}}
<form action="{{ route('download_invoice') }}" method="POST" id="download_invoice" target="_blank">
    @csrf
    <input type="hidden" name="id" value="{{ $item->advice_note_id }}">
    @method('GET')
</form>
{{-- end --}}

<script>
    @if(Session::get('success'))
    setTimeout(() => {
    document.location.reload();
  }, 10000);
  @endif

  function RefreshPage(){
    document.location.reload();
  }
</script>
