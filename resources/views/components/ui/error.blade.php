@props(['messages' => null])

@if ($messages)
    <p {{ $attributes->merge(['class' => 'mt-1.5 text-[0.8125rem] text-red-700']) }}>
        {{ is_array($messages) ? $messages[0] : $messages }}
    </p>
@endif
