{{-- company registration form --}}
<div class=" row">
    <div class=" col-md-12 form-group" style="background-color: rgb(237, 237, 243);">
        <h4 class=" text-bold text-primary text-center">ASSOCIATE COMPANIES / BUSINESS</h4>
    </div><br>
    <div class="row form-group">
        <div class="col-md-4">
            <label>Company Name<i class=" text-danger">*</i></label>
            <input type="text" name="company_name" class=" form-control required" placeholder="Company Name"
                value="{{ old('company_name') }}" required>
            <span class="text-danger"> @error('company_name')
                    {{ $message }}
                @enderror
            </span>
        </div>
        <div class=" col-md-4">
            <label>Company Email<i class=" text-danger">*</i></label>
            <input type="text" name="company_email" class=" form-control"
                placeholder=" Company Email ie. demo@gmail.com" value="{{ old('company_email') }}" required>
            <span class="text-danger"> @error('company_email')
                    {{ $message }}
                @enderror
            </span>
        </div>
        <div class="  col-md-4">
            <label>Campany Phone<i class=" text-danger">*</i></label>
            <input type="text" name="company_phone" class="form-control" placeholder="255xxxxxxxxx"
                value="{{ old('company_phone') }}" required>
            <span class="text-danger"> @error('company_phone')
                    {{ $message }}
                @enderror
            </span>
        </div>
    </div>

    <div class=" form-group row">
        <div class="  col-md-4">
            <label for="associate-company-sector">Company Sector</label>
            <select id="associate-company-sector" name="company_sector" class="form-control" required>
                <option value="">Select company sector</option>
                @foreach ($sectors as $sector)
                    <option value="{{ $sector->sectorID }}" {{ old('company_sector') == $sector->sectorID ? 'selected' : '' }}>
                        {{ $sector->sectorName }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger"> @error('company_sector')
                    {{ $message }}
                @enderror
            </span>
        </div>
        <div class="  col-md-4">
            <label for="associate-company-category">Company Category</label>
            <select id="associate-company-category" name="company_category" class="form-control" required>
                <option value="">Select company category</option>
                @foreach (['Government Institution', 'Private Companies', 'SMEs'] as $category)
                    <option value="{{ $category }}" {{ old('company_category') === $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger"> @error('company_category')
                    {{ $message }}
                @enderror
            </span>
        </div>

        <div class="col-md-4">
            <label for="country">Company Originality (Country)</label>
            <select name="country" id="country" class="form-control" required>
                <option value="">Select country</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->country_name }}" {{ old('country') === $country->country_name ? 'selected' : '' }}>
                        {{ $country->country_name }} ({{ $country->country_short_code }})
                    </option>
                @endforeach
            </select>
            <span class="text-danger"> @error('country')
                    {{ $message }}
                @enderror
            </span>
        </div>
    </div>







    {{-- <div class=" col-md-12" style="background-color: rgb(237, 237, 243);">
        <h4 class=" text-bold text-primary text-center">COMPANY REGISTRATION INFORMATIONS</h4>
    </div>
     <div class=" form-group row">
    <div class=" col-md-4">
        <label>Busines Registration Number</label>
        <input type="text" name="registrationNo" class="form-control " placeholder="999-999-999" value="{{ old('registrationNo') }}">
    </div>
    <div class=" col-md-4">
        <label>Taxpayer Identification Number (TIN)</label>
        <input type="text" name="tinNo" class=" form-control input-sm" placeholder="999-999-999" value="{{ old('tinNo') }}">
    </div>
    <div class=" col-md-4">
        <label>Company Telephone</label>
        <input type="text" name="companyTele" class=" form-control input-sm" placeholder=" Company Telephone Number" value="{{ old('companyTele') }}">
    </div>
    </div>

    <div class=" row form-group">
        <div class="col-md-4">
            <label>Company Address</label>
            <input type="text" name="companyAddress" class=" form-control input-sm " placeholder="Company Address Eg, P.O.BOX 5402" value="{{ old('companyAddress') }}">
        </div>
        <div class="col-md-4">
            <label>Company Website</label>
            <input type="text" name="website" class=" form-control input-sm" placeholder="Company website" value="{{ old('website') }}">
        </div>
    </div>


    <div class=" col-md-12" style="background-color: rgb(237, 237, 243);">
        <h4 class=" text-bold text-primary text-center">COMPANY CONTACT PERSON INFORMATIONS</h4>
    </div>

    <div class=" row form-group">
        <div class=" col-md-4">
            <label>Contact Person Name:<i class=" text-danger">*</i></label>
            <input type="text" name="personalName" class=" form-control input-sm " placeholder="Contact Person Name" value="{{ old('personalName') }}">
            <span class=" text-danger">@error('personalName') {{ $message }} @enderror</span>
        </div>
        <div class="col-md-4">
            <label>Contact person Phone No:<i class=" text-danger">*</i></label>
            <input type="text" name="personalPhone" class=" form-control input-sm" placeholder="Contact Personal Phone Number" value="{{ old('personalPhone') }}">
            <span class=" text-danger">@error('personalPhone') {{ $message }} @enderror</span>
        </div>
        <div class=" col-md-4">
            <label>Contact Person Email:</label>
            <input type="text" name="personalEmail" class=" form-control input-sm " placeholder="Contact Person Email" value="{{ old('personalEmail') }}">
            <span class=" text-danger">@error('personalEmail') {{ $message }} @enderror</span>
        </div>
    </div>
   <div class=" row form-group">
    <div class=" col-md-4">
        <label>Contact Person Title:<i class=" text-danger">*</i></label>
        <input type="text" name="personalTitle" class=" form-control input-sm " placeholder="Contact Person Title" value="{{ old('personalTitle') }}">
        <span class=" text-danger">@error('personalTitle') {{ $message }} @enderror</span>
    </div>
   </div> --}}
    <div class=" form-group">
        <div class="col-md-12  text-right">
            <button type="submit" class="btn btn-primary text-right"> Register Company</button>
        </div>
    </div>

</div>
{{-- company registration form --}}
