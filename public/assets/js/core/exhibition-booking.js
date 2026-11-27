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
    const clear = document.querySelector('#booking-clear');
    const formatter = new Intl.NumberFormat('en', {useGrouping: true});

    function shortenCompanyStamp(raw) {
        const text = (raw || '').trim().toUpperCase();
        if (!text) {
            return 'SELECTED';
        }
        if (text.length <= 13) {
            return text;
        }
        const stripped = text
            .replace(/\b(COMPANY|LIMITED|LTD\.?|CO\.?|CORPORATION|CORP\.?|INC\.?|PLC|GROUP|ENTERPRISES|INVESTMENTS?|TANZANIA|\(T\))\b/g, '')
            .replace(/[\s,\-.]+/g, ' ')
            .trim() || text;
        const words = stripped.split(' ');
        let short = words[0];
        if (words[1] && (short + ' ' + words[1]).length <= 14) {
            short += ' ' + words[1];
        }
        return short.length > 15 ? short.slice(0, 14) + '…' : short;
    }

    function currentSelectedStamp() {
        const selectedOption = company.options[company.selectedIndex];
        if (!company.value || !selectedOption) {
            return 'SELECTED';
        }
        return shortenCompanyStamp(selectedOption.textContent);
    }

    function syncBlueprintVisuals() {
        const stampLabel = currentSelectedStamp();
        for (const cb of booths) {
            const btn = blueprintButtons.get(cb.value);
            const row = form.querySelector(`[data-booth-row="${CSS.escape(cb.value)}"]`);
            if (row) {
                row.classList.toggle('is-selected-row', cb.checked);
            }
            if (!btn) {
                continue;
            }
            btn.classList.toggle('is-selected', cb.checked);
            btn.setAttribute('aria-pressed', cb.checked ? 'true' : 'false');
            const stamp = btn.querySelector('.pavilion-booth-stamp');
            if (stamp) {
                const text = cb.checked ? stampLabel : (stamp.dataset.defaultEmpty || 'EMPTY');
                stamp.textContent = text;
                // Match the server-side size tiers so a long company name still
                // fits the -45deg stamp inside the cell.
                stamp.classList.toggle('is-long', text.length > 6 && text.length <= 11);
                stamp.classList.toggle('is-xlong', text.length > 11);
            }
        }
    }

    function updateSelection() {
        if (!hasBooths) {
            return;
        }

        const count = booths.filter(booth => booth.checked).length;
        summary.textContent = count > 0
            ? formatter.format(count) + (count === 1 ? ' booth selected' : ' booths selected')
            : 'Select at least one available booth.';
        if (clear) {
            clear.hidden = count === 0;
        }
        syncBlueprintVisuals();
    }

    for (const booth of booths) {
        booth.addEventListener('change', updateSelection);
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
        window.jQuery(company)
            .select2({width: '100%'})
            .on('change', syncBlueprintVisuals);
    } else {
        company.addEventListener('change', syncBlueprintVisuals);
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
            const firstBlueprintBtn = blueprintButtons.values().next().value;
            (firstBlueprintBtn || booths[0])?.focus();
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
