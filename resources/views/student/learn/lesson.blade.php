<x-layouts.learn :title="$lesson->title" :course="$course">
    <x-slot:header-meta>
        <span class="hidden text-[0.8125rem] text-ink-500 sm:block tabular-nums">{{ $progressPercent }}% complete</span>
    </x-slot:header-meta>

    <x-slot:sidebar>
        <x-student.lesson-sidebar :course="$course" :curriculum="$curriculum" :current-lesson="$lesson"
                                  :completed-map="$completedMap" :progress-percent="$progressPercent" />
    </x-slot:sidebar>

    @php
        $isComplete = isset($completedMap[$lesson->id]);
        $frameId = 'lesson-player-'.$lesson->id;
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-6 lg:px-8 lg:py-8"
         x-data="lessonPlayer({
             frameId: '{{ $frameId }}',
             endpoint: '{{ route('lessons.watch', [$course, $lesson]) }}',
             startPercent: {{ $watchedPercent }},
             alreadyComplete: {{ $isComplete ? 'true' : 'false' }},
         })">

        <x-ui.flash />

        <p class="text-[0.8125rem] text-ink-500">{{ $lesson->module->title }}</p>
        <h1 class="mt-1.5 text-[1.5rem] leading-tight">{{ $lesson->title }}</h1>

        {{-- Lesson body: the shape depends entirely on the lesson type --}}
        <div class="mt-6">
            @switch ($lesson->type)
                @case (\App\Enums\LessonType::Video)
                    <x-ui.video-embed :src="$lesson->embed_url" :title="$lesson->title" :id="$frameId" />
                    @break

                @case (\App\Enums\LessonType::Live)
                    @if ($lesson->liveClass)
                        <div class="border border-ink-200 bg-white p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <x-ui.badge :class="$lesson->liveClass->status->badgeClasses()">
                                        {{ $lesson->liveClass->status->label() }}
                                    </x-ui.badge>
                                    <h2 class="mt-3 font-serif text-lg text-ink-900">{{ $lesson->liveClass->title }}</h2>
                                    <p class="mt-1.5 text-sm text-ink-600">
                                        {{ $lesson->liveClass->scheduled_at->format('l j F, g:ia') }} ·
                                        {{ $lesson->liveClass->duration_minutes }} minutes
                                    </p>
                                </div>
                                <x-ui.button :href="route('live-classes.show', $lesson->liveClass)" variant="outline" size="sm">
                                    Class details
                                </x-ui.button>
                            </div>
                        </div>
                    @else
                        <x-ui.alert variant="info">This live class has not been scheduled yet.</x-ui.alert>
                    @endif
                    @break

                @case (\App\Enums\LessonType::Text)
                    <div class="prose-academy border-y border-ink-200 py-8">
                        @foreach (preg_split('/\r\n\r\n|\n\n/', (string) $lesson->content) as $paragraph)
                            @if (trim($paragraph) !== '')
                                @if (str_starts_with(trim($paragraph), '**') && str_ends_with(trim($paragraph), '**'))
                                    <h3>{{ trim($paragraph, "* \n") }}</h3>
                                @else
                                    <p>{!! preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e(trim($paragraph))) !!}</p>
                                @endif
                            @endif
                        @endforeach
                    </div>
                    @break

                @default
                    @if ($lesson->content)
                        <div class="prose-academy border-y border-ink-200 py-8">{{ $lesson->content }}</div>
                    @endif
            @endswitch
        </div>

        @if ($lesson->description)
            <p class="mt-6 text-[0.975rem]/7 text-ink-700">{{ $lesson->description }}</p>
        @endif

        {{-- Resources --}}
        @if ($lesson->resources->isNotEmpty())
            <section class="mt-9">
                <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">
                    Lesson resources
                </h2>
                <ul class="mt-3 divide-y divide-ink-200 border-y border-ink-200">
                    @foreach ($lesson->resources as $resource)
                        <li class="flex items-center gap-4 py-3">
                            <x-ui.icon name="file-text" class="size-5 shrink-0 text-ink-400" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm text-ink-900">{{ $resource->title }}</p>
                                <p class="text-[0.75rem] text-ink-500 uppercase">
                                    {{ $resource->extension }} · {{ $resource->size_label }}
                                </p>
                            </div>
                            <x-ui.button :href="route('resources.lesson', $resource)" variant="outline" size="sm" icon="download">
                                Download
                            </x-ui.button>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Completion --}}
        <section class="mt-9 border border-ink-200 bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <template x-if="complete">
                        <span class="flex items-center gap-2 text-sm font-medium text-pine-700">
                            <x-ui.icon name="check-circle" class="size-5" />
                            Lesson complete
                        </span>
                    </template>
                    <template x-if="! complete">
                        <span class="text-sm text-ink-600">
                            Mark this lesson complete when you are done
                            <span x-show="percent > 0" class="text-ink-400" x-cloak>
                                — watched <span x-text="percent"></span>%
                            </span>
                        </span>
                    </template>
                </div>

                @if ($isComplete)
                    <form method="POST" action="{{ route('lessons.uncomplete', [$course, $lesson]) }}">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="outline" size="sm">Mark as not complete</x-ui.button>
                    </form>
                @else
                    <form method="POST" action="{{ route('lessons.complete', [$course, $lesson]) }}">
                        @csrf
                        <x-ui.button type="submit" icon="check">
                            {{ $nextLesson ? 'Complete and continue' : 'Mark complete' }}
                        </x-ui.button>
                    </form>
                @endif
            </div>
        </section>

        {{-- Certificate prompt once the course is finished --}}
        @if ($certificate)
            <x-ui.alert variant="success" class="mt-6" title="You have completed this course">
                <p class="mt-1">Your certificate is ready.</p>
                <a href="{{ route('certificates.show', $certificate) }}"
                   class="mt-2 inline-flex items-center gap-1.5 text-[0.8125rem] font-medium text-pine-700 hover:underline">
                    View certificate
                    <x-ui.icon name="arrow-right" class="size-3.5" />
                </a>
            </x-ui.alert>
        @endif

        {{-- Lesson navigation --}}
        <nav class="mt-9 flex items-stretch justify-between gap-4 border-t border-ink-200 pt-6">
            @if ($previousLesson)
                <a href="{{ route('learn.lesson', [$course, $previousLesson]) }}"
                   class="group flex max-w-[45%] items-center gap-3 text-left">
                    <x-ui.icon name="arrow-left" class="size-4 shrink-0 text-ink-400 transition-transform group-hover:-translate-x-0.5" />
                    <span class="min-w-0">
                        <span class="block text-[0.75rem] text-ink-500">Previous</span>
                        <span class="block truncate text-sm text-ink-800 group-hover:text-ink-950">{{ $previousLesson->title }}</span>
                    </span>
                </a>
            @else
                <span></span>
            @endif

            @if ($nextLesson)
                <a href="{{ route('learn.lesson', [$course, $nextLesson]) }}"
                   class="group flex max-w-[45%] items-center gap-3 text-right">
                    <span class="min-w-0">
                        <span class="block text-[0.75rem] text-ink-500">Next</span>
                        <span class="block truncate text-sm text-ink-800 group-hover:text-ink-950">{{ $nextLesson->title }}</span>
                    </span>
                    <x-ui.icon name="arrow-right" class="size-4 shrink-0 text-ink-400 transition-transform group-hover:translate-x-0.5" />
                </a>
            @endif
        </nav>
    </div>
</x-layouts.learn>
