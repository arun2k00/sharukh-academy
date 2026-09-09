@props(['course', 'curriculum', 'currentLesson', 'completedMap' => [], 'progressPercent' => 0])

<div class="px-5 py-5">
    <x-ui.progress :value="$progressPercent" label="Course progress" />
    <p class="mt-2 text-[0.8125rem] text-ink-500">
        {{ count($completedMap) }} of {{ $curriculum->sum(fn ($module) => $module->lessons->count()) }} lessons complete
    </p>
</div>

<nav class="border-t border-ink-200" aria-label="Course contents">
    @foreach ($curriculum as $index => $module)
        @php
            $moduleLessons = $module->lessons;
            $moduleDone = $moduleLessons->every(fn ($lesson) => isset($completedMap[$lesson->id]));
        @endphp

        <div class="border-b border-ink-200">
            <div class="flex items-baseline gap-3 bg-sand-100 px-5 py-3">
                <span class="font-mono text-[0.75rem] text-ink-400 tabular-nums">
                    {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                </span>
                <h2 class="flex-1 font-sans text-[0.8125rem] font-semibold text-ink-800">{{ $module->title }}</h2>
                @if ($moduleDone && $moduleLessons->isNotEmpty())
                    <x-ui.icon name="check" class="size-3.5 text-pine-600" />
                @endif
            </div>

            <ul>
                @foreach ($moduleLessons as $lesson)
                    @php
                        $isCurrent = $currentLesson !== null && $lesson->id === $currentLesson->id;
                        $isDone = isset($completedMap[$lesson->id]);
                    @endphp

                    <li>
                        <a href="{{ route('learn.lesson', [$course, $lesson]) }}"
                           @if($isCurrent) aria-current="page" @endif
                           class="flex items-start gap-3 px-5 py-2.5 text-sm {{ $isCurrent ? 'bg-pine-50 text-ink-900' : 'text-ink-600 hover:bg-sand-100 hover:text-ink-900' }}">
                            <span class="mt-0.5 shrink-0">
                                @if ($isDone)
                                    <x-ui.icon name="check-circle" class="size-[1.05rem] text-pine-600" />
                                @elseif ($isCurrent)
                                    <x-ui.icon name="play-circle" class="size-[1.05rem] text-pine-700" />
                                @else
                                    <x-ui.icon name="circle" class="size-[1.05rem] text-ink-300" />
                                @endif
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block {{ $isCurrent ? 'font-medium' : '' }}">{{ $lesson->title }}</span>
                                <span class="mt-0.5 flex items-center gap-2 text-[0.75rem] text-ink-400">
                                    <span>{{ $lesson->type->shortLabel() }}</span>
                                    @if ($lesson->duration_minutes > 0)
                                        <span aria-hidden="true">·</span>
                                        <span class="tabular-nums">{{ $lesson->duration_label }}</span>
                                    @endif
                                    @if ($lesson->resources_count > 0)
                                        <span aria-hidden="true">·</span>
                                        <span>{{ $lesson->resources_count }} {{ str('file')->plural($lesson->resources_count) }}</span>
                                    @endif
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
