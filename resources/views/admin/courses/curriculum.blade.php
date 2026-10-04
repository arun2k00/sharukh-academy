<x-layouts.admin :title="'Curriculum — '.$course->title" heading="Curriculum">
    <x-admin.page-header :title="$course->title" :back="route('admin.courses.edit', $course)" back-label="Course settings"
                         description="Modules run top to bottom; lessons run in order inside each module.">
        <x-slot:actions>
            <x-ui.button :href="route('admin.lessons.create', $course)" size="sm" icon="plus"
                         :disabled="$modules->isEmpty()">Add lesson</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($modules->isEmpty())
        <x-ui.empty-state icon="layers" title="No modules yet"
                          description="Start by adding the first module — lessons live inside modules." />
    @else
        <div class="space-y-5">
            @foreach ($modules as $index => $module)
                <section class="border border-ink-200 bg-white">
                    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-200 bg-sand-100 px-4 py-3">
                        <div class="flex min-w-0 items-baseline gap-3">
                            <span class="font-mono text-[0.8125rem] text-ink-400 tabular-nums">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <div class="min-w-0">
                                <h2 class="truncate font-serif text-[1.0625rem] text-ink-900">{{ $module->title }}</h2>
                                <p class="flex items-center gap-1.5 text-[0.8125rem] text-ink-500">
                                    {{ $module->lessons->count() }} {{ str('lesson')->plural($module->lessons->count()) }}
                                    @unless ($module->unlock_type === \App\Enums\ModuleUnlockType::Immediate)
                                        <span aria-hidden="true">·</span>
                                        <x-ui.icon name="lock" class="size-3 text-brass-600" />
                                        <span class="text-brass-700">
                                            {{ $module->unlock_type === \App\Enums\ModuleUnlockType::FixedDate
                                                ? 'Unlocks '.$module->unlock_at?->format('j M Y')
                                                : 'Unlocks '.$module->unlock_after_days.'d after enrollment' }}
                                        </span>
                                    @endunless
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            {{-- Ordering: explicit and reliable, no drag-and-drop to go wrong. --}}
                            <form method="POST" action="{{ route('admin.modules.move', $module) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" @disabled($loop->first) aria-label="Move module up"
                                        class="border border-ink-300 bg-white p-1.5 text-ink-600 hover:bg-sand-100 disabled:opacity-30">
                                    <x-ui.icon name="arrow-up" class="size-3.5" />
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.modules.move', $module) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" @disabled($loop->last) aria-label="Move module down"
                                        class="border border-ink-300 bg-white p-1.5 text-ink-600 hover:bg-sand-100 disabled:opacity-30">
                                    <x-ui.icon name="arrow-down" class="size-3.5" />
                                </button>
                            </form>

                            <a href="{{ route('admin.modules.edit', $module) }}"
                               class="border border-ink-300 bg-white px-2.5 py-1.5 text-[0.8125rem] text-ink-700 hover:bg-sand-100">Edit</a>

                            <x-admin.delete-form :action="route('admin.modules.destroy', $module)"
                                                 :confirm="'Delete this module and its '.$module->lessons->count().' lesson(s)?'" />
                        </div>
                    </header>

                    @if ($module->lessons->isEmpty())
                        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-5">
                            <p class="text-sm text-ink-500">No lessons in this module yet.</p>
                            <x-ui.button :href="route('admin.lessons.create', ['course' => $course, 'module' => $module->id])"
                                         size="sm" variant="outline" icon="plus">Add the first lesson</x-ui.button>
                        </div>
                    @else
                        <ul class="divide-y divide-ink-200">
                            @foreach ($module->lessons as $lesson)
                                <li class="flex flex-wrap items-center gap-3 px-4 py-3">
                                    <span class="w-6 shrink-0 text-center font-mono text-[0.75rem] text-ink-400 tabular-nums">
                                        {{ $loop->iteration }}
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('admin.lessons.edit', $lesson) }}"
                                           class="block truncate text-sm text-ink-900 hover:underline">{{ $lesson->title }}</a>
                                        <p class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[0.75rem] text-ink-500">
                                            <span>{{ $lesson->type->label() }}</span>
                                            @if ($lesson->duration_minutes > 0)
                                                <span aria-hidden="true">·</span><span>{{ $lesson->duration_label }}</span>
                                            @endif
                                            @if ($lesson->resources_count > 0)
                                                <span aria-hidden="true">·</span>
                                                <span>{{ $lesson->resources_count }} {{ str('file')->plural($lesson->resources_count) }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2">
                                        @if ($lesson->is_preview)
                                            <x-ui.badge class="bg-pine-50 text-pine-700">Preview</x-ui.badge>
                                        @endif
                                        @unless ($lesson->is_published)
                                            <x-ui.badge class="bg-sand-200 text-ink-600">Draft</x-ui.badge>
                                        @endunless

                                        <form method="POST" action="{{ route('admin.lessons.move', $lesson) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" @disabled($loop->first) aria-label="Move lesson up"
                                                    class="border border-ink-300 bg-white p-1.5 text-ink-600 hover:bg-sand-100 disabled:opacity-30">
                                                <x-ui.icon name="arrow-up" class="size-3" />
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.lessons.move', $lesson) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" @disabled($loop->last) aria-label="Move lesson down"
                                                    class="border border-ink-300 bg-white p-1.5 text-ink-600 hover:bg-sand-100 disabled:opacity-30">
                                                <x-ui.icon name="arrow-down" class="size-3" />
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.lessons.edit', $lesson) }}"
                                           class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="border-t border-ink-200 px-4 py-3">
                            <a href="{{ route('admin.lessons.create', ['course' => $course, 'module' => $module->id]) }}"
                               class="inline-flex items-center gap-1.5 text-[0.8125rem] text-pine-700 hover:underline">
                                <x-ui.icon name="plus" class="size-3.5" />
                                Add a lesson to this module
                            </a>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    @endif

    {{-- Add module --}}
    <x-ui.panel title="Add a module" class="mt-8">
        <form method="POST" action="{{ route('admin.modules.store', $course) }}" class="space-y-5">
            @csrf

            <x-ui.field label="Module title" for="module_title" :error="$errors->first('title')" required>
                <x-ui.input id="module_title" name="title" value="{{ old('title') }}" required
                            placeholder="Getting oriented" :invalid="$errors->has('title')" />
            </x-ui.field>

            <x-ui.field label="Description" for="module_description" :error="$errors->first('description')"
                        hint="Optional. Shown under the module title in the admin curriculum.">
                <x-ui.textarea id="module_description" name="description" rows="2"
                               :invalid="$errors->has('description')">{{ old('description') }}</x-ui.textarea>
            </x-ui.field>

            @include('admin.modules._unlock-fields', ['module' => null])

            <x-ui.button type="submit" icon="plus">Add module</x-ui.button>
        </form>
    </x-ui.panel>
</x-layouts.admin>
