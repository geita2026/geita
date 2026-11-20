<x-app-layout>
    <title>{{ env('APP_NAME') }} | Events Participants</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                <li class="active">Participant registration</li>
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
                    <h4 class=" text-center">TRADE PROMOTION EVENT PARTICIPANTS REGISTRATION LIST</h4>
                </div>
                <div class=" col-md-12 form-group">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class=" text-danger">{{ $error }} ,</span>
                        @endforeach
                    @endif
                </div>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-primary" data-toggle="modal"
                        data-target="#AddParticipants">Add
                        Event Participant <i class=" icon-plus2"></i></button>
                </div>
                <div class=" form-group">
                    <form action="{{ route('event_participants.index') }}" method="POST">
                        @csrf
                        @method('GET')
                        <select name="eventNumber" class=" form-control" onchange="this.form.submit()">
                            <option> - - - Select Promotion Event - - - </option>
                            @foreach ($events as $item)
                                <option value="{{ $item->eventNumber }}"> {{ $item->eventTitle }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class=" form-group">
                    <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold">PARTICIPATION #</th>
                                <th class=" text-bold">FULL NAME</th>
                                <th class=" text-bold">EMAIL ADDRESS</th>
                                <th class=" text-bold">PHONE NUMBER</th>
                                <th class=" text-bold">COMPANY NAME</th>
                                {{-- <th class=" text-bold">COMPANY PROFILE</th> --}}
                                <th class=" text-bold">COMPANY LOGO</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($participants as $item)
                                <tr>
                                    <td>{{ $item->participantId }}</td>
                                    <td>{{ $item->participantName }}</td>
                                    <td>{{ $item->participantEmail }}</td>
                                    <td>{{ $item->participantPhone }}</td>
                                    <td>{{ $item->participantCompany }}</td>
                                    {{-- <td>{{ $item->companyProfile }}</td> --}}


                                    <td class=" text-center">
                                        @if ($item->companyLogo != null)
                                            <img src="{{ $item->companyLogo }}" height="50" width="50"
                                                style="border: 1px solid rgb(190, 188, 188);border-radius:100%;">
                                        @else
                                            <b>NULL</b>
                                        @endif

                                    </td>

                                    <td>
                                        <a data-toggle="modal" data-target="#EditParticipant{{ $item->participantId }}"
                                            class=" btn btn-primary">View Participant <i
                                                class=" icon icon-eye"></i></a>
                                    </td>

                                    {{-- edit role modal --}}
                                    <div id="EditParticipant{{ $item->participantId }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h2 class="modal-title text-center">VIEW TRADE PROMOTION EVENT PARTICIPANT
                                                    </h2>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('update_participant') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="participantId"
                                                            value="{{ $item->participantId }}">
                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>participant Name<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="participantName"
                                                                    class=" form-control" placeholder=" participantName"
                                                                    value="{{ $item->participantName }}" readonly>
                                                                <span class=" text-danger">
                                                                    @error('participantName')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>participant Email<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="participantEmail"
                                                                    class=" form-control"
                                                                    placeholder=" participantEmail"
                                                                    value="{{ $item->participantEmail }}" readonly>
                                                                <span class=" text-danger">
                                                                    @error('participantEmail')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>participant Phone<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="participantPhone"
                                                                    class=" form-control"
                                                                    placeholder=" participantPhone"
                                                                    value="{{ $item->participantPhone }}" readonly>
                                                                <span class=" text-danger">
                                                                    @error('participantPhone')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>participant Website</label>
                                                                <input type="url" name="participantWebsite"
                                                                    class=" form-control"
                                                                    placeholder=" participantWebsite"
                                                                    value="{{ $item->participantWebsite }}" readonly>
                                                                <span class=" text-danger">
                                                                    @error('participantWebsite')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>participant Title<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="participantTitle"
                                                                    class=" form-control"
                                                                    placeholder=" participantTitle"
                                                                    value="{{ $item->participantTitle }}" readonly>
                                                                <span class=" text-danger">
                                                                    @error('participantTitle')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>participant Company<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="participantCompany"
                                                                    class=" form-control"
                                                                    placeholder=" participantCompany"
                                                                    value="{{ $item->participantCompany }}" readonly>
                                                                <span class=" text-danger">
                                                                    @error('participantCompany')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class=" row">

                                                            <div class=" form-group col-md-12">
                                                                <label>company Profile</label>
                                                                <textarea name="companyProfile" class=" form-control" placeholder=" companyProfile here" readonly>{{ $item->companyProfile }}</textarea>
                                                                <span class=" text-danger">
                                                                    @error('companyProfile')
                                                                        {{ $message }}
                                                                    @enderror
                                                                </span>
                                                            </div>
                                                        </div>


                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>Participant Rank</label>
                                                                <input type="number" name="participantRank"
                                                                    value="{{ $item->participantRank }}"
                                                                    class=" form-control" readonly>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Participant Sector</label>
                                                                <input type="text" name="sector"
                                                                    placeholder="Participant Sector"
                                                                    value="{{ $item->sector }}" class=" form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                        data-dismiss="modal">Close</button>
                                                   
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit role modal -->

                                    {{-- delete modal --}}
                                    <div id="DeleteParticipant{{ $item->participantId }}" class="modal fade"
                                        aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete this
                                                        Participant ?
                                                        ?</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('event_participants.destroy', ['event_participant' => $item->participantId]) }}"
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
    </div>
    <!-- /dashboard content -->

    {{-- add role modal --}}
    <form data-action="{{ route('event_participants.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="AddParticipants" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h2 class="modal-title text-bold">REGISTER TRADE PROMOTION EVENT PARTICIPANTS</h2>
                    </div>
                    <div class="modal-body">
                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>participant Name<i class=" text-danger">*</i></label>
                                <input type="text" name="participantName" class=" form-control"
                                    placeholder=" participantName" value="{{ old('participantName') }}" required>
                                <span class=" text-danger">
                                    @error('participantName')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>participant Email<i class=" text-danger">*</i></label>
                                <input type="text" name="participantEmail" class=" form-control"
                                    placeholder=" participantEmail" value="{{ old('participantEmail') }}">
                                <span class=" text-danger">
                                    @error('participantEmail')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>participant Phone<i class=" text-danger">*</i></label>
                                <input type="text" name="participantPhone" class=" form-control"
                                    placeholder=" participantPhone" value="{{ old('participantPhone') }}" required>
                                <span class=" text-danger">
                                    @error('participantPhone')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>participant Website</label>
                                <input type="url" name="participantWebsite" class=" form-control"
                                    placeholder=" participantWebsite" value="{{ old('participantWebsite') }}">
                                <span class=" text-danger">
                                    @error('participantWebsite')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>participant Title<i class=" text-danger">*</i></label>
                                <input type="text" name="participantTitle" class=" form-control"
                                    placeholder=" participantTitle" value="{{ old('participantTitle') }}" required>
                                <span class=" text-danger">
                                    @error('participantTitle')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>participant Company<i class=" text-danger">*</i></label>
                                <input type="text" name="participantCompany" class=" form-control"
                                    placeholder=" participantCompany" value="{{ old('participantCompany') }}">
                                <span class=" text-danger">
                                    @error('participantCompany')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>participant Profile</label>
                                <textarea name="participantProfile" class=" form-control" placeholder=" participantProfile here">{{ old('participantProfile') }}</textarea>
                                <span class=" text-danger">
                                    @error('participantProfile')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>company Profile</label>
                                <textarea name="companyProfile" class=" form-control" placeholder=" companyProfile here">{{ old('companyProfile') }}</textarea>
                                <span class=" text-danger">
                                    @error('companyProfile')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>participantPhoto</label>
                                <input type="file" name="participantPhoto" class=" form-control"
                                    placeholder=" participantPhoto" value="{{ old('participantPhoto') }}">
                                <span class=" text-danger">
                                    @error('participantPhoto')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>companyLogo</label>
                                <input type="file" name="companyLogo" class=" form-control"
                                    placeholder=" companyLogo" value="{{ old('companyLogo') }}">
                                <span class=" text-danger">
                                    @error('companyLogo')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>Participant Rank</label>
                                <input type="number" name="participantRank" value="100" class=" form-control">
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Participant Sector</label>
                                <input type="text" name="sector" value="{{ old('sector') }}"
                                    class=" form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Register Participant</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}

</x-app-layout>
