(function ($) {
    'use strict';

    const customSelect = '.select2-hidden-accessible, .bootstrap-select, .selectpicker, '
        + '.multiselect, .select-search, [data-toggle="select2"], [data-remote], [data-native-select]';

    function enhanceSelect(select) {
        const $select = $(select);
        if (select.disabled || select.size > 1 || !select.getClientRects().length || $select.is(customSelect)
            || $select.closest('.bootstrap-select, .dataTables_wrapper .dataTables_length').length
            || $select.next('.nice-select').length) {
            return;
        }

        const options = Array.from(select.options).filter(option => option.value
            && !option.disabled && !option.hidden && !option.closest('optgroup:disabled'));

        if (select.required && !select.multiple && !select.value && options.length === 1) {
            $select.val(options[0].value).trigger('change');
        }

        if (!document.body.classList.contains('gememis-app') || !$.fn.select2 || options.length < 2 || !select.form) {
            return;
        }

        const modal = $select.closest('.modal');
        const settings = { width: '100%' };
        if (modal.length) {
            settings.dropdownParent = modal;
        }
        if (!select.multiple && select.options[0] && select.options[0].value === '') {
            settings.placeholder = select.options[0].textContent.trim();
        }
        $select.addClass('gememis-searchable').select2(settings);

        const control = $select.next('.select2-container').find('.select2-selection')[0];
        const label = select.labels && select.labels[0];
        if (label && select.id) {
            label.id = label.id || select.id + '-label';
            control.setAttribute('aria-labelledby', label.id);
        }
        ['aria-describedby', 'aria-invalid'].forEach(attribute => {
            if (select.hasAttribute(attribute)) {
                control.setAttribute(attribute, select.getAttribute(attribute));
            }
        });

        select.addEventListener('invalid', function (event) {
            event.preventDefault();
            if (select.form.querySelector('input:invalid, select:invalid, textarea:invalid') === select) {
                control.focus();
                control.setAttribute('aria-invalid', 'true');
            }
        });
        $select.on('change.gememis', function () {
            control.setAttribute('aria-invalid', String(!select.validity.valid));
        });
        if (modal.length && !select.multiple) {
            $select.on('select2:open.gememis', function () {
                setTimeout(function () {
                    const search = modal[0].querySelector('.select2-dropdown .select2-search__field');
                    if (search) {
                        search.focus({ preventScroll: true });
                    }
                }, 0);
            });
        }
    }

    function enhanceWithin(root) {
        root.querySelectorAll('select[name]').forEach(enhanceSelect);
    }

    $(document).on('shown.bs.modal.gememis', '.modal', function (event) {
        if (event.target === this) {
            enhanceWithin(this);
        }
    });

    // Let each page initialize its own AJAX and custom dropdowns first.
    $(function () {
        setTimeout(function () {
            enhanceWithin(document);
        }, 0);
    });
})(jQuery);
