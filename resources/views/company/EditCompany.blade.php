<div class=" ">
    {{-- update Company form --}}
    <div class=" form-group col-md-6">
        <code style="font-size: 5vmin">TSRN: <b style="font-size: 5vmin">{{ $item->registrationID }}</b></code>
    </div>
    <div class=" col-md-4 text-center form-group">
        @if ($item->companyLogo==Null)
        <img src="{{ asset('assets/images/cover.jpg') }}" height="100" width="100" style="border: 1px solid blue;border-radius:100%;">
        @else
        <img src="{{ asset('profile_images/'.$item->companyLogo) }}" height="100" width="100" style="border: 1px solid blue;border-radius:100%;">  
        @endif
        
    </div>
    <div class=" form-group col-md-2">
        <label>Company / Institution Logo</label>
        <input type="file" name="companyLogo" class=" form-control">
        <span class=" text-danger">@error('companyLogo') {{ $message }} @enderror</span>
    </div>

    {{-- <div class=" col-md-12 form-group">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="notifyUser" class="styled" value="1" style=" width: 20px;height: 20px;">
                Notify user about registration (This will send registration SMS to user)
            </label>
        </div>
    </div> --}}
    
    <div class=" col-md-12 form-group zoom" style="background-color: rgb(237, 237, 243);">
        <h4 class=" text-bold text-primary text-center"> PRIMARY INFORMATIONS</h4>
    </div>

    
    <div class=" row form-group col-md-12">
        <div class=" col-md-4">
            <label>Company Name<i class=" text-danger">*</i></label>
            <input type="text" name="company_name" class=" form-control required" placeholder="Company Name"
                value="{{ $item->companyName }}" required>
            <span class="text-danger"> @error('company_name')
                    {{ $message }}
                @enderror
            </span>
        </div>
        <div class=" col-md-4">
            <label>Company Email</label>
            <input type="text" name="company_email" class=" form-control"
                placeholder=" Company Email ie. example@gmail.com" value="{{ $item->companyEmail }}" required>
            <span class="text-danger"> @error('company_email')
                    {{ $message }}
                @enderror
            </span>
        </div>
        <div class=" col-md-4">
            <label>Campany Phone<i class=" text-danger">*</i></label>
            <input type="text" name="company_phone" class="form-control" placeholder="255xxxxxxxxx"
                value="{{ $item->companyPhone }}" required>
            <span class="text-danger"> @error('company_phone')
                    {{ $message }}
                @enderror
            </span>
        </div>
    </div>

    <div class=" row form-group col-md-12">
        <div class=" col-md-4">
            <label>Company Sector<i class=" text-danger">*</i></label>
            <select name="company_sector" class="bootstrap-select " data-live-search="true" data-width="100%"
                placehoder="Select Company category" required>
                <option value="{{ $item->companySector }}">{{ $item->sectorName }}</option>
                <optgroup label="Company Sectors">
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->sectorID }}">{{ $sector->sectorName }}</option>
                    @endforeach
                </optgroup>
            </select>
            <span class="text-danger"> @error('company_sector')
                    {{ $message }}
                @enderror
            </span>
        </div>

        <div class="col-md-4">
            <label>Company Category<i class=" text-danger">*</i></label>
            <select name="company_category" class="bootstrap-select " data-live-search="true" data-width="100%"
                placehoder="Select Company category">
                <option value="{{ $item->companyCategory }}">
                    @if ($item->companyCategory == 1)
                        Government Institution
                    @elseif ($item->companyCategory == 2)
                        Private Company
                    @else
                        SMEs
                    @endif
                </option>
                <option value="1">Government Institution</option>
                <option value="2">Private Companies</option>
                <option value="3">SMEs</option>
            </select>
            <span class="text-danger"> @error('company_category')
                    {{ $message }}
                @enderror
            </span>
        </div>

        <div class=" col-md-4">
            <label>Company Originality (Location) <i class=" text-danger">*</i></label>
            <select name="company_location" class="bootstrap-select " data-live-search="true" data-width="100%"
                placehoder="Select Company category" id="country" required>
                <option value="{{ $item->companyLocation }}">{{ $item->companyLocation }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->country_name }}">{{ $country->country_name }}</option>
                @endforeach
            </select>
            <span class="text-danger"> @error('company_location')
                    {{ $message }}
                @enderror
            </span>
        </div>
    </div>

    <div class=" row form-group col-md-12">
        <div class=" col-md-4" id="region">
            <label>Specify Region<i class=" text-danger">*</i></label>
            {{-- <input type="text" name="region" class="form-control" placeholder="Enter Region i.e Dar es salaam" value="{{ $item->region }}"> --}}
            <!--- Tanzania, United Republic of states -->
            <select id="country-state" name="region" class=" bootstrap-select" data-live-search="true"
                data-width="100%">
                <option value="{{ $item->region }}">{{ $item->region }}</option>
                <option value="Arusha Region">Arusha Region</option>
                <option value="Dar es Salaam Region">Dar es Salaam Region</option>
                <option value="Dodoma Region">Dodoma Region</option>
                <option value="Geita Region">Geita Region</option>
                <option value="Iringa Region">Iringa Region</option>
                <option value="Kagera Region">Kagera Region</option>
                <option value="Katavi Region">Katavi Region</option>
                <option value="Kigoma Region">Kigoma Region</option>
                <option value="Kilimanjaro Region">Kilimanjaro Region</option>
                <option value="Lindi Region">Lindi Region</option>
                <option value="Manyara Region">Manyara Region</option>
                <option value="Mara Region">Mara Region</option>
                <option value="Morogoro Region">Morogoro Region</option>
                <option value="Mtwara Region">Mtwara Region</option>
                <option value="Mwanza Region">Mwanza Region</option>
                <option value="Njombe Region">Njombe Region</option>
                <option value="North Pemba Region">North Pemba Region</option>
                <option value="Pwani Region">Pwani Region</option>
                <option value="Rukwa Region">Rukwa Region</option>
                <option value="Ruvuma Region">Ruvuma Region</option>
                <option value="Shinyanga Region">Shinyanga Region</option>
                <option value="Simiyu Region">Simiyu Region</option>
                <option value="Singida Region">Singida Region</option>
                <option value="South Pemba Region">South Pemba Region</option>
                <option value="Tabora Region">Tabora Region</option>
                <option value="Tanga Region">Tanga Region</option>
                <option value="Zanzibar Central/South Region">Zanzibar Central/South Region</option>
                <option value="Zanzibar North Region">Zanzibar North Region</option>
                <option value="Zanzibar Urban/West Region">Zanzibar Urban/West Region</option>
            </select>
        </div>
    </div>


    <div class=" col-md-12" style="background-color: rgb(237, 237, 243);">
        <h4 class=" text-bold text-primary text-center zoom">COMPANY REGISTRATION INFORMATIONS</h4>
    </div>

    <div class="row form-group col-md-12">
        <div class=" col-md-4">
            <label>Busines Registration Number</label>
            <input type="text" name="registrationNo" class="form-control " placeholder="999-999-999"
                value="{{ $item->registrationNo }}" data-mask="999-999-999">
        </div>
        <div class=" col-md-4">
            <label>Taxpayer Identification Number (TIN)</label>
            <input type="text" name="tinNo" class=" form-control " placeholder="999-999-999"
                value="{{ $item->tinNo }}" data-mask="999-999-999">
        </div>

        <div class=" col-md-4">
            <label>Company Telephone</label>
            <input type="text" name="companyTele" class=" form-control input-sm"
                placeholder=" Company Telephone Number" value="{{ $item->companyTel }}">
        </div>

    </div>

    <div class="row form-group col-md-12">
        <div class=" col-md-4">
            <label>Company Address<i class=" text-danger">*</i></label>
            <div class="form-group">
                <input type="text" name="companyAddress" class=" form-control input-sm "
                    placeholder="Company Address Eg, P.O.BOX 5402" value="{{ $item->companyAddress }}" >
            </div>
        </div>
        <div class=" col-md-4">
            <label>Company Website Eg:(<i class=" text-muted">https://example.co.tz</i>)</label>
            <div class="form-group">
                <input type="url" name="website" class=" form-control input-sm"
                    placeholder="eg: https://example.co.tz" value="{{ $item->website }}">
            </div>
        </div>
    </div>



    <div class=" col-md-12" style="background-color: rgb(237, 237, 243);">
        <h4 class=" text-bold text-primary text-center zoom">COMPANY CONTACT PERSON INFORMATIONS</h4>
    </div>
    <div class=" row form-group col-md-12">
        <div class=" col-md-4">
            <label>Contact Person Name<i class=" text-danger">*</i></label>
            <input type="text" name="personalName" class=" form-control input-sm "
                placeholder="Contact Person Name" value="{{ $item->personalName }}" >
        </div>
        <div class=" col-md-4">
            <label>Contact person Phone No<i class=" text-danger">*</i></label>
            <input type="text" name="personalPhone" class=" form-control input-sm"
                placeholder="Contact Personal Phone Number" value="{{ $item->personalPhone }}" >
        </div>
        <div class=" col-md-4">
            <label>Contact Person Email<i class=" text-danger">*</i></label>
            <input type="text" name="personalEmail" class=" form-control input-sm "
                placeholder="Contact Person Email" value="{{ $item->personalEmail }}" >
        </div>
    </div>

    <div class=" row form-group col-md-12">
        <div class=" col-md-4">
            <label>Contact Person Title<i class=" text-danger">*</i></label>
            <input type="text" name="personalTitle" class=" form-control input-sm "
                placeholder="Contact Person Title" value="{{ $item->personalTitle }}" >
        </div>
        <div class=" col-md-6">
            <label>By checking check box, you agree to comply with and be bound by <a href="#"
                    data-toggle="modal" data-target="#termsAdCondition">Rules
                    and regulations</a>.<i class=" text-danger">*</i></label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="isAcceptedTerms" class="styled" required style=" width: 20px;height: 20px;"
                        @if ($item->isAcceptedTerms == 1) checked @endif>
                    I accept that the selection committee’s decision
                    is final and if selected, my firm will participate
                    in the trade fair and I accept to abide by all the <a href="#" data-toggle="modal"
                        data-target="#termsAdCondition"> Rules
                        and regulations</a> governing this participation.
                    We have adequate funds for participating in this event.

                </label>
            </div>


        </div>
    </div>

    <div class=" row form-group col-md-12">
        <div class=" col-md-12  text-right">
            <button type="submit" class="btn btn-primary text-right"> Update Compay Information</button>
        </div>
    </div>
    {{-- update company form --}}
</div>

<div id="termsAdCondition" class="modal fade" aria-modal="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-primary">

                <h4 class="modal-title text-center">
                    <b style="font-size:2.5vmin">GEITA MINERAL EXHIBITION RULES AND
                        REGULATIONS - 2024.</b></h5>

            </div>

            <div class=" modal-body">
                @include('Support.DITFRules&Regulations')
            </div>

            <div class=" modal-footer">

                <div class=" text-right">

                    <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>

                </div>

            </div>



        </div>

    </div>

</div>
