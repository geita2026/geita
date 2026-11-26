@php
    $flashToasts = [];
    foreach (['success' => 'success', 'fail' => 'error', 'warning' => 'warning', 'info' => 'info'] as $key => $icon) {
        $message = session($key);
        if (is_string($message) && $message !== '') {
            $flashToasts[] = ['icon' => $icon, 'text' => $message];
        }
    }
@endphp
@if ($flashToasts)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messages = @json($flashToasts);
            messages.forEach(message => window.gememisToast(message));
        });
    </script>
@endif
