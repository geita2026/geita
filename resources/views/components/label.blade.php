@props(['for', 'value' => null])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'control-label']) }}>
    {{ $value ?? $slot }}
</label>