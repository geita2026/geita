<form id="first-company-registration-form" method="POST" action="{{ $registrationAction }}">
    @csrf
    @php
        $registrationErrors = $errors->firstCompany;
        $defaultCountry = $countries->firstWhere('country_short_code', 'TZ')->country_name ?? 'Tanzania';
    @endphp

    <div class="registration-body">
        @error('registration', 'firstCompany')
            <p class="alert alert-danger" role="alert">{{ $message }}</p>
        @enderror

        <div class="registration-grid">
            <div class="registration-field registration-wide">
                <label for="first-company-name">Company or institution name <span class="registration-required" aria-hidden="true">*</span></label>
                <input id="first-company-name" name="companyName" autocomplete="organization" value="{{ old('companyName') }}"
                    class="form-control" maxlength="255" required
                    aria-describedby="first-company-companyName-error" @error('companyName', 'firstCompany') aria-invalid="true" @enderror>
                <span class="registration-error" id="first-company-companyName-error" aria-live="polite">{{ $registrationErrors->first('companyName') }}</span>
            </div>
            <div class="registration-field">
                <label for="first-company-tin">Tax Identification Number (TIN) <span class="registration-required" aria-hidden="true">*</span></label>
                <input id="first-company-tin" name="tinNo" value="{{ old('tinNo') }}"
                    class="form-control" inputmode="numeric" pattern="[0-9]{9}" maxlength="9"
                    required aria-describedby="first-company-tin-help first-company-tinNo-error"
                    @if ($registrationErrors->has('tinNo')) aria-invalid="true" @endif>
                <span id="first-company-tin-help" class="registration-hint">Enter all 9 digits.</span>
                <span class="registration-error" id="first-company-tinNo-error" aria-live="polite">{{ $registrationErrors->first('tinNo') }}</span>
            </div>
            <div class="registration-field">
                <label for="first-company-email">Company email <span class="registration-required" aria-hidden="true">*</span></label>
                <input type="email" id="first-company-email" name="companyEmail" autocomplete="email" value="{{ old('companyEmail') }}"
                    class="form-control" maxlength="255" required
                    aria-describedby="first-company-companyEmail-error" @error('companyEmail', 'firstCompany') aria-invalid="true" @enderror>
                <span class="registration-error" id="first-company-companyEmail-error" aria-live="polite">{{ $registrationErrors->first('companyEmail') }}</span>
            </div>
            <div class="registration-field">
                <label for="first-company-phone">Company phone <span class="registration-required" aria-hidden="true">*</span></label>
                <input type="tel" id="first-company-phone" name="companyPhone" autocomplete="tel" value="{{ old('companyPhone') }}"
                    class="form-control" maxlength="30" required
                    aria-describedby="first-company-companyPhone-error" @error('companyPhone', 'firstCompany') aria-invalid="true" @enderror>
                <span class="registration-error" id="first-company-companyPhone-error" aria-live="polite">{{ $registrationErrors->first('companyPhone') }}</span>
            </div>
            <div class="registration-field">
                <label for="first-company-country">Country <span class="registration-required" aria-hidden="true">*</span></label>
                <select id="first-company-country" name="companyLocation" autocomplete="country-name" class="form-control" required
                    aria-describedby="first-company-companyLocation-error" @error('companyLocation', 'firstCompany') aria-invalid="true" @enderror>
                    <option value="">Select country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->country_name }}"
                            {{ old('companyLocation', $defaultCountry) === $country->country_name ? 'selected' : '' }}>
                            {{ $country->country_name }}
                        </option>
                    @endforeach
                </select>
                <span class="registration-error" id="first-company-companyLocation-error" aria-live="polite">{{ $registrationErrors->first('companyLocation') }}</span>
            </div>
            <div class="registration-field">
                <label for="first-company-sector">Company sector <span class="registration-required" aria-hidden="true">*</span></label>
                <select id="first-company-sector" name="companySector" class="form-control" required
                    aria-describedby="first-company-companySector-error" @error('companySector', 'firstCompany') aria-invalid="true" @enderror>
                    <option value="">Select sector</option>
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->sectorID }}"
                            {{ old('companySector') === (string) $sector->sectorID ? 'selected' : '' }}>
                            {{ $sector->sectorName }}
                        </option>
                    @endforeach
                </select>
                <span class="registration-error" id="first-company-companySector-error" aria-live="polite">{{ $registrationErrors->first('companySector') }}</span>
            </div>
            <div class="registration-field">
                <label for="first-company-category">Company category <span class="registration-required" aria-hidden="true">*</span></label>
                <select id="first-company-category" name="companyCategory" class="form-control" required
                    aria-describedby="first-company-companyCategory-error" @error('companyCategory', 'firstCompany') aria-invalid="true" @enderror>
                    <option value="">Select company category</option>
                    <option {{ old('companyCategory') === 'Individuals' ? 'selected' : '' }}>Individuals</option>
                    <option {{ old('companyCategory') === 'Small Traders' ? 'selected' : '' }}>Small Traders</option>
                    <option {{ old('companyCategory') === 'Medium-Sized Enterprises' ? 'selected' : '' }}>Medium-Sized Enterprises</option>
                    <option {{ old('companyCategory') === 'Large Corporates' ? 'selected' : '' }}>Large Corporates</option>
                    <option value="Government Insititution" {{ old('companyCategory') === 'Government Insititution' ? 'selected' : '' }}>
                        Government Institution
                    </option>
                </select>
                <span class="registration-error" id="first-company-companyCategory-error" aria-live="polite">{{ $registrationErrors->first('companyCategory') }}</span>
            </div>
            <div class="registration-field registration-wide">
                <label for="first-company-address">Company address <span class="registration-required" aria-hidden="true">*</span></label>
                <textarea id="first-company-address" name="companyAddress" class="form-control"
                    autocomplete="street-address" rows="2" maxlength="1000" required
                    aria-describedby="first-company-companyAddress-error" @error('companyAddress', 'firstCompany') aria-invalid="true" @enderror>{{ old('companyAddress') }}</textarea>
                <span class="registration-error" id="first-company-companyAddress-error" aria-live="polite">{{ $registrationErrors->first('companyAddress') }}</span>
            </div>
        </div>

        <section class="registration-optional" aria-labelledby="optional-company-details">
            <h3 id="optional-company-details">Optional company details</h3>
            <div class="registration-grid">
                <div class="registration-field">
                    <label for="first-company-registration">Business registration number</label>
                    <input id="first-company-registration" name="registrationNo" value="{{ old('registrationNo') }}"
                        class="form-control" maxlength="255"
                        aria-describedby="first-company-registrationNo-error" @error('registrationNo', 'firstCompany') aria-invalid="true" @enderror>
                    <span class="registration-error" id="first-company-registrationNo-error" aria-live="polite">{{ $registrationErrors->first('registrationNo') }}</span>
                </div>
                <div class="registration-field">
                    <label for="first-company-website">Website</label>
                    <input type="url" id="first-company-website" name="website" value="{{ old('website') }}"
                        autocomplete="url" class="form-control" maxlength="255"
                        aria-describedby="first-company-website-error" @error('website', 'firstCompany') aria-invalid="true" @enderror>
                    <span class="registration-error" id="first-company-website-error" aria-live="polite">{{ $registrationErrors->first('website') }}</span>
                </div>
            </div>
        </section>
    </div>

    <div class="registration-footer">
        <div>
            <label class="registration-consent">
                <input type="checkbox" name="isAcceptedTerms" value="1" {{ old('isAcceptedTerms') ? 'checked' : '' }} required
                    aria-describedby="first-company-isAcceptedTerms-error" @error('isAcceptedTerms', 'firstCompany') aria-invalid="true" @enderror>
                <span>I accept the <a href="{{ route('terms_and_condition.index') }}" target="_blank" rel="noopener">terms and conditions</a>.</span>
            </label>
            <span class="registration-error" id="first-company-isAcceptedTerms-error" aria-live="polite">{{ $registrationErrors->first('isAcceptedTerms') }}</span>
        </div>
        <button type="submit" class="registration-submit">Register company</button>
    </div>
</form>
