@props(['target', 'label' => 'Starts in'])

{{-- Client-side countdown; the server has already decided whether the join
     action is actually available, so this is purely informational. --}}
<div x-data="{
        target: new Date('{{ $target->toIso8601String() }}').getTime(),
        remaining: 0,
        tick() {
            this.remaining = Math.max(0, this.target - Date.now());
        },
        get days() { return Math.floor(this.remaining / 86400000); },
        get hours() { return Math.floor((this.remaining % 86400000) / 3600000); },
        get minutes() { return Math.floor((this.remaining % 3600000) / 60000); },
        get seconds() { return Math.floor((this.remaining % 60000) / 1000); },
     }"
     x-init="tick(); setInterval(() => tick(), 1000)"
     {{ $attributes->merge(['class' => 'text-center']) }}>
    <p class="text-[0.6875rem] font-medium tracking-[0.14em] text-ink-500 uppercase">{{ $label }}</p>
    <div class="mt-3 flex items-center justify-center gap-4 sm:gap-6">
        <template x-if="days > 0">
            <div>
                <p class="font-serif text-3xl text-ink-900 tabular-nums sm:text-4xl" x-text="days"></p>
                <p class="mt-1 text-[0.6875rem] text-ink-500 uppercase">days</p>
            </div>
        </template>
        <div>
            <p class="font-serif text-3xl text-ink-900 tabular-nums sm:text-4xl" x-text="String(hours).padStart(2,'0')"></p>
            <p class="mt-1 text-[0.6875rem] text-ink-500 uppercase">hours</p>
        </div>
        <div>
            <p class="font-serif text-3xl text-ink-900 tabular-nums sm:text-4xl" x-text="String(minutes).padStart(2,'0')"></p>
            <p class="mt-1 text-[0.6875rem] text-ink-500 uppercase">min</p>
        </div>
        <div>
            <p class="font-serif text-3xl text-ink-900 tabular-nums sm:text-4xl" x-text="String(seconds).padStart(2,'0')"></p>
            <p class="mt-1 text-[0.6875rem] text-ink-500 uppercase">sec</p>
        </div>
    </div>
</div>
