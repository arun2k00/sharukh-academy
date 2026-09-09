@props(['for' => null, 'required' => false])

<label @if($for) for="{{ $for }}" @endif
       {{ $attributes->merge(['class' => 'block text-[0.8125rem] font-medium text-ink-700']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-red-600" aria-hidden="true">*</span>
    @endif
</label>
