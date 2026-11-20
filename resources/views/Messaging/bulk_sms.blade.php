<x-app-layout>
    <title>{{ env('APP_NAME') }} | Bulk-SMS</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('messaging.index') }}"><i class=" icon-envelop position-left"></i>BULK MESSAGES</a></li>
            <li class="active">Messaging - Bulk messages</li>
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

            <div class=" table-header form-group p-10">
                <h6 class=" text-center text-uppercase text-bold">BULK SMS</h6>
            </div>

            {{--  error message  --}}
            <div class=" form-group">
             @if ($errors->any())
             @foreach ($errors->all() as $error)
             <span class="text-danger ml-10">{{ $error }}</span>
             @endforeach
             @endif
            </div>


             <div id="DeleteDraftMessages" class="modal fade" data-backdrop="static" data-keyboard="false">
                 <div class="modal-dialog modal-md">
                     <div class="modal-content">
                         <div class="modal-header bg-warning-400">
                             <h5 class="modal-title text-bold text-center"><i class=" icon-warning22"></i> DELETE ALL DRAFT MESSAGES ?</h5>
                         </div>
                         <form action="{{ route('bulk_sms.destroy',['bulk_sm'=>"delete-messages"]) }}" method="POST">
                             @csrf
                             @method('DELETE')
                         <div class="modal-body">
                            <input type="hidden" name="source" value="0">
                             <p>
                                <code>NOTE:</code> This process is irrevesible!
                             </p>

                         </div>

                         <div class="modal-footer">
                             <button type="button" class="btn btn-default border-blue" data-dismiss="modal">Close</button>
                             <button type=" submit" class="btn btn-warning btn-ladda btn-ladda-spinner">Yes, Delete <i class=" icon-bin"></i></button>
                         </div>
                     </form>
                     </div>
                 </div>
             </div>
             <!-- /basic modal -->


             {{-- Delete Sent messages --}}
             <div id="DeleteSentMessages" class="modal fade" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-warning-400">
                            <h5 class="modal-title text-bold text-center">REMOVE ALL SENT MESSAGES</h5>
                        </div>
                        <form action="{{ route('bulk_sms.destroy',['bulk_sm'=>'DeleteMessage']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                        <div class="modal-body">
                            <input type="hidden" name="source" value="1">
                            <p>
                                <code>NOTE:</code> This process is irrevesible!
                             </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default border-blue" data-dismiss="modal">Close</button>
                            <button type=" submit" class="btn btn-warning btn-ladda btn-ladda-spinner">Yes, Delete <i class=" icon-bin"></i></button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <!-- /basic modal -->


             <div class=" row form-group">

                 <div class=" col-md-6 form-group text-right p-20">
                      <!-- Top right menu -->
                      <ul class="fab-menu fab-menu-absolute fab-menu-top-left" data-fab-toggle="hover" id="fab-menu-affixed-demo-right">
                        <li>
                            <a class="fab-menu-btn btn bg-teal-400 btn-float btn-rounded btn-icon">
                                <i class="fab-icon-open  icon-envelop3"></i>
                                <i class="fab-icon-close icon-cross2"></i>
                            </a>
                            <ul class="fab-menu-inner">
                                <li>
                                    <div data-fab-label="Compose SMS">
                                        <a data-toggle="modal" data-target="#compose"   class="btn bg-blue-400 btn-rounded btn-icon btn-float">
                                            <i class="icon-compose"></i>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div data-fab-label="Clear All Draft Messages">
                                        <a  data-toggle="modal" data-target="#DeleteDraftMessages" class="btn btn-warning btn-rounded btn-icon btn-float">
                                            <i class=" icon-bin"></i>
                                        </a>
                                    </div>
                                </li>

                                <li>
                                    <div data-fab-label="Clear All Sent Messages">
                                        <a  data-toggle="modal" data-target="#DeleteSentMessages" class="btn btn-warning btn-rounded btn-icon btn-float">
                                            <i class=" icon-bin"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <!-- /top right menu -->
                </div>

             </div>



             <!-- Basic modal -->
                     <div id="compose" class="modal fade" data-backdrop="static" data-keyboard="false" data-backdrop="static" data-keyboard="false">
                         <div class="modal-dialog modal-lg">
                             <div class="modal-content">
                                 <div class="modal-header bg-primary">
                                     <h5 class="modal-title text-bold text-center">COMPOSE MESSAGE FOR SPECIFIC Customer GROUP</h5>
                                 </div>
                                 <form action="{{ route('bulk_sms.store') }}" method="POST">
                                     @csrf
                                 <div class="modal-body">
                                     <div class=" row">
                                      <div class="form-group">
                                         <label><span class="text-bold">Select Recipicients Group </span>  <i class=" text-danger">*</i></label>
                                             <select id="Receivers" name="group_name" class=" bootstrap-select" data-live-search="true" data-width="100%" placehoder="Select department" required autofocus>
                                                 <option value="Single">Single Contact</option>
                                                 <option value="CurrentExhibitors">Current Exhibitors</option>
                                                 <option value="AllExhibitors">All Exhibitors</option>
                                                 <option value="AllCustomers"  > All Registered Customers</option>
                                                 @foreach ($groups as $item)
                                                 <option value="{{ $item->group_id }}">{{ $item->group_name }}</option>
                                                 @endforeach

                                             </select>
                                     </div>

                                     <div class=" form-group row" id="Single">
                                        <div class=" col-md-6">
                                            <label>Customer Name<i class=" text-danger">*</i></label>
                                            <input type="text" name="customer_name" class=" form-control" placeholder="Enter Customer name">
                                         </div>
                                         <div class=" col-md-6">
                                            <label>Customer Phone Number<i class=" text-danger">*</i></label>
                                            <input type="text" name="phone_number" class=" form-control" placeholder="Enter phone Number">
                                         </div>
                                     </div>

                                     <div class="form-group">
                                        <label> your Message (max 100 characters)</label>
                                        <textarea name="sms_body" class=" form-control" rows="4" placeholder=" Type your message here" required>{{ old('sms_body') }}</textarea>
                                    </div>

                                     <div class=" form-group">
                                        <label>Select Date Message to be sent <i class=" text-danger">*</i></label>
                                        <input type="date" name="sms_sent_on" class="form-control date-step" placeholder="Set when emails must be sent" required>
                                        <span class="help-block">This date will act as customer schedule for sending composed messages </span>
                                    </div>
                                     </div>
                                 </div>

                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default border-warning text-warning" data-dismiss="modal">Close</button>
                                     <button type=" submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Organize SMS <i class="  icon-square-right"></i></button>
                                 </div>
                             </form>
                             </div>
                         </div>
                     </div>
                     <!-- /basic modal -->


          <!-- Tabs -->
          <div class=" col-md-2">
            <ul class="nav nav-lg nav-tabs nav-justified no-margin no-border-radius bg-info-400">
                <li class="active">
                    <a href="#draft" class="text-size-small text-uppercase text-bold" data-toggle="tab">
                         Draft SMS
                    </a>
                </li>

                <li class=" col-md-12">
                    <a href="#sent" class="text-size-small text-uppercase text-bold" data-toggle="tab">
                         Sent SMS
                    </a>
                </li>
            </ul>
          </div>
         <!-- /tabs -->

         <!-- Tabs content -->
         <div class="tab-content col-md-10">
             <div class="tab-pane active fade in has-padding" id="draft">
                 <table class=" table table-bordered datatable-button-html5-columns table-striped">
                     <thead>
                         <tr>
                            <th class=" text-bold">CUSTOMER_NAME</th>
                            <th class=" text-bold">CUSTOMER_PHONE_NO</th>
                            <th class=" text-bold">SCHEDULED_ON</th>
                            <th class=" text-bold">MESSAGE CONTENT</th>
                         </tr>
                     </thead>
                     <tbody>


                         @php $i=0; @endphp
                         @foreach ($draft_messages as $item)
                         <tr>
                             <td title="{{ $item->sms_body }}">{{ $item->customer_name }}</td>
                             <td>{{ $item->phone_number }}</td>
                             <td>{{ date('d-m-Y',strtotime($item->sms_sent_on)) }}</td>
                             <td>{{$item->sms_body  }}</td>


                             {{-- update phone No modal --}}
                             <div id="updatePhoneNo{{ $item->sms_id }}" class="modal fade" data-backdrop="static" data-keyboard="false" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info-400">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title text-bold text-center">UPDATE PHONE NUMBERS</h5>
                                        </div>

                                        <form action="{{ route('bulk_sms.update',['bulk_sm'=>$item->sms_id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        <div class="modal-body">
                                           <div class="row form-group">
                                            <div class="form-group col-md-6">
                                                <label>Company Phone Number <i class=" text-danger">*</i></label>
                                                <input type="text" name="companyPhone" class="form-control" placeholder=" Enter phone Number" required value="{{ $item->companyPhone }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Contact Person Phone Number <i class=" text-danger">*</i></label>
                                                <input type="text" name="personalPhone" class="form-control" placeholder=" Enter phone Number" value="{{ $item->personalPhone }}">
                                            </div>
                                           </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type=" submit" class="btn btn-primary btn-ladda btn-ladda-spinner"> Update Company</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                             </div>
                             {{-- //end update phone numbers --}}

                         </tr>
                         @php $i++; @endphp
                         @endforeach

                     </tbody>
                 </table>
             </div>

             <div class="tab-pane fade has-padding" id="sent">
                 <table class=" table table-bordered datatable-button-html5-columns table-striped">
                     <thead>
                         <tr>
                            <th class=" text-bold">CUSTOMER_NAME</th>
                            <th class=" text-bold">CUSTOMER_PHONE_NO</th>
                            <th class=" text-bold">SCHEDULED_ON</th>
                            <th class=" text-bold">STATUS</th>
                         </tr>
                     </thead>
                     <tbody>

                         @php $i=0; @endphp
                         @foreach ($sent_messages as $item)
                         <tr>
                            <td title="{{ $item->sms_body }}">{{ $item->customer_name }}</td>
                            <td>{{ $item->phone_number }}</td>
                            <td>{{ date('d-m-Y',strtotime($item->sms_sent_on)) }}</td>
                             <td>
                                @if ($item->sms_status==1)
                                <b class=" text-success">Sent</b>
                                @endif
                             </td>

                         </tr>
                         @php $i++; @endphp
                         @endforeach
                     </tbody>
             </div>

         </div>
         <!-- /tabs content -->


             </div>
         </div>
 </div>
 </x-app-layout>


  {{-- New space modal --}}
  <script>
    $("#Receivers").change(function() {
        if ( $(this).val() == "Single") { //for functional Halls

            $("#Single").show();
        }
        else
        {
            $("#Single").hide();

        }
});

</script>
