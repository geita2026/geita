(function ($) {
    'use strict';

    const service = document.getElementById('service-booking-service');
    if (!service) {
        return;
    }
    const quantity = document.getElementById('service-booking-quantity');
    const preview = document.getElementById('service-booking-preview');
    const amount = new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function updatePreview() {
        const option = service.selectedOptions[0];
        const cost = Number(option?.dataset.price);
        const units = quantity.valueAsNumber;
        preview.textContent = Number.isFinite(cost) && Number.isInteger(units) && units > 0
            ? [option.dataset.currency, amount.format(cost * units)].filter(Boolean).join(' ')
            : '—';
    }

    $(service).on('change', updatePreview);
    quantity.addEventListener('input', updatePreview);
    updatePreview();
})(jQuery);
