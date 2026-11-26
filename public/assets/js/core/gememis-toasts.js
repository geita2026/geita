(function () {
    'use strict';

    let queue = Promise.resolve();

    window.gememisToast = function ({ icon = 'info', title, text }) {
        const show = function () {
            return window.Swal.fire({
                toast: true,
                position: 'top-end',
                icon,
                title,
                text,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: { popup: 'gememis-toast' },
                timer: icon === 'error' ? 6500 : 4500,
                timerProgressBar: true,
                didOpen(popup) {
                    popup.addEventListener('mouseenter', window.Swal.stopTimer);
                    popup.addEventListener('mouseleave', window.Swal.resumeTimer);
                    popup.addEventListener('focusin', window.Swal.stopTimer);
                    popup.addEventListener('focusout', window.Swal.resumeTimer);
                },
            });
        };

        queue = queue.then(show, show);
        return queue;
    };
})();
