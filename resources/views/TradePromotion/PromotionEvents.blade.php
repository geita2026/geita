<x-app-layout>
    <title>{{ env('APP_NAME') }} | Promotion Events</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                <li class="active">Promotion Events</li>
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
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">TRADE PROMOTION EVENTS</h4>
                </div>

                <div class=" col-md-12 form-group">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class=" text-danger">{{ $error }} ,</span>
                        @endforeach
                    @endif
                </div>

                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-primary" data-toggle="modal" data-target="#add_role">Add
                        Event <i class=" icon-plus2"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">EVENT TITLE</th>
                            <th class=" text-bold">EVENT PARTICIPANTS</th>
                            <th class=" text-bold">START FROM</th>
                            <th class=" text-bold">END TO</th>
                            <th class=" text-bold">EVENT REG DEADLINE</th>
                            <th class=" text-bold">EVENT POSTER</th>
                            <th class=" text-bold">EVENT STATUS</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($promotionEvents as $item)
                            <tr>
                                <td>{{ $item->eventNumber }}</td>
                                <td class=" text-uppercase">{{ $item->eventTitle }}</td>
                                <td>{{ $item->eventParticipants }}</td>
                                <td>{{ $item->startFrom }}</td>
                                <td>{{ $item->endTo }}</td>
                                <td>{{ $item->registrationDeadline }}</td>

                                <td class=" text-center">

                                    <img src="{{ $item->eventPoster }}" height="50" width="50">
                                </td>
                                <td class=" text-center">
                                    @if ($item->eventStatus == 0)
                                        <label class=" label label-danger">INACTIVE</label>
                                    @elseif ($item->eventStatus == 1)
                                        <label class=" label label-success">ACTIVE</label>
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
                                                    <a data-toggle="modal" data-target="#editEvent{{ $item->eventId }}"
                                                        class="text-primary">Update Event <i
                                                            class=" icon-pencil7"></i></a>
                                                </li>
                                                <div class=" divider"></div>
                                                <li>
                                                    <a data-toggle="modal"
                                                        data-target="#registrationLink{{ $item->eventId }}"
                                                        class="text-primary">RegistrationLink <i
                                                            class=" icon-user-plus"></i></a>
                                                </li>
                                                <div class=" divider"></div>
                                                <li>
                                                    <a href="" data-toggle="modal"
                                                        data-target="#delete_permission{{ $item->eventId }}"
                                                        class="text-danger"> Delete Event <i class=" icon-bin"></i></a>
                                                    <a class="text-danger btn"> </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>


                                <div id="registrationLink{{ $item->eventId }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h2 class="modal-title text-center">UPDATE TRADE PROMOTION EVENT</h2>
                                            </div>
                                            <div class="modal-body">
                                               <input type="text" id="myInput" value="{{ env('APP_URL').'/tpe/'.$item->eventNumber }}" class=" form-control">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" onclick="copyText()">Copy Link <i class=" icon-copy"></i></button>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>

                                {{-- edit role modal --}}
                                <div id="editEvent{{ $item->eventId }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h2 class="modal-title text-center">UPDATE TRADE PROMOTION EVENT</h2>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('promotion_events.update', ['promotion_event' => $item->eventId]) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class=" form-group">
                                                        <label>Promotion Event Title<i
                                                                class=" text-danger">*</i></label>
                                                        <input type="text" name="eventTitle" class=" form-control"
                                                            placeholder=" Event Title"
                                                            value="{{ $item->eventTitle }}">
                                                        <span class=" text-danger">
                                                            @error('eventTitle')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Promotion Event Participants<i
                                                                class=" text-danger">*</i></label>
                                                        <input type="text" name="eventParticipants"
                                                            class=" form-control" placeholder=" Event Participants"
                                                            value="{{ $item->eventParticipants }}">
                                                        <span class=" text-danger">
                                                            @error('eventParticipants')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Promotion Event Description<i
                                                                class=" text-danger">*</i></label>
                                                        <textarea name="eventDescription" class=" form-control" placeholder=" Event Participants">{{ $item->eventDescription }}</textarea>
                                                        <span class=" text-danger">
                                                            @error('eventDescription')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Promotion Event Poster</label>
                                                        <input type="file" name="eventPoster"
                                                            class=" form-control" placeholder=" Event Participants"
                                                            value="{{ $item->eventPoster }}">
                                                        <span class=" text-danger">
                                                            @error('eventPoster')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class=" row">
                                                        <div class=" form-group col-md-6">
                                                            <label>Event Start From<i
                                                                    class=" text-danger">*</i></label>
                                                            <input type="date" name="startFrom"
                                                                class=" form-control" placeholder=" Event Start From"
                                                                value="{{ $item->startFrom }}">
                                                            <span class=" text-danger">
                                                                @error('startFrom')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Event End To<i class=" text-danger">*</i></label>
                                                            <input type="date" name="endTo"
                                                                class=" form-control" placeholder=" Event End To"
                                                                value="{{ $item->endTo }}">
                                                            <span class=" text-danger">
                                                                @error('endTo')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class=" row">
                                                        <div class="form-group col-md-6">
                                                            <label>Registration Deadline <i
                                                                    class=" text-danger">*</i></label>
                                                          
                                                            <input type="date" name="registrationDeadline"
                                                                class=" form-control" placeholder=" Event Title"
                                                                value="{{ date('Y-m-d',strtotime($item->registrationDeadline)) }}">
                                                            <span class=" text-danger">
                                                                @error('registrationDeadline')
                                                                    {{ $message }}
                                                                @enderror
                                                            </span>
                                                        </div>
                                                        <div class=" form-group col-md-6">
                                                            <label>Event Status</label>
                                                            <select name="eventStatus" class=" form-control">
                                                                @if ($item->eventStatus == 0)
                                                                    <option value="0">Inactive</option>
                                                                @elseif ($item->eventStatus == 1)
                                                                    <option value="1">Active</option>
                                                                @endif
                                                                <option value="0">Inactive</option>
                                                                <option value="1">Active</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /edit role modal -->

                                {{-- delete modal --}}
                                <div id="delete_permission{{ $item->eventId }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete this
                                                    Event ?
                                                    ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('promotion_events.destroy', ['promotion_event' => $item->eventId]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal"
                                                            class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-danger ml-20">Yes,
                                                            Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- DELETE --}}

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
    <!-- /dashboard content -->

    {{-- add role modal --}}
    <form data-action="{{ route('promotion_events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h2 class="modal-title text-bold">REGISTER TRADE PROMOTION EVENT</h2>
                    </div>
                    <div class="modal-body">
                        <div class=" form-group">
                            <label>Promotion Event Title<i class=" text-danger">*</i></label>
                            <input type="text" name="eventTitle" class=" form-control" placeholder=" Event Title"
                                value="{{ old('eventTitle') }}">
                            <span class=" text-danger">
                                @error('eventTitle')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class=" form-group">
                            <label>Promotion Event Participants<i class=" text-danger">*</i></label>
                            <input type="text" name="eventParticipants" class=" form-control"
                                placeholder=" Event Participants" value="{{ old('eventParticipants') }}">
                            <span class=" text-danger">
                                @error('eventParticipants')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class=" form-group">
                            <label>Promotion Event Description<i class=" text-danger">*</i></label>
                            <textarea name="eventDescription" class=" form-control" placeholder=" Event Participants">{{ old('eventDescription') }}</textarea>
                            <span class=" text-danger">
                                @error('eventDescription')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class=" form-group">
                            <label>Promotion Event Poster</label>
                            <input type="file" name="eventPoster" class=" form-control"
                                placeholder=" Event Participants" value="{{ old('eventPoster') }}">
                            <span class=" text-danger">
                                @error('eventPoster')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>Event Start From<i class=" text-danger">*</i></label>
                                <input type="date" name="startFrom" class=" form-control"
                                    placeholder=" Event Start From" value="{{ old('startFrom') }}">
                                <span class=" text-danger">
                                    @error('startFrom')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Event End To<i class=" text-danger">*</i></label>
                                <input type="date" name="endTo" class=" form-control"
                                    placeholder=" Event End To" value="{{ old('endTo') }}">
                                <span class=" text-danger">
                                    @error('endTo')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" form-group">
                            <label>Registration Deadline <i class=" text-danger">*</i></label>
                            <input type="date" name="registrationDeadline" class=" form-control"
                                placeholder=" Event Title" value="{{ old('registrationDeadline') }}">
                            <span class=" text-danger">
                                @error('registrationDeadline')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Register Event</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}

</x-app-layout>
<script>
    function copyText() {
      // Select the text field
      const inputField = document.getElementById("myInput");
      inputField.select();
      inputField.setSelectionRange(0, 99999); // For mobile devices
  
      // Copy the text inside the text field
      navigator.clipboard.writeText(inputField.value)
        .then(() => {
          alert("Text copied to clipboard: " + inputField.value);
        })
        .catch((err) => {
          alert("Failed to copy text: ", err);
        });
    }
  </script>