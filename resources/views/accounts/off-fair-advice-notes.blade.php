<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
@include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left"></i>OFF FAIR ADVICE NOTES</a></li>
            <li class="active">View All Off fair Advice notes</li>
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
            <h6 class="panel-title text-bold">OFF FAIR ADVICE NOTES</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body">

     {{--  search company from the list  --}}
     <div class="row form-group">
        <div class=" col-md-4">
            <div class=" form-group">
                <a href="{{ route('controlNoRequests.index') }}" class=" btn border-slate"><i class=" icon-arrow-left22"></i> Back</a>
            </div>
        </div>
        <div class="has-feedback has-feedback-left col-md-8">
            <input type="text" class="form-control" placeholder=" Search any Advice Note here . . . . . ." id="adviceNote">
            <div class="form-control-feedback">
                <i class="icon-search4 text-size-base"></i>
            </div>
        </div>
    </div>
{{--  end searching  --}}

         <div>
            {{-- table for available customers in database--}}
            <div class=" form-group row">
                <div class=" col-md-12">
                    <table class="table table-bordered datatable-button-html5-columns table-striped">
                        <thead>
                            <tr>
                                <th colspan="7" class=" text-bold text-center"><h3>GENERATED ADVICE NOTE</h3></th>
                            </tr>
                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">ADVICE NOTE ID</th>
                                <th class=" text-bold text-center">CUSTOMER NAME</th>
                                <th class=" text-bold text-center">ADVICE NOTE STATUS</th>
                                <th class=" text-bold text-center">GENERATED ON</th>
                                <th class=" text-bold text-center">ACTIONS</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($advice_notes as $item)
                            <tr>
                                <td>{{ $i }}</td>

                                <td class=" text-bold">
                                    <a href="{{ route('Generated-Advice-Note.show',['Generated_Advice_Note'=>$item->off_fair_advice_note_id]) }}"  title="Click here to view advice Note" >{{ $item->off_fair_advice_note_id }}</a>
                                    <form action="{{ route('Off-Fair-Advice-Notes.edit',['Off_Fair_Advice_Note'=>$item->off_fair_advice_note_id]) }}" method="post" id="View">@csrf @method('GET')
                                        {{-- <input type="hidden" name="advice_note_id" value="{{ $item->off_fair_advice_note_id}}"> --}}
                                    </form>
                                </td>
                                <td class=" text-uppercase">{{ $item->customer_name }}</td>

                               {{-- set deadline --}}
                               <div id="setDeadline{{ $item->off_fair_advice_note_id  }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h4 class="modal-title text-bold text-center">SET OFF FAIR ADVICE NOTE DEADLINE</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('Off-Fair-Advice-Notes.update',['Off_Fair_Advice_Note'=>$item->off_fair_advice_note_id ])}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class=" form-group">
                                                        <label>Set Deadline</label>
                                                        <input type="hidden" name="advice_note" value="{{ $item->off_fair_advice_note_id }}" value="{{ $item->off_fair_advice_note_deadline }}" required>
                                                    </div>
                                                    <input type="date" name="deadline" class=" form-control" required>
                                                </div>
                                                <div class=" modal-footer">
                                                    <div class="  form-group text-right">
                                                        <button class=" btn btn-default border-blue" data-dismiss="modal"> Close Modal</button>
                                                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Set Deadline</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                               {{-- end set deadline --}}

                               {{-- send to accounts --}}
                               <td class=" text-center">
                                @if($item->off_fair_advice_note_status==2)
                                <label class=" text-bold text-warning ">PENDING <i class=" spinner icon-spinner3"></i></label>
                                @elseif ($item->off_fair_advice_note_status==3)
                                <a class=" text-bold text-success" title="Advice not status">APPROVED<i class=" icon-check"></i></a>
                                @elseif ($item->off_fair_advice_note_status==4)
                                <a class=" text-bold text-primary">RETURNED <i class=" icon-star-half"></i></a>
                                @endif
                                </td>
                               {{-- /end sending to accounts --}}

                                    {{-- delete Customer Modal --}}
                                    <div id="back_advice_note{{ $item->off_fair_advice_note_id  }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h4 class="modal-title text-bold text-center">ARE YOU SURE YOU WANT TO RETURN BACK ADVICE NOTE ? </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('Generated-Advice-Note.update',['Generated_Advice_Note'=>$item->off_fair_advice_note_id])}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="advice_note" value="{{ $item->off_fair_advice_note_id }}">
                                                        </div>

                                                        <div class=" modal-footer">
                                                            <div class="  form-group text-right">
                                                                <button class=" btn border-warning" data-dismiss="modal"> Close</button>
                                                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label"> Return Back <i class=" icon-reply"></i></span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    {{-- //delete customer modal --}}
                                    <td class=" text-center">{{ date('d-m-Y h:i:s', strtotime($item->advice_note_created_at)) }}</td>

                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                               @can('Return Back Off Fair Advice Note')
                                                <li>
                                                        <a  data-toggle="modal" data-target="#back_advice_note{{ $item->off_fair_advice_note_id  }}" class=" text-primary text-bold">Return Back <i class=" icon-reply"></i></a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
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
    </div>
</div>
{{-- //user registration  --}}
</x-app-layout>
<script type="text/javascript" src="{{ asset('assets/js/pages/datatables_api.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/widgets.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/effects.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/extensions/mousewheel.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/globalize.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.de-DE.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.ja-JP.js') }}"></script>

<script>
    var projects = [
      @foreach ($advice_notes as $data)
      {
       advice_note_id:"{{ $data->off_fair_advice_note_id }}",
       label: "{{ $data->advice_note_id }} ~ {{ date('d-m-Y', strtotime($data->advice_note_created_at)) }} ~ {{ $data->customer_name }}",
       desc: "<b>{{ $data->off_fair_advice_note_status }}</b> ~ {{ $data->companyEmail }} ~ {{ $data->companyPhone }} ~ {{ $data->companyLocation }} ~ {{ $data->companyCategory }}",
      },
      @endforeach
   ];
    // Initialize autocomplete
    $("#adviceNote").autocomplete({
       minLength: 0,
       source: projects,
       focus: function( event, ui ) {
           $("#adviceNote").val(ui.item.label);
           return false;
       },
       a: function( event, ui ) {
           $("#adviceNote").val(ui.item.label);
           return false;
       }
   })
   .autocomplete("instance")._renderItem = function(ul, item) {
       return $( "<li class='bg-teal-400'>").append( '<a href="Generated-Advice-Note/'+ item.advice_note_id +'"  class="btn border-white" style="text-align:left;">' +  "<span style='font-size:130%' class='text-uppercase text-bold'>" +item.label+ '</span>' + "<span style='font-size:130%' class='text-uppercase text-bold'>" + '<span class="text-size-small"> <br>' + item.desc + '</a>').appendTo(ul);
   };
</script>
