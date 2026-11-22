
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Help</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
        @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="#"><i class=" icon-info22 position-left"></i>HELP & FEEDBACK</a></li>
           <li class="active">user feedback</li>
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
        <div class=" table-header">
            <div class="panel-heading ">
                <h6 class="panel-title  text-bold text-uppercase text-center">USER FEEDBACK AND FREQUENTLY ASKED QUASTIONS (FAQs)</h6>
            </div>
        </div>
<div class=" panel-body table-responsive">



@if (Auth::user()->account_type==1)
<div class=" form-group col-md-12">
    <table class=" table table-bordered table-responsive">
        <thead>
            <tr class=" table-header">
                <th colspan="5" class=" text-center text-bold">CUSTOMER COMMENTS PREVIEW LIST</th>
            </tr>
            <tr>
                <th class=" text-center text-bold">COMMENT</th>
                <th class=" text-center text-bold">COMMENTED BY</th>

                <th class=" text-center text-bold">STATUS</th>
                <th class=" text-center text-bold">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feedback as $item)
            <tr>
                <td>{{ $item->customer_comment }} (<span class=" text-muted text-small">{{ date('M d, Y h:i:s',strtotime($item->asked_at)) }}</span>)</td>
                <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                <td class=" text-center">
                    @if($item->answer==Null)<b class=" text-danger">Not Attended</b>
                    @else <b class=" text-success"> Attended On: </b><span class=" text-small text-muted">{{ date('M d, Y h:i:s',strtotime($item->attended_on)) }}</span>
                    @endif
                </td>

                <td class="text-center">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-menu4"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a data-toggle="modal" data-target="#attend{{ $item->feedback_id }}" class=" text-primary">
                                        @if($item->answer==Null) Attend Comment @else Update Answer @endif<i class=" icon-reply"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="" data-toggle="modal" data-target="#delete{{ $item->feedback_id }}"  class="text-danger">Delete Comment <i class=" icon-bin"></i></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>


              {{-- answer modal --}}
              <div id="attend{{ $item->feedback_id }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-uppercase text-bold"> <b>{{ $item->customer_comment }}</b></h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('helps.update',['help'=>$item->feedback_id]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class=" form-group">
                                    <label>Answer<i class=" text-danger">*</i></label>
                                    <textarea name="answer" class=" form-control" rows="10">{{ $item->answer }}</textarea>
                                </div>
                                <div class=" form-group">
                                    <label>Is FAQ ?</label>
                                    <select name="is_faq" class=" form-control">
                                        @if($item->is_faq==1)
                                        <option value="1">YES (This Option will list This question to FAQ list)</option>
                                        @endif
                                        <option value="0"> No (This option will hide this question from FAQ list)</option>
                                        <option value="1">YES (This Option will list This question to FAQ list)</option>
                                    </select>
                                </div>

                                <div class=" text-right">
                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                    <button type="submit"  class=" btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- answer modal --}}


            @endforeach
        </tbody>
    </table>
   </div>
@endif


<!-- Questions area -->
<h4 class="text-center content-group">
    All questions are answered
    <small class="display-block">Pass through all questions, if you did'nt get help just left your comment
         our officers will answer you as soon as they get into system</small>
</h4>



<div class="row">
    <div class="col-lg-7">

        <!-- Questions list -->
        <div class="panel-group panel-group-control panel-group-control-right">


            @foreach ($faq as $item)
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" href="#{{ $item->feedback_id }}">
                            <i class="icon-help position-left text-slate"></i> {{ $item->customer_comment }}
                        </a>
                    </h6>
                </div>

                <div id="{{ $item->feedback_id }}" class="panel-collapse collapse">
                    <div class="panel-body">
                        {{ $item->answer }}
                    </div>

                    <div class="panel-footer panel-footer-transparent">
                        <div class="heading-elements">

                            <ul class="list-inline list-inline-condensed heading-text pull-right">
                                <li><span href="#" class=" text-muted text-capitalize">Latest answer update: {{ date('M d, Y h:i:s',strtotime($item->attended_on)) }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>


    </div>

    {{-- external Users --}}
    @can('GIVE_FEEDBACK')
    <div class="col-lg-5">
        <!-- Search -->
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title">Leave your comment or question here</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a href="#"><i class=" icon-envelop2"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
               <div class=" form-group col-md-12">
                <form action="{{ route('helps.store') }}" method="POST">
                    @csrf
                    <div class=" col-md-12">
                        <div class=" form-group">
                            <textarea type="text" name="customer_comment" class="form-control input-lg" placeholder=" Your comment"></textarea>
                            <span class=" text-danger">@error('customer_comment') {{ $message }} @enderror</span>
                        </div>
                        <div class=" form-group text-right">
                            <button type="submit" class="btn btn-primary btn-lg btn-icon">Send <i class="  icon-arrow-right13"></i></button>
                        </div>
                    </div>
                </form>
               </div>

               <div class=" form-group col-md-12">
                <table class=" table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th class=" text-center">ASKED QUESTION</th>
                            <th class=" text-center">DATE ASKED</th>
                            <th class=" text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feedback as $item)
                        <tr>
                            <td>{{ $item->customer_comment }}</td>
                            <td>{{ date('M d, Y h:i:s',strtotime($item->asked_at)) }}</td>
                            <td class=" text-center">
                                @if ($item->answer==Null)
                                <a  data-toggle="modal" data-target="#delete{{ $item->feedback_id }}" class=" btn  text-danger"> Remove <i class=" icon-cross"></i> </a>
                                @else
                                <a data-toggle="modal" data-target="#answer{{ $item->feedback_id }}"  class=" btn ">View Answer <i class=" icon-arrow-right13"></i></a>
                                @endif
                            </td>
                        </tr>


                          {{-- answer modal --}}
                          <div id="answer{{ $item->feedback_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-uppercase text-bold"> <b>{{ $item->customer_comment }}</b></h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('helps.update',['help'=>$item->feedback_id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class=" form-group">
                                                {{ $item->answer }}
                                                <br><br><span class=" text-muted">Attended one: {{ date('M d, Y h:i:s',strtotime($item->attended_on)) }}</span>
                                            </div>

                                            <div class=" text-right">
                                                <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- answer modal --}}


                {{-- delete modal --}}
              <div id="delete{{ $item->feedback_id }}" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title"> are you sure you want to delete <b>{{ $item->customer_comment }}</b> question ?</h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('helps.destroy',['help'=>$item->feedback_id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class=" form-group">
                                    <i class=" icon-warning2 text-danger"></i> This process can not be reversed, such that All information associated will be deleted.
                                </div>

                                <div class=" text-right">
                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                    <button type="submit"  class=" btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- delete modal --}}

                        @endforeach
                    </tbody>
                </table>
               </div>
            </div>
        </div>
        <!-- /search -->

    </div>
    @endcan
    {{-- internal users --}}

</div>
<!-- /questions area -->


</div>
</div>
</div>
</x-app-layout>

