<div class=" table-responsive">
    <div class=" form-group col-md-12">
        <table class="table table-bordered">
            <tbody>

                @if (Auth::user()->account_type == 1)
                    <tr>
                        <td colspan="4" class=" text-right">

                            @if ($item->tinNo ?? false != null)
                                <form action="{{ route('update_company') }}" method="post">@csrf
                                    <input type="hidden" name="TIN" value="{{ $item->tinNo }}"
                                        placeholder="Enter Tax Identification Number" class=" form-control">
                                    <button type="submit"
                                        class=" btn btn-rounded btn-primary btn-ladda btn-ladda-spinner">Verify
                                        Company From MIS
                                        <i class=" icon-arrow-right13"></i>
                                    </button>
                                </form>
                            @endif


                        </td>
                    </tr>
                @endif
                <form action="{{ route('register_company') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <tr>
                        <th colspan="2" class="active">
                            <input type="hidden" name="TSRN" value="{{ $item->registrationID ?? null }}">
                            <div class="" style="font-size: 1.5vmin">TSRN:
                                <b>{{ $item->registrationID ?? 'UN REGISTERED' }}</b>
                                @if (session('tinStatus') == 'VERIFIED')
                                    <b class=" icon-user-check text-success"></b>
                                @endif
                            </div>
                        </th>
                        <th>

                            @if ($item->companyLogo ?? false)
                                <img src="{{ asset('profile_images/' . $item->companyLogo) }}" alt=""
                                    style="width: 100px;height:60px; border-radius:2%;border:1px solid rgb(131, 130, 130);">
                            @else
                                <i class=" text-danger">
                                    Please upload the company logo in either PNG, JPG, or JPEG format, ensuring that the
                                    file size does not exceed 2 MB.
                                </i>
                            @endif
                            <br><br>
                            <input type="file" name="companyLogo">
                        </th>

                    </tr>
                  
                    <tr>
                        <td colspan="3">
                            <div class=" alert alert-info">
                                <i class=" icon-info22"></i>
                                Please ensure that the company details you provide are accurate and up-to-date. This
                                information is crucial for effective communication and compliance with regulatory
                                requirements.
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="3">
                            <div class=" row form-group">
                                <div class=" col-md-6">
                                    <label>Company / Business Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="company_name" class=" form-control"
                                        placeholder="Company Name"
                                        value="{{ $item->companyName ?? old('company_name') }}"
                                        @if (session('tinStatus') == 'VERIFIED') readonly @endif required>
                                    <span class="text-danger"> @error('company_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class=" col-md-6">
                                    <label>Company Email<i class=" text-danger">*</i></label>
                                    <input type="text" name="company_email" class=" form-control"
                                        placeholder="Company Email"
                                        value="{{ $item->companyEmail ?? old('company_email') }}" required>
                                    <span class="text-danger"> @error('company_email')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>

                            <div class=" row form-group">
                                <div class=" col-md-6">
                                    <label>Company Phone<i class=" text-danger">*</i> <span class=" text-muted">Start
                                            with
                                            country code ie, 255,254,257 . Do not include +</span></label>
                                    <input type="text" name="company_phone" class=" form-control"
                                        placeholder="Company Phone"
                                        value="{{ $item->companyPhone ?? old('company_phone') }}" required>
                                    <span class="text-danger"> @error('company_phone')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class=" col-md-6">
                                    <label>Company Address<i class=" text-danger">*</i></label>
                                    <input type="text" name="company_address" class=" form-control"
                                        placeholder="Company Address"
                                        value="{{ $item->companyAddress ?? old('company_address') }}"
                                        @if (session('tinStatus') == 'VERIFIED') readonly @endif required>
                                    <span class="text-danger"> @error('company_address')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>

                            <div class=" row form-group">
                                <div class=" col-md-6">
                                    <label>Company Website <span class=" text-muted">eg:
                                            https://yourcompany.co.tz</span></label>
                                    <input type="text" name="website" onfocus="this.setSelectionRange(8, 8);"
                                        class=" form-control" placeholder="Company Website"
                                        value="{{ $item->website ?? old('website') }}">
                                    <span class="text-danger"> @error('website')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="  col-md-6">
                                    <label>Company Sector </label>
                                    <select id="searchSectors" name="company_sector" class=" form-control">
                                        <option value="{{ $item->companySector ?? '' }}">
                                            {{ $item->sectorName ?? 'Search sector' }}
                                        </option>
                                    </select>
                                    <span class="text-danger"> @error('company_sector')
                                            {{ $message }}
                                        @enderror
                                    </span>

                                </div>
                            </div>
                            <div class=" row form-group">
                                <div class="col-md-6">
                                    <label>Country<i class=" text-danger">*</i></label>
                                    <select id="searchCountry" name="companyLocation" class=" form-control">
                                        <option value="{{ $item->companyLocation ?? '' }}">
                                            {{ $item->companyLocation ?? 'Search Countries' }}</option>
                                    </select>

                                    <span class="text-danger"> @error('country')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <label>Company Category<i class=" text-danger">*</i></label>
                                    <select name="company_category" class="form-control "
                                        placehoder="Select Company category">
                                        <option value="{{ $item->companyCategory ?? '' }}">
                                            {{ $item->companyCategory ?? 'Select Company category' }} </option>
                                        <option value="Individuals">Individuals</option>
                                        <option value="Small Traders">Small Traders</option>
                                        <option value="Medium-Sized Enterprises">Medium-Sized Enterprises</option>
                                        <option value="Large Corporates">Large Corporates</option>
                                        <option value="Government Insititution">Government Insititution</option>
                                    </select>
                                    <span class="text-danger"> @error('company_category')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class=" col-md-12 text-center p-10 text-primary"
                                style="background-color: rgb(227, 252, 250);">
                                <i>Company Registration & Compliance</i>
                            </div>
                            <div class=" row form-group">
                                <div class=" col-md-6">
                                    <label>Business / Company Registration Number</label>
                                    <input type="text" name="registrationNo" placeholder="Registration No"
                                        value="{{ $item->registrationNo ?? old('registrationNo') }}"
                                        class=" form-control" @if (session('tinStatus') == 'VERIFIED') readonly @endif>
                                    <span class="text-danger"> @error('registrationNo')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="  col-md-6">
                                    <label>Tax Payer Identification Number (TIN)</label>
                                    <input type="text" name="TIN" class=" form-control"
                                        value="{{ $item->tinNo ?? '' }}"
                                        @if (session('tinStatus') == 'VERIFIED') readonly @endif>
                                    <span class="text-danger"> @error('TIN')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>

                            <div class="row form-group">

                                <!-- Originality -->
                                <div class="col-md-6">
                                    <label>
                                        Company Type / Originality
                                        <i class="text-danger">*</i>
                                    </label>

                                    <select name="originality" class="form-control" required>
                                        <option value="">
                                            {{ $item->originality ?? 'Select Originality' }}
                                        </option>

                                        <option value="DOMESTIC"
                                            {{ ($item->originality ?? '') === 'DOMESTIC' ? 'selected' : '' }}>
                                            DOMESTIC COMPANY
                                        </option>

                                        <option value="FOREIGN"
                                            {{ ($item->originality ?? '') === 'FOREIGN' ? 'selected' : '' }}>
                                            FOREIGN COMPANY
                                        </option>

                                        <option value="JOINT_VENTURE"
                                            {{ ($item->originality ?? '') === 'JOINT_VENTURE' ? 'selected' : '' }}>
                                            JOINT VENTURE COMPANY (DOMESTIC & FOREIGN)
                                        </option>
                                    </select>

                                    <small class="form-text text-muted">
                                        Mandatory for regulatory classification, compliance verification,
                                        and determination of applicable legal and operational requirements.
                                    </small>

                                    <span class="text-danger">
                                        @error('originality')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <!-- Has Other Companies -->
                                <div class="col-md-6">
                                    <label class="d-block">
                                        Do you have other companies participating in the exhibition under your profile?
                                        <i class="text-danger">*</i>
                                    </label>

                                    <div class="d-flex align-items-center gap-4 mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hasOtherCompanies"
                                                id="other_companies_yes" value="YES"
                                                {{ ($item->hasOtherCompanies ?? null) === 1 ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="other_companies_yes">
                                                Yes, I have other companies participating under my profile
                                            </label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hasOtherCompanies"
                                                id="other_companies_no" value="NO"
                                                {{ ($item->hasOtherCompanies ?? null) === 0 ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="other_companies_no">
                                                No, I don’t have other companies participating under my profile
                                            </label>
                                        </div>
                                    </div>

                                    <span class="text-danger">
                                        @error('hasOtherCompanies')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>
                

                            <div class=" col-md-12 text-center p-10 text-primary"
                                style="background-color: rgb(227, 252, 250);">
                                <i>Contact Person Information</i>
                            </div>
                            <div class=" row form-group">
                                <div class=" col-md-6">
                                    <label>Contact Person Name<i class=" text-danger">*</i></label>
                                    <input type="text" name="personalName" class=" form-control"
                                        placeholder="Contact Person Name"
                                        value="{{ $item->personalName ?? old('personalName') }}">
                                    <span class=" text-danger">
                                        @error('personalName')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class=" col-md-6">
                                    <label>Contact Personal Email</label>
                                    <input type="text" name="personalEmail" class=" form-control"
                                        placeholder="Contact Personal Email"
                                        value="{{ $item->personalEmail ?? old('personalEmail') }}">
                                    <span class=" text-danger">
                                        @error('personalEmail')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class=" row form-group">
                                <div class=" col-md-6">
                                    <label>Contact Person Phone Number</label>
                                    <input type="text" name="personalPhone" class=" form-control"
                                        placeholder="Contact Person Phone"
                                        value="{{ $item->personalPhone ?? old('personalPhone') }}">
                                    <span class=" text-danger">
                                        @error('personalPhone')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class=" col-md-6">
                                    <label>Title</label>
                                    <input type="text" name="personalTitle" class=" form-control"
                                        placeholder="Contact Personal Title"
                                        value="{{ $item->personalTitle ?? old('personalTitle') }}">
                                    <span class=" text-danger">
                                        @error('personalTitle')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class=" col-md-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="isAcceptedTerms" class="styled" required
                                            style=" width: 20px;height: 20px;"
                                            @if ($item->isAcceptedTerms ?? 0 == 1) checked @endif>
                                        {{-- Placeholder domain: replace with the official site when it is confirmed. --}}
                                        By accessing or using <a href="https://gememis.go.tz"
                                            target="_newtab">gememis.go.tz</a>, you agree to be bound by <a
                                            href="{{ route('terms_and_condition.index') }}" target="_newtab">Terms
                                            and Conditions</a>. If
                                        you
                                        do not agree with any part of these terms, please do not use our
                                        service.
                                    </label>
                                </div>


                            </div>

                            <div class=" col-md-3 text-right">
                                <button type="submit" class="btn btn-primary btn-rounded "
                                    id="submitButton">Complete
                                    Registration <i class=" icon-arrow-right13"></i></button>
                            </div>

                        </td>
                    </tr>

                    
                </form>

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#searchSectors').select2({
            ajax: {
                url: '{{ route('sectors.create') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.sectorID,
                                text: item.sectorName,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search sector . . .',
            minimumInputLength: 0,
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#searchCountry').select2({
            ajax: {
                url: '{{ route('countries.create') }}', // URL for the AJAX request
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search query
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.country_name,
                                text: item.country_name,
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search Country',
            minimumInputLength: 0,
        });
    });
</script>
