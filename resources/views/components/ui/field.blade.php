@props(['label' => null, 'for' => null, 'hint' => null, 'error' => null, 'required' => false])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <x-ui.label :for="$for" :required="$required">{{ $label }}</x-ui.label>
    @endif

    {{ $slot }}

    @if ($hint && ! $error)
        <p class="text-[0.8125rem] text-ink-500">{{ $hint }}</p>
    @endif

    <x-ui.error :messages="$error" />
</div>
