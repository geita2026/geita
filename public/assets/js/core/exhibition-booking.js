(() => {
    const form = document.querySelector('#exhibition-booking-form');
    const submit = document.querySelector('#booking-submit');
    const company = document.querySelector('#booking-company');

    if (!form || !submit || !company) {
        return;
    }

    if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery(company).select2({width: '100%'});
    }

    const hasBooths = form.dataset.hasBooths === 'true';
    const booths = [...form.querySelectorAll('.booking-booth-select')];
    const summary = document.querySelector('#booking-selection');
    const clear = document.querySelector('#booking-clear');
    const formatter = new Intl.NumberFormat('en', {useGrouping: true});

    function updateSelection() {
        if (!hasBooths) {
            return;
        }

        const count = booths.filter(booth => booth.checked).length;
        summary.textContent = count > 0
            ? formatter.format(count) + (count === 1 ? ' booth selected' : ' booths selected')
            : 'Select at least one available booth.';
        clear.hidden = count === 0;
    }

    for (const booth of booths) {
        booth.addEventListener('change', updateSelection);
    }

    if (clear) {
        clear.addEventListener('click', () => {
            for (const booth of booths) {
                booth.checked = false;
            }
            updateSelection();
            booths[0]?.focus();
        });
    }

    form.addEventListener('submit', event => {
        if (hasBooths && !booths.some(booth => booth.checked)) {
            event.preventDefault();
            booths[0]?.focus();
            return;
        }

        submit.disabled = true;
    });

    window.addEventListener('pageshow', () => {
        submit.disabled = false;
        updateSelection();
    });

    updateSelection();
})();
