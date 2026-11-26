<button type="button" class="geita-password-toggle" aria-controls="{{ $inputId }}"
    aria-label="Show password" data-password-visibility hidden>
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/>
        <circle cx="12" cy="12" r="2.5"/>
        <path class="geita-password-slash" d="M3 3l18 18"/>
    </svg>
</button>

@once
    <script src="{{ asset('assets/js/core/password-visibility.js') }}" defer></script>
@endonce
