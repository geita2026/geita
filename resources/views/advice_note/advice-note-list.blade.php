<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('adviceNotes.index') }}"><i class=" icon-office position-left"></i>ADVICE-NOTE</a></li>
            <li class="active">View-All Advice Note</li>
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
            <h6 class="panel-title text-bold">LIST OF GENERATED ADVICE NOTES</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
        <div class=" panel-body table-responsive">
            <div class=" form-group">
                <a href="{{ route('adviceNotes.index') }}" class=" btn btn-default text-primary"> <i class=" icon-arrow-left15"></i> Back</a>
            </div>

            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <span class=" text-danger">{{ $error }} ,</span>
            @endforeach
            @endif
            {{-- // end of messages --}}
            <table class=" table table-sm datatable-button-html5-columns table-bordered table-striped table-responsive">
                <thead>
                    <tr class=" bg-success">
                        <th>S/N</th>
                        <th class=" text-bold">Advice Note ID</th>
                        <th class=" text-bold">Company Name</th>
                        <th class=" text-bold">Created At</th>
                        <th class=" text-bold">Deadline</th>
                        <th class=" text-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i=1;
                    @endphp
                    @foreach ($adviceNotes as  $item)
                    <tr>
                        <td>{{ $i }}</td>
                                <td>
                                    @if($item->advice_note_status=='SAC')
                                    <a class="text-warning text-bold" data-toggle="modal" data-target="#description{{ $item->advice_note_id }}">{{ $item->advice_note_id }}</a>
                                    @elseif($item->advice_note_status=='ACCOUNT')
                                    <a class="text-success text-bold" data-toggle="modal" data-target="#description{{ $item->advice_note_id }}">{{ $item->advice_note_id }}</a>
                                    @endif
                                    {{--  description modal  --}}

                                    <div id="description{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title text-center text-bold">ROVIDE DESCRIPTION FOR THIS ADVICE NOTE</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('adviceNotes.update',['adviceNote'=>$item->advice_note_id]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="advice_note_id" value="{{ $item->advice_note_id }}">
                                                        <input type="hidden" name="deadline_id" value="{{ $item->deadline_id }}">
                                                        <div class=" form-group">
                                                            <label>Advice Note Descriptions <i class=" text-danger">*</i></label>
                                                            <textarea  name="description" class=" form-control" placeholder="Type description here" @if($item->advice_note_status=='ACCOUNT') disabled @endif>{{ $item->advice_note_description ?? '-' }}</textarea>
                                                        </div>
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-primary ml-20" @if($item->advice_note_status=='ACCOUNT') disabled @endif> Submit</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end descritption modal  --}}
                                </td>
                                <td>{{ $item->companyName }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td class=" text-center">
                                    @if(empty($item->deadline_date))
                                    <a href="" data-toggle="modal" data-target="#deadline{{ $item->advice_note_id }}"  class=" ml-20 text-bold" > Set Deadline</a>
                                    @elseif($item->advice_note_status=='SAC')
                                    @php
                                    $today=strtotime(date('Y-m-d'));
                                    $deadline=strtotime($item->deadline_date);
                                    @endphp
                                    <a href="{{ route('adviceNotes.edit', ['adviceNote'=>$item->advice_note_id]) }}"  class=" text-bold" >SendToAccountants
                                    </a>
                                    @else
                                    @php
                                    $today=strtotime(date('Y-m-d'));
                                    $deadline=strtotime($item->deadline_date);
                                    $date1=$item->deadline_date;
                                    $date = date("d-m-Y", strtotime($date1));
                                    @endphp
                                    @if($deadline>$today)
                                    <b class=" text-success">{{ $date }}</b>
                                    @else
                                    <b class=" text-warning" data-popup="tooltip" title="Expired Deadline">{{ $date }}</b>
                                    @endif
                                    @endif
                                    {{--  modal to set deadline  --}}
                                    <div id="deadline{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title text-center text-bold">SET ADVICE NOTE DEADLINE DATE</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('adviceNoteDeadlines.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="advice_note_id" value="{{ $item->advice_note_id }}">
                                                        <input type="hidden" name="deadline_id" value="{{ $item->deadline_id }}">
                                                        <div class=" form-group">
                                                            <label>Advice Note deadline Date <i class=" text-danger">*</i></label>
                                                            <input type="date" name="deadline_date"  class=" form-control">
                                                        </div>
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-primary ml-20" >Set Deadline</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                 {{--  action selector  --}}
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>

                                                <a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}" class=" text-indigo text-bold">View Advice Note<i class=" icon-eye"></i></a>

                                            </li>
                                            <li>
                                                @can('Update Advice Note')
                                                @if($item->advice_note_status=='SAC')
                                                <a href="{{ route('update_adviceNotes.show',['update_adviceNote'=>$item->advice_note_id]) }}" class=" text-primary text-bold">Update Advice Note<i class=" icon-pencil7"></i></a>
                                                @else
                                                @endif
                                                @endcan
                                            </li>
                                            <li>


                                                @if($item->advice_note_status=='SAC')
                                                @can('delete-adviceNote')
                                                <a href="" data-toggle="modal" data-target="#d{{ $item->advice_note_id }}"  class="text-danger" data-popup="tooltip" title="Cancel AdviceNote">Cancel Advice Note <i class=" icon-cancel-square"></i></a>
                                                @endcan
                                                @endif
                                            </li>

                                        </ul>
                                    </li>
                                </ul>
                            </td>
                            {{--  //end action selector  --}}

                                {{-- delete modal --}}
                                <div id="d{{ $item->advice_note_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to Cancel this Advice Note ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('adviceNotes.destroy', ['adviceNote' => $item->advice_note_id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
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
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</x-app-layout>
