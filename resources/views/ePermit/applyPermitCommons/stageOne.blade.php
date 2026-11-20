<style>
    .checkbox1 {
        -ms-transform: scale(2);
        /* IE */
        -moz-transform: scale(2);
        -webkit-transform: scale(2);
        -o-transform: scale(2);
        transform: scale(2);
        padding: 15px;
    }
</style>
<div class="tab-pane @if ($item->applicationStatus == 1) active @endif" id="stageOne">
    <div class=" col-md-12">
        <b class=" text-danger">NOTE!</b>
        <p>1. Remember to save your application by clicking the button named <a href="#saveApplication">Save
                Application</a> at the buttom to avoid data loss.</p>
        <p>2. Fields with <i class=" text-danger">*</i> Mark are required.</p>
    </div>
    <table class="table">
        <tbody>
            <form action="{{ route('apply_permit.update', ['apply_permit' => $item->applicationId]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="source" value="1">
                <tr>
                    <td colspan="3">
                        <div class=" col-md-12 text-center p-10 text-primary"
                            style="background-color: rgb(227, 252, 250);">
                            <i>EVENT DETAILS</i>
                        </div>

                        <div class=" row form-group">
                            <div class=" col-md-6">
                                <label>Title of Trade Promotion Event <span class=" text-muted">(Eg.
                                        SabaSaba Exhibition -
                                        {{ date('Y') }})</span><i class=" text-danger">*</i></label>
                                <input type="text" name="eventTitle" class=" form-control applicationForm toUpper"
                                    placeholder="Title of Promotion Event"
                                    value="{{ $item->eventTitle ?? old('eventTitle') }}">
                                <span class="text-danger"> @error('eventTitle')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6">
                                <label>Trade Promotion Event Theme<i class=" text-danger">*</i></label>
                                <input type="text" name="eventTheme" class=" form-control applicationForm toUpper"
                                    placeholder="event Them" value="{{ $item->eventTheme ?? old('eventTheme') }}">
                                <span class="text-danger"> @error('eventTheme')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" row form-group">
                            <div class=" col-md-6">
                                <label>Trade Promotion Event Objectives<i class=" text-danger">*</i> <span
                                        class=" text-muted">(List Objectives of your Event in the text area provided
                                        bellow)</span></label>
                                <textarea name="eventObjectives" class=" form-control applicationForm" placeholder="Trade Promotion Event Objectives">{{ $item->eventObjectives ?? old('eventObjectives') }}</textarea>
                                <span class="text-danger">
                                    @error('eventObjectives')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>


                            <div class=" col-md-6">
                                <div class="form-group">
                                    <label>Promotion Event Sector<i class=" text-danger">*</i></label>
                                    <select name="eventSector" class="applicationForm form-control">
                                        <optgroup label="Sectors List">
                                            <option value="{{ $item->companySector }}">{{ $item->sectorName }}</option>
                                            @foreach ($sectors as $sector)
                                                <option value="{{ $sector->sectorID }}">
                                                    {{ $sector->sectorName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    <span class="text-danger">
                                        @error('eventSector')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>
                        </div>


                        <div class=" row form-group">
                            <div class=" col-md-6">
                                <label>Event Start From Date<i class=" text-danger">*</i></label>
                                <input type="date" name="startFrom" class="applicationForm form-control"
                                    placeholder="Event Start From Date"
                                    value="{{ date('Y-m-d', strtotime($item->startFrom ?? date('Y-m-d'))) }}">
                                <span class="text-danger"> @error('startFrom')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6">
                                <label>Event End Date<i class=" text-danger">*</i></label>
                                <input type="date" name="endDate" class=" form-control applicationForm"
                                    placeholder="Event End Date"
                                    value="{{ date('Y-m-d', strtotime($item->endDate ?? date('Y-m-d'))) }}">
                                <span class="text-danger"> @error('endDate')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class=" row form-group">
                            <div class=" col-md-6">
                                <label>Expected Number Of Local Participants<i class=" text-danger">*</i> <span
                                        class=" text-muted">(write 0 if no any)</span></label>
                                <input type="number" name="localParticipants" class="applicationForm form-control"
                                    placeholder="Number Of Local Participants"
                                    value="{{ $item->localParticipants ?? old('localParticipants') }}">
                                <span class="text-danger">
                                    @error('localParticipants')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6">
                                <label>Expected Number Of Foreign Participants<i class=" text-danger">*</i><span
                                        class=" text-muted">(write 0 if no any)</span></label>
                                <input type="number" name="foreignParticipants" oninput="checkNumber()"
                                    id="numberInput" class=" form-control applicationForm"
                                    placeholder="Number Of Foreign Participants"
                                    value="{{ $item->foreignParticipants ?? 0 }}">
                                <span class="text-danger">
                                    @error('foreignParticipants')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>


                        <div class=" row form-group">
                            <div class=" col-md-6" id="extraInput">
                                <label>Ministry of Foreign Affairs approval for
                                    foreign applicant<span class=" text-muted"> (If you expect to have foreign
                                        participants)</span></label>
                                <input type="file" name="MOFAttachment" class="form-control applicationForm">
                                <span>
                                    @error('MOFAttachment')
                                        {{ $message }}
                                    @enderror
                                </span>

                                <div class=" text-right mt-5">
                                    <a href="{{ $item->MOFAttachment ?? 0 }}" target="_newtab">
                                        @if ($item->MOFAttachment != null)
                                            View File
                                            <i class=" icon icon-eye"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div class=" col-md-6">
                                <label>Entry Fee for Visitors <i class=" text-danger">*</i><span
                                        class=" text-muted">(write 0 if no entry fee)</span></label>
                                <input type="number" name="entryFee" class=" form-control applicationForm"
                                    placeholder="Entry Fee for Visitors" value="{{ $item->entryFee ?? 0 }}">
                                <span class="text-danger"> @error('entryFee')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>


                        <div class=" row form-group ">
                            <div class=" col-md-6">
                                <label>Website / System for Managing This
                                    Event</label>
                                <input type="text" name="eventWebsite" onfocus="this.setSelectionRange(8, 8);"
                                    class=" form-control applicationForm" placeholder="Company Website"
                                    value="{{ $item->eventWebsite ?? 'https://' }}">
                                <span class="text-danger">
                                    @error('eventWebsite')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>


                        </div>

                        <div class=" col-md-12 text-center p-10 text-primary"
                            style="background-color: rgb(227, 252, 250);">
                            <i>Location of Exhibition Venue</i>
                        </div>
                        <div class=" row form-group">
                            <div class=" col-md-6">
                                <label>Venue Name & Adress <i class=" text-danger">*</i></label>
                                <input type="text" name="venueAddress" placeholder="Venue Address"
                                    value="{{ $item->venueAddress ?? old('venueAddress') }}"
                                    class=" form-control applicationForm toUpper">
                                <span class="text-danger">
                                    @error('venueAddress')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class=" col-md-6">
                                <label>Type Of Venue <i class=" text-danger">*</i></label>
                                <select name="typeOfBuilding" class=" form-control applicationForm">
                                    <option value="{{ $item->typeOfBuilding }}">
                                        {{ $item->typeOfBuilding ?? '- - - Select Type of Venue - - -' }}</option>
                                    <option value="INDOOR">INDOOR</option>
                                    <option value="OUTDOOR">OUTDOOR</option>
                                    <option value="BOTH (OUTDOOR AND INDOOR)">BOTH (OUTDOOR AND INDOOR)</option>
                                </select>
                                <span class=" text-danger">
                                    @error('typeOfBuilding')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>


                        <div class=" row form-group">

                            <div class=" col-md-6">
                                <label>Venue In charge<i class=" text-danger">*</i> <span
                                        class=" text-muted">(write Personal / Institution Name)</span></label>
                                <input type="text" name="venueOwner" class=" form-control applicationForm toUpper"
                                    placeholder="Owner of Exhibition Venue"
                                    value="{{ $item->venueOwner ?? old('venueOwner') }}">
                                <span class=" text-danger">
                                    @error('venueOwner')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6">
                                <label>Contact Person Phone NUmber<i class=" text-danger">*</i><i
                                        class=" text-muted"></i> <span class=" text-muted">(Must be valid, can be
                                        called for venue booking verification)</span></label>
                                <input type="text" name="venueOwnerAddress" class=" form-control applicationForm"
                                    placeholder="Address of owner"
                                    value="{{ $item->venueOwnerAddress ?? old('venueOwnerAddress') }}">
                                <span class=" text-danger">
                                    @error('venueOwnerAddress')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        @if ($item->allowedToEdit == 1)
                            <div class=" col-md-6 text-right">
                                <button id="saveApplication" type="submit"
                                    title="Save your Application to avoid Loosing your Data."
                                    class="btn btn-primary zoom btn-rounded btn-ladda btn-ladda-spinner ">Save
                                    Application <i class=" icon-arrow-right13"></i></button>
                            </div>


                            <div class=" col-md-6 text-right">

                                @if ($submitForReview->nullValues > 0)
                                    <button type="button" class=" btn btn-warning" data-placement="left"
                                        data-popup="popover" title="Message" data-trigger="hover"
                                        data-content="All field required, must be submited to allow you submit for review">
                                        {{ $submitForReview->nullValues }} Fields
                                        Remained To Submit for
                                        Review<i class=" icon-arrow-right32"></i>
                                    </button>
                                @elseif ($submitForReview->nullValues == 0)
                                    <button type="button" class=" btn btn-success" data-toggle="modal"
                                        data-target="#submitApplication">
                                        Application Completed, Submit for Review<i
                                            class=" icon-arrow-right32"></i></button>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            </form>

        </tbody>
    </table>
</div>


{{-- add role modal --}}
<form data-action="{{ route('apply_permit.update', ['apply_permit' => $item->applicationId]) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="submitApplication" class="modal fade" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">CONFIRM INFORMATION SUBMITED</h5>
                </div>
                <div class="modal-body">
                    <div class="">
                        <input type="hidden" name="source" value="2">

                        <div class="checkbox" style="font-size: 18px;">
                            <label>
                                <input type="checkbox" class=" checkbox1" name="contract" class="styled" required>
                                I / We hereby declare that the particulars given in this application are, to the best of
                                my / our
                                knowledge and that I / we shall comply with the Tanzania Development Authority act and
                                all
                                other
                                applicable laws.
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                            class="ladda-label">Submit Application for Review</span></button>
                </div>
            </div>
        </div>
    </div>
</form>
{{--  end  --}}


<script>
    $(function() {
    $('.toUpper').keyup(function() {
        this.value = this.value.toUpperCase();
    });
});

    @if ($item->allowedToEdit == 0)
        const inputs = document.querySelectorAll('.applicationForm');
        inputs.forEach(input => input.disabled = true);
    @endif
</script>
