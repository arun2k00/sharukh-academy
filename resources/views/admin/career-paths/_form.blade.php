@php
    $existingRows = old('courses', $careerPath->exists
        ? $careerPath->courses->map(fn ($course) => [
            'course_id' => $course->id,
            'stage_label' => $course->pivot->stage_label,
            'sort_order' => $course->pivot->sort_order,
        ])->values()->all()
        : []);
@endphp

<div class="space-y-5">
    <x-ui.field label="Title" for="title" :error="$errors->first('title')" required>
        <x-ui.input id="title" name="title" value="{{ old('title', $careerPath->title) }}" required :invalid="$errors->has('title')" />
    </x-ui.field>

    <x-ui.field label="URL slug" for="slug" :error="$errors->first('slug')" hint="Leave blank to generate it from the title.">
        <x-ui.input id="slug" name="slug" value="{{ old('slug', $careerPath->slug) }}" :invalid="$errors->has('slug')" />
    </x-ui.field>

    <x-ui.field label="Subtitle" for="subtitle" :error="$errors->first('subtitle')" hint="Shown under the title on the roadmap page.">
        <x-ui.input id="subtitle" name="subtitle" value="{{ old('subtitle', $careerPath->subtitle) }}" :invalid="$errors->has('subtitle')" />
    </x-ui.field>

    <x-ui.field label="Description" for="description" :error="$errors->first('description')" required>
        <x-ui.textarea id="description" name="description" rows="4" required
                       :invalid="$errors->has('description')">{{ old('description', $careerPath->description) }}</x-ui.textarea>
    </x-ui.field>

    <div class="grid gap-5 sm:grid-cols-2">
        <x-ui.field label="Capstone title" for="capstone_title" :error="$errors->first('capstone_title')"
                    hint='Optional final step, e.g. "Real-world project".'>
            <x-ui.input id="capstone_title" name="capstone_title" value="{{ old('capstone_title', $careerPath->capstone_title) }}"
                        :invalid="$errors->has('capstone_title')" />
        </x-ui.field>

        <x-ui.field label="Position" for="sort_order" :error="$errors->first('sort_order')">
            <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                        value="{{ old('sort_order', $careerPath->sort_order ?? 0) }}" :invalid="$errors->has('sort_order')" />
        </x-ui.field>
    </div>

    <x-ui.field label="Capstone description" for="capstone_description" :error="$errors->first('capstone_description')">
        <x-ui.textarea id="capstone_description" name="capstone_description" rows="3"
                       :invalid="$errors->has('capstone_description')">{{ old('capstone_description', $careerPath->capstone_description) }}</x-ui.textarea>
    </x-ui.field>

    <x-ui.field label="All-in-one bundle course" for="bundle_course_id" :error="$errors->first('bundle_course_id')"
                hint="Optional. A single course that covers this whole path — highlighted at the top of the roadmap page as the one-enrolment alternative.">
        <x-ui.select id="bundle_course_id" name="bundle_course_id" :invalid="$errors->has('bundle_course_id')">
            <option value="">None</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((int) old('bundle_course_id', $careerPath->bundle_course_id) === $course->id)>
                    {{ $course->title }}
                </option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <div class="flex flex-wrap gap-6">
        <x-ui.checkbox name="is_published" value="1" label="Published"
                       hint="Visible on the public roadmap page." :checked="(bool) old('is_published', $careerPath->is_published ?? true)" />
        <x-ui.checkbox name="is_featured" value="1" label="Featured"
                       hint="Highlighted on the catalogue." :checked="(bool) old('is_featured', $careerPath->is_featured ?? false)" />
    </div>
</div>

<div class="mt-8 border-t border-ink-200 pt-6" x-data="careerPathForm(@js($existingRows))">
    <h3 class="text-sm font-medium text-ink-900">Courses in this path</h3>
    <p class="mt-1 text-[0.8125rem] text-ink-500">
        Group existing courses into stages (e.g. "FOUNDATION", "DATA ENGINEERING") and order them into the
        journey a student sees on the roadmap page. Each course stays independently enrollable.
    </p>

    <div class="mt-4 space-y-3">
        <template x-for="(row, index) in rows" :key="row.key">
            <div class="grid grid-cols-1 gap-3 border border-ink-200 p-3 sm:grid-cols-[2fr_1.5fr_5rem_auto] sm:items-end">
                <label class="block">
                    <span class="mb-1 block text-[0.8125rem] text-ink-600">Course</span>
                    <select :name="`courses[${index}][course_id]`" x-model="row.course_id"
                            class="block w-full appearance-none border border-ink-300 bg-white px-3 py-2 text-sm text-ink-900 focus:border-pine-600 focus:ring-0 focus:outline-none">
                        <option value="">Choose a course…</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-1 block text-[0.8125rem] text-ink-600">Stage label</span>
                    <input type="text" :name="`courses[${index}][stage_label]`" x-model="row.stage_label"
                           placeholder="e.g. FOUNDATION"
                           class="block w-full border border-ink-300 px-3 py-2 text-sm text-ink-900 focus:border-pine-600 focus:ring-0 focus:outline-none">
                </label>

                <label class="block">
                    <span class="mb-1 block text-[0.8125rem] text-ink-600">Order</span>
                    <input type="number" min="0" :name="`courses[${index}][sort_order]`" x-model="row.sort_order"
                           class="block w-full border border-ink-300 px-3 py-2 text-sm text-ink-900 focus:border-pine-600 focus:ring-0 focus:outline-none">
                </label>

                <button type="button" @click="removeRow(index)"
                        class="justify-self-start border border-ink-300 px-3 py-2 text-[0.8125rem] text-ink-600 hover:bg-sand-100 sm:justify-self-auto">
                    Remove
                </button>
            </div>
        </template>
    </div>

    <button type="button" @click="addRow()"
            class="mt-3 border border-ink-300 px-3 py-1.5 text-[0.8125rem] text-ink-700 hover:bg-sand-100">
        + Add course
    </button>

    <x-ui.error :messages="$errors->first('courses')" />
</div>
