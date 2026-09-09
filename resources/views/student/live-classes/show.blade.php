<x-layouts.app :title="$liveClass->title">
    <a href="{{ route('live-classes.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">
        <x-ui.icon name="arrow-left" class="size-3.5" />
        All live classes
    </a>

    <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-8">
            <x-ui.badge :class="$liveClass->status->badgeClasses()">{{ $liveClass->status->label() }}</x-ui.badge>

            <h1 class="mt-4 text-[1.6rem] leading-tight">{{ $liveClass->title }}</h1>
            <p class="mt-2 text-sm text-ink-600">
                Part of <a href="{{ route('learn.course', $liveClass->course) }}"
                           class="text-pine-700 hover:underline">{{ $liveClass->course->title }}</a>
            </p>

            @if ($liveClass->description)
                <div class="prose-academy mt-6">
                    @foreach (preg_split('/\r\n\r\n|\n\n/', $liveClass->description) as $paragraph)
                        @if (trim($paragraph) !== '')
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            @endif

            @if ($liveClass->preparation_notes)
                <section class="mt-8 border-l-2 border-pine-600 bg-pine-50 p-5">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-pine-800 uppercase">
                        Before the session
                    </h2>
                    <p class="mt-2.5 text-[0.9375rem]/7 text-ink-700">{{ $liveClass->preparation_notes }}</p>
                </section>
            @endif

            @if ($liveClass->resources->isNotEmpty())
                <section class="mt-8">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">
                        Class materials
                    </h2>
                    <ul class="mt-3 divide-y divide-ink-200 border-y border-ink-200">
                        @foreach ($liveClass->resources as $resource)
                            <li class="flex items-center gap-4 py-3">
                                <x-ui.icon name="file-text" class="size-5 shrink-0 text-ink-400" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm text-ink-900">{{ $resource->title }}</p>
                                    <p class="text-[0.75rem] text-ink-500 uppercase">
                                        {{ $resource->extension }} · {{ $resource->size_label }}
                                    </p>
                                </div>
                                <x-ui.button :href="route('resources.live-class', $resource)" variant="outline" size="sm" icon="download">
                                    Download
                                </x-ui.button>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($liveClass->recording_url && $liveClass->hasEnded())
                <section class="mt-8">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Recording</h2>
                    @php $recordingEmbed = \App\Support\YouTube::embedUrl(\App\Support\YouTube::id($liveClass->recording_url)); @endphp

                    @if ($recordingEmbed)
                        <div class="mt-3">
                            <x-ui.video-embed :src="$recordingEmbed" :title="$liveClass->title.' recording'" />
                        </div>
                    @else
                        <x-ui.button :href="$liveClass->recording_url" target="_blank" rel="noopener"
                                     variant="outline" class="mt-3" icon-after="external-link">
                            Open the recording
                        </x-ui.button>
                    @endif
                </section>
            @endif
        </div>

        {{-- When and how to join --}}
        <aside class="lg:col-span-4">
            <div class="border border-ink-200 bg-white p-5 lg:sticky lg:top-24">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Date</dt>
                        <dd class="mt-1 text-[0.9375rem] text-ink-900">{{ $liveClass->scheduled_at->format('l j F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Time</dt>
                        <dd class="mt-1 text-[0.9375rem] text-ink-900">
                            {{ $liveClass->scheduled_at->format('g:ia') }} – {{ $liveClass->ends_at->format('g:ia') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Duration</dt>
                        <dd class="mt-1 text-[0.9375rem] text-ink-900">{{ $liveClass->duration_minutes }} minutes</dd>
                    </div>
                    @if ($liveClass->instructor)
                        <div>
                            <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Led by</dt>
                            <dd class="mt-1 text-[0.9375rem] text-ink-900">{{ $liveClass->instructor->name }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 border-t border-ink-200 pt-5">
                    @if ($liveClass->isJoinable())
                        <x-ui.button :href="route('live-classes.join', $liveClass)" class="w-full" size="lg"
                                     target="_blank" rel="noopener">
                            Join the class
                        </x-ui.button>
                        <p class="mt-2 text-center text-[0.75rem] text-ink-500">Opens Zoom in a new tab</p>
                    @elseif ($liveClass->hasEnded())
                        <p class="text-center text-sm text-ink-500">This session has finished.</p>
                    @else
                        <x-ui.button disabled class="w-full" size="lg">Join opens shortly before</x-ui.button>
                        <p class="mt-2 text-center text-[0.75rem] text-ink-500">
                            {{ $liveClass->scheduled_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</x-layouts.app>
