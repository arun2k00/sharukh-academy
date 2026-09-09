@php /** @var \App\Models\Lesson $lesson */ @endphp

{{-- The visible fields follow the selected lesson type; the server enforces the
     same rule, so hiding a field never decides what is required. --}}
<div x-data="{ type: '{{ old('type', $lesson->type?->value ?? 'video') }}' }" class="grid gap-6 lg:grid-cols-12">
    <div class="space-y-6 lg:col-span-8">
        <x-ui.panel title="Lesson">
            <div class="space-y-5">
                <x-ui.field label="Title" for="title" :error="$errors->first('title')" required>
                    <x-ui.input id="title" name="title" value="{{ old('title', $lesson->title) }}" required
                                :invalid="$errors->has('title')" />
                </x-ui.field>

                <x-ui.field label="URL slug" for="slug" :error="$errors->first('slug')"
                            hint="Leave blank to generate it from the title. Must be unique within this course.">
                    <x-ui.input id="slug" name="slug" value="{{ old('slug', $lesson->slug) }}"
                                :invalid="$errors->has('slug')" />
                </x-ui.field>

                <x-ui.field label="Module" for="course_module_id" :error="$errors->first('course_module_id')" required>
                    <x-ui.select id="course_module_id" name="course_module_id" :invalid="$errors->has('course_module_id')">
                        @foreach ($modules as $module)
                            <option value="{{ $module->id }}"
                                @selected((int) old('course_module_id', $lesson->course_module_id) === $module->id)>{{ $module->title }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Lesson type" for="type" :error="$errors->first('type')" required>
                    <x-ui.select id="type" name="type" x-model="type" :invalid="$errors->has('type')">
                        @foreach (\App\Enums\LessonType::options() as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('type', $lesson->type?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Description" for="description" :error="$errors->first('description')"
                            hint="Shown under the player. One or two sentences.">
                    <x-ui.textarea id="description" name="description" rows="3"
                                   :invalid="$errors->has('description')">{{ old('description', $lesson->description) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>

        {{-- Recorded video --}}
        <div x-show="type === 'video'" x-cloak>
            <x-ui.panel title="Recorded video">
                <x-ui.field label="YouTube link" for="video_url" :error="$errors->first('video_url')"
                            hint="Public or unlisted videos both work. Only the video ID is stored.">
                    <x-ui.input id="video_url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                                placeholder="https://www.youtube.com/watch?v=..." :invalid="$errors->has('video_url')" />
                </x-ui.field>

                @if ($lesson->embed_url)
                    <div class="mt-5">
                        <x-ui.video-embed :src="$lesson->embed_url" :title="$lesson->title" />
                    </div>
                @endif
            </x-ui.panel>
        </div>

        {{-- Live class --}}
        <div x-show="type === 'live'" x-cloak>
            <x-ui.panel title="Live class"
                        description="Point this lesson at a scheduled live class for the same course.">
                <x-ui.field label="Live class" for="live_class_id" :error="$errors->first('live_class_id')">
                    <x-ui.select id="live_class_id" name="live_class_id" :invalid="$errors->has('live_class_id')">
                        <option value="">Not linked</option>
                        @foreach ($liveClasses as $liveClass)
                            <option value="{{ $liveClass->id }}"
                                @selected((int) old('live_class_id', $lesson->live_class_id) === $liveClass->id)>
                                {{ $liveClass->title }} — {{ $liveClass->scheduled_at->format('j M Y') }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                @if ($liveClasses->isEmpty())
                    <p class="mt-3 text-[0.8125rem] text-ink-500">
                        This course has no live classes yet.
                        <a href="{{ route('admin.live-classes.create', ['course' => $course->id]) }}"
                           class="text-pine-700 hover:underline">Schedule one</a>.
                    </p>
                @endif
            </x-ui.panel>
        </div>

        {{-- Text lesson --}}
        <div x-show="type === 'text' || type === 'quiz' || type === 'resource'" x-cloak>
            <x-ui.panel title="Lesson content"
                        description="Separate paragraphs with a blank line. Wrap a line in ** for a subheading.">
                <x-ui.field label="Content" for="content" :error="$errors->first('content')">
                    <x-ui.textarea id="content" name="content" rows="14"
                                   :invalid="$errors->has('content')">{{ old('content', $lesson->content) }}</x-ui.textarea>
                </x-ui.field>
            </x-ui.panel>
        </div>
    </div>

    <div class="space-y-6 lg:col-span-4">
        <x-ui.panel title="Settings">
            <div class="space-y-5">
                <x-ui.field label="Duration (minutes)" for="duration_minutes" :error="$errors->first('duration_minutes')"
                            hint="Used for the course length shown publicly." required>
                    <x-ui.input id="duration_minutes" name="duration_minutes" type="number" min="0" max="1440"
                                value="{{ old('duration_minutes', $lesson->duration_minutes ?? 0) }}" required
                                :invalid="$errors->has('duration_minutes')" />
                </x-ui.field>

                <x-ui.field label="Position in module" for="sort_order" :error="$errors->first('sort_order')"
                            hint="Leave blank to place it last.">
                    <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                                value="{{ old('sort_order', $lesson->sort_order) }}" :invalid="$errors->has('sort_order')" />
                </x-ui.field>

                <x-ui.checkbox name="is_published" value="1" label="Published"
                               hint="Unpublished lessons are hidden from students and excluded from progress."
                               :checked="(bool) old('is_published', $lesson->is_published ?? true)" />

                <x-ui.checkbox name="is_preview" value="1" label="Free preview"
                               hint="Anyone can watch this lesson from the public course page."
                               :checked="(bool) old('is_preview', $lesson->is_preview)" />

                <x-ui.checkbox name="is_required" value="1" label="Required for completion"
                               hint="Uncheck for optional material when the course only requires marked lessons."
                               :checked="(bool) old('is_required', $lesson->is_required ?? true)" />
            </div>
        </x-ui.panel>
    </div>
</div>
