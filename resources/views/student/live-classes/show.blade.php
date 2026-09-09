@php
    $isLiveNow = $liveClass->hasStarted() && ! $liveClass->hasEnded();
    $heroImage = $liveClass->banner_url ?? $liveClass->course->thumbnail_url;
@endphp

<x-layouts.public :title="$liveClass->title" :description="$liveClass->description ?: ($liveClass->title.' — '.$liveClass->course->title)">
    <section class="border-b border-ink-200 bg-white">
        <div class="mx-auto max-w-5xl px-5 pt-8 lg:px-8">
            <x-ui.breadcrumbs :items="[
                ['label' => 'Live classes', 'url' => route('live-classes.index')],
                ['label' => $liveClass->title],
            ]" />
        </div>

        <div class="mx-auto grid max-w-5xl gap-10 px-5 py-10 lg:grid-cols-12 lg:gap-12 lg:px-8 lg:py-14">
            <div class="lg:col-span-7">
                @if ($isLiveNow && $canViewFull)
                    <x-ui.badge class="bg-red-600 text-white">
                        <span class="size-1.5 animate-pulse rounded-full bg-white"></span>
                        Live now
                    </x-ui.badge>
                @elseif ($liveClass->hasEnded())
                    <x-ui.badge class="bg-ink-100 text-ink-500">Session ended</x-ui.badge>
                @else
                    <x-ui.badge class="bg-sand-200 text-ink-700">Upcoming live class</x-ui.badge>
                @endif

                <p class="mt-4 text-[0.8125rem] text-pine-700">{{ $liveClass->course->title }}</p>
                <h1 class="mt-1.5 text-[1.85rem] leading-tight sm:text-[2.15rem]">{{ $liveClass->title }}</h1>

                @if ($liveClass->description)
                    <p class="mt-4 max-w-xl text-[0.975rem]/7 text-ink-600">{{ $liveClass->description }}</p>
                @endif

                <dl class="mt-7 grid grid-cols-2 gap-x-8 gap-y-4 border-t border-ink-200 pt-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Date</dt>
                        <dd class="mt-1 text-sm text-ink-900">{{ $liveClass->scheduled_at->format('j F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Time</dt>
                        <dd class="mt-1 text-sm text-ink-900">{{ $liveClass->scheduled_at->format('g:ia') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Duration</dt>
                        <dd class="mt-1 text-sm text-ink-900">{{ $liveClass->duration_minutes }} minutes</dd>
                    </div>
                    @if ($liveClass->instructor ?? $liveClass->course->instructor)
                        <div>
                            <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Instructor</dt>
                            <dd class="mt-1 text-sm text-ink-900">{{ ($liveClass->instructor ?? $liveClass->course->instructor)->name }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    <x-ui.share-menu :url="route('live-classes.show', $liveClass)" :title="$liveClass->title" />
                    @if ($canViewFull)
                        <x-ui.button :href="route('live-classes.calendar', $liveClass)" variant="outline" size="sm" icon="calendar">
                            Add to calendar
                        </x-ui.button>
                    @endif
                </div>
            </div>

            {{-- Status-driven action panel --}}
            <div class="lg:col-span-5">
                <div class="border border-ink-200 bg-white p-6 lg:sticky lg:top-24">
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="" class="-mx-6 -mt-6 mb-6 aspect-[16/9] w-[calc(100%+3rem)] object-cover">
                    @endif

                    @if (! $canViewFull)
                        {{-- Guest or authenticated-but-not-enrolled: never reveal the Zoom URL or internal materials. --}}
                        @guest
                            <p class="text-center text-sm text-ink-600">Sign in to join this live class.</p>
                            <x-ui.button :href="route('login', ['redirect' => route('live-classes.show', $liveClass)])"
                                         class="mt-4 w-full" size="lg">
                                Login to join
                            </x-ui.button>
                            <p class="mt-3 text-center text-[0.8125rem] text-ink-500">
                                New here?
                                <a href="{{ route('register') }}" class="text-pine-700 hover:underline">Create an account</a>
                            </p>
                        @else
                            <p class="text-center text-sm text-ink-600">
                                This live class is available to students enrolled in {{ $liveClass->course->title }}.
                            </p>
                            <x-ui.button :href="route('courses.show', $liveClass->course)" class="mt-4 w-full" size="lg">
                                View course
                            </x-ui.button>
                        @endguest
                    @elseif ($liveClass->hasEnded())
                        <p class="text-center text-sm text-ink-600">This live session has ended.</p>
                        @if ($liveClass->recording_url)
                            @php $recordingEmbed = \App\Support\YouTube::embedUrl(\App\Support\YouTube::id($liveClass->recording_url)); @endphp
                            @if ($recordingEmbed)
                                <div class="mt-4">
                                    <x-ui.video-embed :src="$recordingEmbed" :title="$liveClass->title.' recording'" />
                                </div>
                            @else
                                <x-ui.button :href="$liveClass->recording_url" target="_blank" rel="noopener" class="mt-4 w-full" size="lg" icon="play">
                                    Watch recording
                                </x-ui.button>
                            @endif
                        @else
                            <p class="mt-3 text-center text-[0.8125rem] text-ink-500">Recording will be available soon.</p>
                        @endif
                    @elseif ($liveClass->isJoinable())
                        <x-ui.badge class="mx-auto flex w-fit bg-red-600 text-white">Live now</x-ui.badge>
                        <x-ui.button :href="route('live-classes.join', $liveClass)" target="_blank" rel="noopener"
                                     class="mt-4 w-full" size="lg">
                            Join live class
                        </x-ui.button>
                        <p class="mt-3 text-center text-[0.75rem] text-ink-500">Opens Zoom in a new tab</p>
                    @else
                        <x-ui.countdown :target="$liveClass->scheduled_at" label="Starts in" />
                        <x-ui.button disabled class="mt-6 w-full" size="lg">Join class</x-ui.button>
                        <p class="mt-3 text-center text-[0.8125rem] text-ink-500">
                            Joining will be available {{ config('academy.live_join_window') }} minutes before the class starts.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($canViewFull && ($liveClass->preparation_notes || $liveClass->resources->isNotEmpty()))
        <section class="mx-auto max-w-5xl px-5 py-12 lg:px-8">
            @if ($liveClass->preparation_notes)
                <div class="border-l-2 border-pine-600 bg-pine-50 p-5">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-pine-800 uppercase">
                        Before the session
                    </h2>
                    <p class="mt-2.5 text-[0.9375rem]/7 text-ink-700">{{ $liveClass->preparation_notes }}</p>
                </div>
            @endif

            @if ($liveClass->resources->isNotEmpty())
                <div class="mt-8">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">
                        Class materials
                    </h2>
                    <ul class="mt-3 divide-y divide-ink-200 border-y border-ink-200">
                        @foreach ($liveClass->resources as $resource)
                            <li class="flex items-center gap-4 py-3">
                                <x-ui.icon name="file-text" class="size-5 shrink-0 text-ink-400" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm text-ink-900">{{ $resource->title }}</p>
                                    <p class="text-[0.75rem] text-ink-500 uppercase">{{ $resource->extension }} · {{ $resource->size_label }}</p>
                                </div>
                                <x-ui.button :href="route('resources.live-class', $resource)" variant="outline" size="sm" icon="download">
                                    Download
                                </x-ui.button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif
</x-layouts.public>
