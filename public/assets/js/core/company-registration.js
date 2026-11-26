(function ($) {
    'use strict';

    const form = document.getElementById('first-company-registration-form');
    if (!form) {
        return;
    }

    const requiredMessages = {
        companyName: 'Enter your company or institution name.',
        tinNo: 'Enter your Tax Identification Number (TIN).',
        companyEmail: 'Enter your company email address.',
        companyPhone: 'Enter your company phone number.',
        companyLocation: 'Select a country.',
        companySector: 'Select a company sector.',
        companyCategory: 'Select a company category.',
        companyAddress: 'Enter your company address.',
        isAcceptedTerms: 'Accept the terms and conditions to continue.',
    };

    const fields = new Map();
    form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(field => {
        const error = document.getElementById('first-company-' + field.name + '-error');
        fields.set(field, {
            error,
            initialValue: field.value,
            serverMessage: error.textContent,
        });
    });

    function validationMessage(field, state) {
        const value = field.value.trim();
        if (field.required && (field.type === 'checkbox' ? !field.checked : !value)) {
            return requiredMessages[field.name];
        }
        if (field.name === 'tinNo' && !/^[0-9]{9}$/.test(value)) {
            return 'Enter exactly 9 digits for your TIN.';
        }
        if (field.validity.typeMismatch) {
            return field.type === 'email'
                ? 'Enter a valid email address, for example name@company.com.'
                : 'Enter a full website address, for example https://company.com.';
        }
        if (field.maxLength >= 0 && value.length > field.maxLength) {
            return 'Use ' + field.maxLength + ' characters or fewer.';
        }
        return value === state.initialValue.trim() ? state.serverMessage : '';
    }

    function visibleControl(field) {
        return field.nextElementSibling?.querySelector('.select2-selection') || field;
    }

    function validate(field) {
        const state = fields.get(field);
        const message = validationMessage(field, state);
        const invalid = String(Boolean(message));
        if (state.error.textContent !== message) {
            state.error.textContent = message;
        }
        field.setAttribute('aria-invalid', invalid);
        visibleControl(field).setAttribute('aria-invalid', invalid);
        return !message;
    }

    $(form).on('focusout input change select2:close', 'input, select, textarea', function (event) {
        if (fields.has(this) && (event.type !== 'input' || this.getAttribute('aria-invalid') === 'true')) {
            validate(this);
        }
    });

    form.addEventListener('submit', function (event) {
        let firstInvalid;
        for (const field of fields.keys()) {
            if (!validate(field) && !firstInvalid) {
                firstInvalid = field;
            }
        }
        if (firstInvalid) {
            event.preventDefault();
            visibleControl(firstInvalid).focus();
        }
    });

    // Use native validation when the inline validation script is unavailable.
    form.noValidate = true;
})(jQuery);
