<title>{{ env('APP_NAME') }} | Invoice</title>
<x-app-layout>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans|Rock+Salt|Shadows+Into+Light|Cedarville+Cursive">
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
        <div class=" panel-body table-responsive">


            {{-- error message --}}

           <div class=" col-md-12 form-group">
            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <span class="text-danger">{{ $error }} , </span>
            @endforeach
            @endif
           </div>
            {{-- end error message --}}


            {{--  message  --}}
            @foreach ($adviceNotes as $item)

            <div class=" row form-group">
                <div class=" col-md-2 text-center">
                    <a href="{{ route('my_invoices.index') }}" class=" btn btn-default border-primary"><i class=" icon-arrow-left12"></i> Back</a>
                </div>
                <div class=" col-md-8 text-center" >
                    @if($item->advice_note_status==4) <label class=" bg-danger p-5"  title="{{ $item->rejection_comment }}.">REJECTED</label>
                    @elseif ($item->advice_note_status==3)
                    <label class=" bg-success p-5">APPROVED INVOICE </label>
                    @elseif ($item->advice_note_status==2)
                    <label class=" bg-primary p-5">CONFIRMED INVOICE</label>
                    @elseif ($item->advice_note_status==1)
                    <form action="{{ route('adviceNotes.update',['adviceNote'=>$item->advice_note_id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="confirm" value="confirm">
                        <button type="submit" class=" btn btn-warning">Confirm Invoice</button>
                    </form>

                    @elseif ($item->advice_note_status==0)
                    <label class=" bg-danger-600 p-5">CANCELED INVOICE</label>
                    @endif
                </div>
                <div class="col-md-2 text-center">
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
                                <a onclick=" event.preventDefault();document.getElementById('download_invoice').submit();" class=" text-primary text-bold" title="Download Invoice">Download Invoice<i class=" icon-download4"></i></a>
                               </li>
                               @endif
                               @endcan
                               {{-- end download invoice --}}

                                @can('DELETE_ADVICE_NOTE')
                                @if ($item->advice_note_status!=3 AND $item->advice_note_status!=0)
                                <li >
                                    <a  data-toggle="modal" data-target="#{{ $item->advice_note_id }}" title="Cancel Advice Note" class=" text-danger"><i class=" icon-cancel-circle2"></i> Cancel Invoice</a>
                                </li>
                                @endif
                                @endcan

                                @if ( date('d-m-Y',strtotime($item->advice_note_deadline)) < date('d-m-Y'))
                                <li>
                                <a data-toggle="modal" data-target="#Extend{{ $item->advice_note_id }}" class=" text-primary"><i class=" icon-calendar"></i> Extend Deadline</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>


                {{-- Extend deadline modal --}}
                <div id="Extend{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title">Are you sure you whant to extend deadline from
                                    <b>{{ date('M d, Y', strtotime($item->advice_note_deadline)) }} </b> To New Date ? </h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('adviceNoteDeadlines.update', ['adviceNoteDeadline' => $item->advice_note_id]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="extend_deadline" value="extend_deadline">
                                    <div class=" form-group">
                                        <label>Give reasons for Extending Invoice Deadline<i class=" text-danger">*</i></label>
                                        <textarea name="reasons" class=" form-control" placeholder="Write your reasons here" required>{{ old('reasons') }}</textarea>
                                    </div>
                                    <div class=" form-group">
                                        <label> Provide Endorsement Attachment <i class=" text-danger">*</i></label>
                                        <input type="file" name="endorsement_attachment" class=" form-control" value="{{ old('endorsement_attachment') }}" required>
                                    </div>

                                    <div class=" form-group">
                                        <label> Select New Deadline Date<i class=" text-danger">*</i></label>
                                        <input type="date" name="expire_date" class=" form-control" value="{{ old('expire_date') }}">
                                    </div>

                                    <div class=" text-right">
                                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-primary ml-20" > Extend Deadline</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- End extend modal --}}

                {{-- delete modal --}}
                <div id="{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h5 class="modal-title">Are you sure you whant to Cancel <b><q>{{ $item->advice_note_id }}</q></b> Invoice ?</h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('adviceNotes.destroy', ['adviceNote' => $item->advice_note_id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class=" form-group">
                                        <label>Give reasons for Canceling this Invoice<i class=" text-danger">*</i></label>
                                        <textarea name="reasons" class=" form-control" placeholder="Write your reasons here" required></textarea>
                                    </div>

                                    <div class=" text-right">
                                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-danger ml-20" >Yes, Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- delete modal --}}

            </div>
            <div id="printableArea">
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
