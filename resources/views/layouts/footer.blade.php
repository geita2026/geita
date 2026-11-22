<footer class="navbar gememis-footer">
    <span>&copy; {{ date('Y') }} Geita Mineral Exhibition Management Information System (GeMEMIS)</span>
    @if (config('global.APP_VERSION'))
        <span>{{ config('global.APP_VERSION') }}</span>
    @endif
</footer>
<script src="{{ asset('assets/js/pages/components_popups.js') }}"></script>
