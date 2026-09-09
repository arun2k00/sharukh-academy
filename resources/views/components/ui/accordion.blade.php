@props(['items' => [], 'open' => null])

{{-- Keyboard-accessible disclosure list used for FAQs and the public curriculum. --}}
<div x-data="{ open: @js($open) }" {{ $attributes->merge(['class' => 'divide-y divide-ink-200 border-y border-ink-200']) }}>
    @foreach ($items as $index => $item)
        <div>
            <h3>
                <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                        :aria-expanded="open === {{ $index }} ? 'true' : 'false'"
                        class="flex w-full items-start justify-between gap-6 py-4 text-left">
                    <span class="font-sans text-[0.95rem] font-medium text-ink-900">{{ $item['question'] }}</span>
                    <x-ui.icon name="chevron-down" class="mt-0.5 size-4 shrink-0 text-ink-400 transition-transform"
                               ::class="open === {{ $index }} && 'rotate-180'" />
                </button>
            </h3>
            <div x-show="open === {{ $index }}" x-collapse x-cloak>
                <div class="pb-5 text-[0.9375rem]/7 text-ink-600">{{ $item['answer'] }}</div>
            </div>
        </div>
    @endforeach
</div>
