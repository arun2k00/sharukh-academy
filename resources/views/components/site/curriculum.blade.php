@props(['course', 'modules', 'hasAccess' => false])

{{-- Public curriculum: shows the shape of the course and unlocks preview lessons. --}}
<div x-data="{ open: 0 }" class="divide-y divide-ink-200 border-y border-ink-200">
    @foreach ($modules as $index => $module)
        <div>
            <h3>
                <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                        :aria-expanded="open === {{ $index }} ? 'true' : 'false'"
                        class="flex w-full items-center gap-4 py-4 text-left">
                    <span class="font-mono text-[0.8125rem] text-ink-400 tabular-nums">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="flex-1 font-sans text-[0.95rem] font-medium text-ink-900">{{ $module->title }}</span>
                    <span class="hidden text-[0.8125rem] text-ink-500 sm:block">{{ $module->lessons->count() }} lessons</span>
                    <x-ui.icon name="chevron-down" class="size-4 shrink-0 text-ink-400 transition-transform"
                               ::class="open === {{ $index }} && 'rotate-180'" />
                </button>
            </h3>

            <div x-show="open === {{ $index }}" x-collapse x-cloak>
                <ul class="pb-4">
                    @forelse ($module->lessons as $lesson)
                        <li class="flex items-center gap-3 border-t border-ink-100 py-2.5 pl-10">
                            @if ($hasAccess || $lesson->isFreePreview())
                                <x-ui.icon name="play-circle" class="size-4 shrink-0 text-pine-600" />
                            @else
                                <x-ui.icon name="lock" class="size-4 shrink-0 text-ink-300" />
                            @endif

                            <span class="min-w-0 flex-1 text-sm text-ink-700">
                                @if ($lesson->isFreePreview())
                                    <a href="{{ route('courses.preview', [$course, $lesson]) }}"
                                       class="text-pine-700 underline-offset-2 hover:underline">{{ $lesson->title }}</a>
                                @else
                                    {{ $lesson->title }}
                                @endif
                            </span>

                            @if ($lesson->isFreePreview())
                                <x-ui.badge class="bg-pine-50 text-pine-700">Preview</x-ui.badge>
                            @elseif ($lesson->type === \App\Enums\LessonType::Live)
                                <x-ui.badge class="bg-brass-50 text-brass-700">Live</x-ui.badge>
                            @endif

                            <span class="w-12 shrink-0 text-right text-[0.8125rem] text-ink-400 tabular-nums">
                                {{ $lesson->duration_minutes > 0 ? $lesson->duration_label : '—' }}
                            </span>
                        </li>
                    @empty
                        <li class="border-t border-ink-100 py-3 pl-10 text-sm text-ink-500">Lessons are being finalised.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endforeach
</div>
