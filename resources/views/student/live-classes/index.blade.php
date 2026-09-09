<x-layouts.app title="Live classes" heading="Live classes"
               description="Sessions scheduled for the courses you are enrolled in.">

    <section>
        <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Upcoming</h2>

        @if ($upcoming->isEmpty())
            <p class="mt-3 border border-dashed border-ink-200 bg-white px-5 py-10 text-center text-sm text-ink-500">
                Nothing scheduled at the moment. New sessions are announced by email.
            </p>
        @else
            <ul class="mt-3 divide-y divide-ink-200 border border-ink-200 bg-white">
                @foreach ($upcoming as $liveClass)
                    <li class="flex flex-wrap items-center gap-x-6 gap-y-4 p-5">
                        <div class="w-16 shrink-0 border-r border-ink-200 pr-4 text-center">
                            <p class="text-[0.6875rem] tracking-wide text-ink-500 uppercase">{{ $liveClass->scheduled_at->format('M') }}</p>
                            <p class="font-serif text-2xl text-ink-900 tabular-nums">{{ $liveClass->scheduled_at->format('j') }}</p>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <a href="{{ route('live-classes.show', $liveClass) }}"
                                   class="text-[0.975rem] font-medium text-ink-900 hover:underline">{{ $liveClass->title }}</a>
                                @if ($liveClass->isJoinable())
                                    <x-ui.badge class="bg-red-600 text-white">Live now</x-ui.badge>
                                @endif
                            </div>
                            <p class="mt-1 truncate text-[0.8125rem] text-ink-500">{{ $liveClass->course->title }}</p>
                            <p class="mt-1.5 text-[0.8125rem] text-ink-600">
                                {{ $liveClass->scheduled_at->format('l g:ia') }} · {{ $liveClass->duration_minutes }} minutes
                                @if ($liveClass->instructor) · {{ $liveClass->instructor->name }} @endif
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            @if ($liveClass->isJoinable())
                                <x-ui.button :href="route('live-classes.join', $liveClass)" target="_blank" rel="noopener">
                                    Join class
                                </x-ui.button>
                            @else
                                <x-ui.button :href="route('live-classes.show', $liveClass)" variant="outline">Details</x-ui.button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($past->isNotEmpty())
        <section class="mt-10">
            <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Past sessions</h2>
            <ul class="mt-3 divide-y divide-ink-200 border border-ink-200 bg-white">
                @foreach ($past as $liveClass)
                    <li class="flex flex-wrap items-center justify-between gap-4 p-5">
                        <div class="min-w-0">
                            <a href="{{ route('live-classes.show', $liveClass) }}"
                               class="text-[0.9375rem] text-ink-800 hover:underline">{{ $liveClass->title }}</a>
                            <p class="mt-1 truncate text-[0.8125rem] text-ink-500">
                                {{ $liveClass->course->title }} · {{ $liveClass->scheduled_at->format('j M Y') }}
                            </p>
                        </div>

                        @if ($liveClass->recording_url)
                            <x-ui.button :href="route('live-classes.show', $liveClass)" variant="outline" size="sm" icon="play">
                                Watch recording
                            </x-ui.button>
                        @else
                            <span class="text-[0.8125rem] text-ink-400">No recording</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.app>
