(() => {
    const form = document.querySelector('#exhibition-booking-form');
    const submit = document.querySelector('#booking-submit');
    const company = document.querySelector('#booking-company');

    if (!form || !submit || !company) {
        return;
    }

    const hasBooths = form.dataset.hasBooths === 'true';
    const booths = [...form.querySelectorAll('.booking-booth-select')];
    const blueprintButtons = new Map(
        [...form.querySelectorAll('[data-blueprint-booth]')].map(btn => [btn.dataset.blueprintBooth, btn])
    );
    const checkboxByBooth = new Map(booths.map(cb => [cb.value, cb]));
    const summary = document.querySelector('#booking-selection');
    const estimate = document.querySelector('#booking-estimate');
    const clear = document.querySelector('#booking-clear');
    const footer = form.querySelector('.booking-footer');
    const formatter = new Intl.NumberFormat('en', {useGrouping: true});
    const money = new Intl.NumberFormat('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    function updateFooterHeight() {
        form.style.setProperty('--booking-footer-height', footer.offsetHeight + 'px');
    }

    updateFooterHeight();
    if ('ResizeObserver' in window) {
        new ResizeObserver(updateFooterHeight).observe(footer);
    } else {
        window.addEventListener('resize', updateFooterHeight);
    }

    function syncBlueprintVisuals() {
        for (const cb of booths) {
            const btn = blueprintButtons.get(cb.value);
            if (!btn) {
                continue;
            }
            btn.classList.toggle('is-selected', cb.checked);
            btn.setAttribute('aria-pressed', cb.checked ? 'true' : 'false');
        }
    }

    function updateEstimate(selectedBooths) {
        const totals = new Map();
        let missingRates = 0;
        for (const booth of selectedBooths) {
            const {estimatedCost, currency} = blueprintButtons.get(booth.value).dataset;
            if (estimatedCost === undefined || !currency) {
                missingRates++;
                continue;
            }
            totals.set(currency, (totals.get(currency) ?? 0) + Number(estimatedCost));
        }
        const amounts = Array.from(totals, ([currency, amount]) => currency + ' ' + money.format(amount));
        estimate.hidden = selectedBooths.length === 0;
        estimate.textContent = amounts.length
            ? 'Estimated booth cost: ' + amounts.join(' + ') + (missingRates ? '. Some booths have no estimate.' : '')
            : 'Estimated booth cost unavailable.';
    }

    function updateSelection() {
        if (!hasBooths) {
            return;
        }

        const selectedBooths = booths.filter(booth => booth.checked);
        const count = selectedBooths.length;
        summary.textContent = count > 0
            ? formatter.format(count) + (count === 1 ? ' booth selected' : ' booths selected')
            : 'Select at least one available booth.';
        if (clear) {
            clear.disabled = count === 0;
        }
        updateEstimate(selectedBooths);
        syncBlueprintVisuals();
    }

    for (const [boothId, btn] of blueprintButtons.entries()) {
        btn.addEventListener('click', () => {
            const cb = checkboxByBooth.get(boothId);
            if (!cb || cb.disabled) {
                return;
            }
            cb.checked = !cb.checked;
            updateSelection();
        });
    }

    if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery(company).select2({width: '100%'});
    }

    if (clear) {
        clear.addEventListener('click', () => {
            for (const booth of booths) {
                booth.checked = false;
            }
            updateSelection();
            blueprintButtons.values().next().value?.focus();
        });
    }

    form.addEventListener('submit', event => {
        if (hasBooths && !booths.some(booth => booth.checked)) {
            event.preventDefault();
            blueprintButtons.values().next().value?.focus();
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
