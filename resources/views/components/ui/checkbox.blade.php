@props(['label' => null, 'hint' => null, 'id' => null])

@php $id = $id ?? 'check-'.\Illuminate\Support\Str::random(6); @endphp

<div class="flex items-start gap-2.5">
    <input type="checkbox" id="{{ $id }}"
        {{ $attributes->merge(['class' => 'mt-0.5 size-4 shrink-0 border-ink-300 text-pine-700 focus:ring-pine-600']) }} />
    @if ($label)
        <div class="min-w-0">
            <label for="{{ $id }}" class="block text-sm text-ink-800">{{ $label }}</label>
            @if ($hint)
                <p class="mt-0.5 text-[0.8125rem] text-ink-500">{{ $hint }}</p>
            @endif
        </div>
    @endif
</div>
