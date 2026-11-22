<div class="panel panel-white" style="max-width:560px; margin:25px auto;">
    @isset($logo)
        <div class="panel-heading text-center">
            {{ $logo }}
        </div>
    @endisset

    <div class="panel-body">
        {{ $slot }}
    </div>
</div>