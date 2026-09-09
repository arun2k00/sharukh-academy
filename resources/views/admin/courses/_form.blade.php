@php
    /** @var \App\Models\Course $course */
    $listToText = fn (?array $items): string => $items ? implode("\n", $items) : '';
@endphp

<div class="grid gap-6 lg:grid-cols-12">
    <div class="space-y-6 lg:col-span-8">
        <x-ui.panel title="Course details">
            <div class="space-y-5">
                <x-ui.field label="Title" for="title" :error="$errors->first('title')" required>
                    <x-ui.input id="title" name="title" value="{{ old('title', $course->title) }}" required
                                :invalid="$errors->has('title')" />
                </x-ui.field>

                <x-ui.field label="URL slug" for="slug" :error="$errors->first('slug')"
                            hint="Leave blank to generate it from the title.">
                    <x-ui.input id="slug" name="slug" value="{{ old('slug', $course->slug) }}"
                                placeholder="performance-marketing-foundations" :invalid="$errors->has('slug')" />
                </x-ui.field>

                <x-ui.field label="Short description" for="short_description"
                            :error="$errors->first('short_description')"
                            hint="One or two sentences, shown on course cards and search results." required>
                    <x-ui.textarea id="short_description" name="short_description" rows="3" required
                                   :invalid="$errors->has('short_description')">{{ old('short_description', $course->short_description) }}</x-ui.textarea>
                </x-ui.field>

                <x-ui.field label="Full description" for="description" :error="$errors->first('description')"
                            hint="Separate paragraphs with a blank line.">
                    <x-ui.textarea id="description" name="description" rows="10"
                                   :invalid="$errors->has('description')">{{ old('description', $course->description) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Sales content"
                    description="One item per line. These appear on the public course page.">
            <div class="space-y-5">
                <x-ui.field label="What students will be able to do" for="outcomes" :error="$errors->first('outcomes')">
                    <x-ui.textarea id="outcomes" name="outcomes" rows="6"
                                   :invalid="$errors->has('outcomes')">{{ old('outcomes', $listToText($course->outcomes)) }}</x-ui.textarea>
                </x-ui.field>

                <x-ui.field label="Who it is for" for="audience" :error="$errors->first('audience')">
                    <x-ui.textarea id="audience" name="audience" rows="4"
                                   :invalid="$errors->has('audience')">{{ old('audience', $listToText($course->audience)) }}</x-ui.textarea>
                </x-ui.field>

                <x-ui.field label="What they need first" for="requirements" :error="$errors->first('requirements')">
                    <x-ui.textarea id="requirements" name="requirements" rows="4"
                                   :invalid="$errors->has('requirements')">{{ old('requirements', $listToText($course->requirements)) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>
    </div>

    <div class="space-y-6 lg:col-span-4">
        <x-ui.panel title="Publishing">
            <div class="space-y-5">
                <x-ui.field label="Status" for="status" :error="$errors->first('status')" required>
                    <x-ui.select id="status" name="status" :invalid="$errors->has('status')">
                        @foreach (\App\Enums\CourseStatus::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $course->status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.checkbox name="is_featured" value="1" label="Feature on the homepage"
                               :checked="(bool) old('is_featured', $course->is_featured)" />

                <x-ui.field label="Catalogue position" for="sort_order" :error="$errors->first('sort_order')"
                            hint="Lower numbers appear first.">
                    <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                                value="{{ old('sort_order', $course->sort_order ?? 0) }}" :invalid="$errors->has('sort_order')" />
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Classification">
            <div class="space-y-5">
                <x-ui.field label="Category" for="category_id" :error="$errors->first('category_id')">
                    <x-ui.select id="category_id" name="category_id" :invalid="$errors->has('category_id')">
                        <option value="">Uncategorised</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected((int) old('category_id', $course->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Instructor" for="instructor_id" :error="$errors->first('instructor_id')">
                    <x-ui.select id="instructor_id" name="instructor_id" :invalid="$errors->has('instructor_id')">
                        <option value="">Not assigned</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                @selected((int) old('instructor_id', $course->instructor_id) === $instructor->id)>{{ $instructor->name }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Level" for="level" :error="$errors->first('level')" required>
                    <x-ui.select id="level" name="level" :invalid="$errors->has('level')">
                        @foreach (\App\Enums\CourseLevel::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('level', $course->level?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Language" for="language" :error="$errors->first('language')" required>
                    <x-ui.input id="language" name="language" value="{{ old('language', $course->language ?? 'English') }}"
                                required :invalid="$errors->has('language')" />
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Pricing">
            <div class="space-y-5">
                <x-ui.field label="Price ({{ config('academy.currency') }})" for="price" :error="$errors->first('price')"
                            hint="Enter 0 to make the course free." required>
                    <x-ui.input id="price" name="price" type="number" min="0" step="0.01"
                                value="{{ old('price', $course->price ?? 0) }}" required :invalid="$errors->has('price')" />
                </x-ui.field>

                <x-ui.field label="Compare-at price" for="compare_at_price" :error="$errors->first('compare_at_price')"
                            hint="Optional. Shown struck through beside the price.">
                    <x-ui.input id="compare_at_price" name="compare_at_price" type="number" min="0" step="0.01"
                                value="{{ old('compare_at_price', $course->compare_at_price) }}"
                                :invalid="$errors->has('compare_at_price')" />
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Media">
            <div class="space-y-5">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="aspect-[16/9] w-full border border-ink-200 object-cover">
                @endif

                <x-ui.field label="Thumbnail" for="thumbnail" :error="$errors->first('thumbnail')"
                            hint="16:9 works best. JPG, PNG or WebP up to 4 MB.">
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                           class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                </x-ui.field>

                <x-ui.field label="Preview video" for="preview_video_url" :error="$errors->first('preview_video_url')"
                            hint="A YouTube link shown on the course page.">
                    <x-ui.input id="preview_video_url" name="preview_video_url"
                                value="{{ old('preview_video_url', $course->preview_video_url) }}"
                                placeholder="https://www.youtube.com/watch?v=..." :invalid="$errors->has('preview_video_url')" />
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Certification">
            <div class="space-y-5">
                <x-ui.checkbox name="certificate_enabled" value="1" label="Issue a certificate on completion"
                               :checked="(bool) old('certificate_enabled', $course->certificate_enabled ?? true)" />

                <x-ui.field label="Completion threshold (%)" for="completion_threshold"
                            :error="$errors->first('completion_threshold')"
                            hint="Percentage of lessons required before the certificate is issued." required>
                    <x-ui.input id="completion_threshold" name="completion_threshold" type="number" min="1" max="100"
                                value="{{ old('completion_threshold', $course->completion_threshold ?? 100) }}"
                                required :invalid="$errors->has('completion_threshold')" />
                </x-ui.field>
            </div>
        </x-ui.panel>
    </div>
</div>
