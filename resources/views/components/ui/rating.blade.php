@props(['value' => 5])

<div {{ $attributes->merge(['class' => 'flex items-center gap-0.5']) }} aria-label="{{ $value }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <x-ui.icon name="star"
                   class="size-3.5 {{ $i <= $value ? 'fill-brass-500 text-brass-500' : 'text-ink-200' }}" />
    @endfor
</div>
