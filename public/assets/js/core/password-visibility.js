document.querySelectorAll('[data-password-visibility]').forEach(function (button) {
    const input = document.getElementById(button.getAttribute('aria-controls'));

    button.hidden = false;
    button.addEventListener('click', function () {
        const showPassword = input.type === 'password';
        input.type = showPassword ? 'text' : 'password';
        button.classList.toggle('is-visible', showPassword);
        button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
    });
});
