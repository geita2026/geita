<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsors</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a><i class=" icon-file-spreadsheet position-left"></i> SPONSORSHIP</a></li>
           <li class="active"> List of all sponsors</li>
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
            <h6 class="panel-title text-uppercase text-bold">EXHIBITION SPONSORS</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload" onclick="updateDiv()"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>


<div class=" panel-body">


    {{--  search company from the list  --}}

    <div class="row form-group">
        <div class=" col-md-4">

        </div>
        <div class="has-feedback has-feedback-left col-md-8">
            <input type="text" class="form-control" placeholder=" Search any Advice Note here . . . . . ." id="adviceNote">
            <div class="form-control-feedback">
                <i class="icon-search4 text-size-base"></i>
            </div>
        </div>
    </div>
{{--  end searching  --}}



        <div class=" form-group mt-20">
            <table class="table table-bordered datatable-button-html5-columns table-striped">
                <thead>
                    <tr>
                        <th colspan="7"><h4 class=" text-bold text-center text-uppercase">LIST OF  <q class=" text-primary">@foreach($sponsors as $ad) @endforeach {{ $ad->event_name ?? '' }}</q>  SPONSORS</h4></th>
                    </tr>
                    <tr>
                        <th class=" text-center text-bold">#</th>
                        <th class=" text-bold text-center">SPONSOR NAME</th>
                        <th class=" text-bold text-center">PACKAGE NAME</th>
                        <th class=" text-bold text-center">AMOUNT</th>
                        <th class=" text-bold text-center">BALANCE</th>
                        <th class=" text-bold text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                   @foreach ($sponsors as $item)
                   <tr>
                    <td>{{ $i }}</td>
                    <td title="This sponsor was invoiced invoice with ID: {{ $item->advice_note_id }}"><a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}" class=" text-bold">{{ $item->companyName }}</a></td>
                    <td>{{ $item->package_name }}</td>
                    <td class=" text-right">{{ number_format($item->customer_amount,2) }} {{ $item->type_of_currency }}</td>
                    <td class=" text-right">{{ number_format($item->customer_amount,2) }} {{ $item->type_of_currency }}</td>
                    <td class="text-center">
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                   @can('UPDATE_SPACE_CATEGORY')
                                <li>
                                    <a  data-toggle="modal" data-target="#SetBenefit{{ $item->advice_note_id }}" class=" text-primary"><i class=" icon-pencil7"></i>Set Benefits</a>
                                </li>
                                   @endcan

                                    <li>
                                        <a data-toggle="modal" data-target="#ViewBenefit{{ $item->advice_note_id }}"  class=" text-default"><i class=" icon-eye"></i>View Benefits</a>
                                    </li>



                                </ul>
                            </li>
                        </ul>
                    </td>


                     {{-- view benefits --}}
                     <div id="ViewBenefit{{ $item->advice_note_id }}" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-center text-bold text-uppercase">SET BENEFITS TO <q>{{ $item->advice_note_id }}</q> INVOICE</h5>
                                </div>
                                <div class="modal-body">
                                    @foreach ($benefits as $value)
                                    @if ($item->advice_note_id==$value->advice_note_id)
                                    <ol><i class=" icon-arrow-right14 text-primary"></i> {{ $value->serviceName }} ({{ $value->service_quantity }})</ol>
                                    @endif
                                    @endforeach
                                    <div class=" row form-group">
                                        <div class=" col-md-12 text-right">
                                            <button type="button" class=" btn btn-default border-blue" data-dismiss="modal">close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- view benefits modal  --}}

                    {{-- set advice note description modal --}}
                    <div id="SetBenefit{{ $item->advice_note_id }}" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-center text-bold text-uppercase">SET BENEFITS TO <q>{{ $item->advice_note_id }}</q> INVOICE</h5>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('sponsors.store') }}" method="POST">
                                        @csrf
                                        <div class=" row">
                                            <input type="hidden" name="advice_note_id" value="{{ $item->advice_note_id }}">
                                            <input type="hidden" name="customer_name" value="{{ $item->company_id }}">
                                            <input type="hidden" name="exhibition_year" value="{{ $item->event }}">
                                            <div class=" form-group col-md-6">
                                                <label> Select Service to offer<i class=" text-danger">*</i></label>
                                                <div class="multi-select-full">
                                                    <select name="service[]" class="multiselect-filtering" multiple="multiple">
                                                        @foreach ($service as $item)
                                                        <option value="{{ $item->serviceID}}">{{ $item->serviceName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="text-danger"> @error('role'){{ $message }}@enderror</span>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Set Quantity <i class=" text-danger">*</i></label>
                                                <input type="number" name="service_quantity" class=" form-control" value="1">
                                            </div>
                                        </div>
                                        <div class=" row form-group">
                                            <div class=" col-md-12 text-right">
                                                <button type="button" class=" btn btn-default border-blue" data-dismiss="modal">close</button>
                                                <button type="submit" class="btn btn-primary" @if($item->advice_note_status=='ATTENDED') disabled @endif> Set Benefit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end descritption modal  --}}
                </tr>


                @php $i++; @endphp
                   @endforeach
                </tbody>

            </table>

        </div>

            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/widgets.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/effects.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/extensions/mousewheel.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/globalize.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.de-DE.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.ja-JP.js') }}"></script>

<script>
    var projects = [
      @foreach ($all_advice_notes as $data)
      {
       advice_note_id:"{{ $data->advice_note_id }}",
       label: "{{ $data->advice_note_id }} ~ {{ date('d-m-Y',strtotime($data->advice_note_created_at)) }} ~ {{ $data->companyName }}",
       desc: "{{ $data->registrationID }} ~ {{ $data->companyEmail }} ~ {{ $data->companyPhone }} ~ {{ $data->companyLocation }}",
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
       return $( "<li class='bg-teal-400'>").append( '<a href="'+ item.advice_note_id +'"  class="btn border-white" style="text-align:left;">' +  "<span style='font-size:130%' class='text-uppercase text-bold'>" +item.label+ '</span>' + "<span style='font-size:130%' class='text-uppercase text-bold'>" + '<span class="text-size-small"> <br>' + item.desc + '</a>').appendTo(ul);
   };
</script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>

