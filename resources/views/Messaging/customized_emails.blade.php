<x-app-layout>
    <title>{{ env('APP_NAME') }} | Bulk-Emails</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('messaging.index') }}"><i class=" icon-envelop position-left"></i>MESSAGING</a></li>
                <li class="active">Messaging - Dashboard</li>
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
                <h6 class="panel-title  text-bold text-uppercase"> MAILING DASHBOARD</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload" onclick="updateDiv()"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>

            <div class=" panel-body">


                {{--  error message  --}}
                <div class=" form-group">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class="text-danger ml-10">{{ $error }}</span>
                        @endforeach
                    @endif
                </div>


                <div id="delete-all" class="modal fade" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-warning-400">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title text-bold text-center">REMOVE ALL EMAIL FROM DRAFT EMAILS</h5>
                            </div>

                            <form action="{{ route('messaging.destroy', ['messaging' => 'delete-messages']) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-body">

                                    <p class=" text-danger"><i class=" icon-warning22"></i>
                                        Are you sure yo want to delete All message from draft ?
                                    </p>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button type=" submit" class="btn btn-warning btn-ladda btn-ladda-spinner">Yes,
                                        Delete <i class=" icon-cross3"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /basic modal -->


                <div class=" row form-group">

                    <div class=" form-group col-md-2">
                        <a class=" icon-pencil btn btn-default border-blue-300" data-toggle="modal"
                            data-target="#compose"> Compose Email</a>
                    </div>


                    <div class=" col-md-2 form-group mt-20 ">
                        <!-- Top right menu -->

                        <ul class="fab-menu fab-menu-absolute fab-menu-top-left" data-fab-toggle="hover"
                            id="fab-menu-affixed-demo-right">

                            <li>

                                <a class="fab-menu-btn btn bg-teal-400 btn-float btn-rounded btn-icon">

                                    <i class="fab-icon-open  icon-envelop3"></i>

                                    <i class="fab-icon-close icon-cross2"></i>

                                </a>



                                <ul class="fab-menu-inner">

                                    <li>
                                        <div data-fab-label="Compose Email">

                                            <a data-toggle="modal" data-target="#compose"
                                                class="btn bg-blue-400 btn-rounded btn-icon btn-float">
                                                <i class=" icon-pencil7"></i>
                                            </a>

                                        </div>
                                    </li>

                                    <li>

                                        <div data-fab-label="Clear All Draft Messages">

                                            <a data-toggle="modal" data-target="#delete-all"
                                                class="btn btn-warning btn-rounded btn-icon btn-float">
                                                <i class=" icon-cross3"></i>

                                            </a>
                                            {{-- <span class="badge  text-default bg-danger"></span> --}}

                                        </div>

                                    </li>
                                </ul>

                            </li>

                        </ul>
                        <!-- /top right menu -->
                    </div>
                </div>


                <!-- Basic modal -->
                <div id="compose" class="modal fade" data-backdrop="static" data-keyboard="false"
                    data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-full">
                        <div class="modal-content">
                            <div class="modal-header bg-teal-400">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title text-bold text-center">COMPOSE EMAIL FOR SPECIFIC GROUP OF
                                    COMPANIES TO SEND CUSTOMERSED EMAILS</h5>

                            </div>

                            <form action="{{ route('messaging.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <label class=" text-left">
                                        Emails will be sent From: <b
                                            class=" text-success">{{ env('MAIL_FROM_ADDRESS') }}</b></i>
                                    </label>

                                    <div class=" row">

                                        {{--  body her  --}}
                                        <div class="form-group col-md-6">
                                            <label><span class="text-bold">Select Recipicients Group </span> <i
                                                    class=" text-danger">*</i></label>
                                            <select name="sector_name" class=" bootstrap-select" data-live-search="true"
                                                data-width="100%" placehoder="Select department" required autofocus>
                                                <option value=""> - - - - Select Company group - - - -</option>

                                                <option value="allCompanies" class=" text-uppercase"> All Companies
                                                </option>
                                                <option value="allExhibitors" class=" text-uppercase"> All Exhibitors
                                                </option>

                                                <optgroup label="Select Companies By Group">
                                                    <div class=" line-e"></div>
                                                    @foreach ($sectors as $item)
                                                        <option value="{{ $item->sectorID }}"
                                                            class=" text-bold text-uppercase">{{ $item->sectorName }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label> BCC <i class=" text-muted">( User to get Copy of email)</i></label>
                                            <input type="text" name="blind_carbon_copy" class=" form-control"
                                                placeholder=" Enter BCC Email" value="{{ old('blind_carbon_copy') }}">
                                        </div>

                                        {{--  end here  --}}
                                        <div class="form-group col-md-6">
                                            <label>Email Subject (max 50 characters) <i
                                                    class=" text-danger">*</i></label>
                                            <input type="text" name="mail_subject" class="form-control"
                                                placeholder=" Enter subject or Title of the Email" required
                                                value="{{ old('mail_subject') }}">
                                        </div>
                                        {{--  end here  --}}

                                        <div class="form-group col-md-6">
                                            <label>Message Title <i class=" text-danger">*</i></label>
                                            <input type="text" name="message_title" class=" form-control"
                                                placeholder=" Enter nail title" required
                                                value="{{ old('message_title') }}">
                                        </div>

                                        {{--  end here  --}}
                                        <div class="form-group col-md-12">
                                            <label>Email Message (max 100 characters) </label>
                                            <textarea name="body" class=" form-control" placeholder=" Type your message here" required>{{ old('body') }}</textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Attach file <i class=" text-muted">(maximum file size 2
                                                    MB)</i></label>
                                            <input type="file" name="file_name" class=" form-control"
                                                value="{{ old('file_name') }}">

                                        </div>
                                        {{--  end here  --}}
                                        <div class=" form-group col-md-6">
                                            <label>Select Date Email to be sent <i class=" text-danger">*</i></label>
                                            <input type="date" name="scheduled_at" class="form-control date-step"
                                                placeholder="Set when emails must be sent" required>
                                            <span class="help-block">This date will act as customer schedule for
                                                sending composed emails </span>
                                        </div>

                                    </div>


                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default border-blue-300 text-warning"
                                        data-dismiss="modal">Close <i class=" icon-cross2"></i></button>
                                    <button type=" submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Send
                                        Email <i class=" icon-arrow-right13"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /basic modal -->


                {{-- <div class=" form-group">
            <a href="{{ route('messaging.edit',['messaging']) }}" class=" icon-pencil btn btn-default border-blue-300"  > Preview page</a>
        </div> --}}


                <!-- Tabs -->
                <ul class="nav nav-lg nav-tabs nav-justified no-margin no-border-radius bg-primary-400">
                    <li class="active">
                        <a href="#draft" class="text-size-small text-uppercase text-bold" data-toggle="tab">
                            <i class=" icon-inbox"></i> Draft Emails
                        </a>
                    </li>
                </ul>
                <!-- /tabs -->

                <!-- Tabs content -->
                <div class="tab-content">
                    <div class="tab-pane active fade in has-padding" id="draft">
                        <table class=" table table-bordered datatable-button-html5-columns table-striped">
                            <thead>
                                <tr>
                                    <th class=" text-bold">Customer Name</th>
                                    <th class=" text-bold">Customer Email</th>
                                    <th class=" text-bold">Message</th>
                                    <th class=" text-bold">Sheduled At</th>
                                    <th class=" text-bold">
                                        Actions
                                        {{-- <a href="{{ route('messaging.edit',['messaging']) }}" class=" btn btn-default border-primary text-bold text-success">Send All <i class=" icon-arrow-right13"></i></a> --}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>


                                @php $i=0; @endphp
                                @foreach ($draft_messages as $item)
                                    <tr>
                                        <td>{{ $item->receiverName }}</td>
                                        <td>{{ $item->emailTo }}</td>
                                        <td><b>{{ $item->emailSubject }}</b><br>{{ $item->emailContent }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->scheduledAt)) }}</td>
                                        <td>
                                            <a href="#"
                                                class=" btn btn-default border-danger text-bold text-danger"
                                                data-toggle="modal" data-target="#{{ $item->emailId }}"> Remove <i
                                                    class=" icon-cross2"></i></a>
                                        </td>


                                        <div id="{{ $item->emailId }}" class="modal fade" data-backdrop="static"
                                            data-keyboard="false">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning-400">
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                        <h5 class="modal-title text-bold text-center">REMOVE EMAIL FROM
                                                            DRAFT EMAILS</h5>
                                                    </div>

                                                    <form
                                                        action="{{ route('messaging.destroy', ['messaging' => $item->emailId]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-body">

                                                            <p>
                                                                Are you sure yo want to delete message ?
                                                            </p>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-link"
                                                                data-dismiss="modal">Close</button>
                                                            <button type=" submit"
                                                                class="btn btn-warning btn-ladda btn-ladda-spinner">Yes,
                                                                Delete <i class=" icon-cross3"></i></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /basic modal -->

                                    </tr>
                                    @php $i++; @endphp
                                @endforeach


                                @if ($i == 0)
                                    <tr class=" alert-danger">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>

                                        <td class=" text-danger"></td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /tabs content -->


            </div>
        </div>
    </div>

    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>
