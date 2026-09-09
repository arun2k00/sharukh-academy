<div class="grid gap-6 lg:grid-cols-12">
    <div class="lg:col-span-8">
        <x-ui.panel title="Testimonial">
            <div class="space-y-5">
                <x-ui.field label="Name" for="name" :error="$errors->first('name')" required>
                    <x-ui.input id="name" name="name" value="{{ old('name', $testimonial->name) }}" required
                                :invalid="$errors->has('name')" />
                </x-ui.field>

                <x-ui.field label="Role or company" for="role" :error="$errors->first('role')">
                    <x-ui.input id="role" name="role" value="{{ old('role', $testimonial->role) }}"
                                placeholder="Growth lead, D2C skincare" :invalid="$errors->has('role')" />
                </x-ui.field>

                <x-ui.field label="Quote" for="quote" :error="$errors->first('quote')" required>
                    <x-ui.textarea id="quote" name="quote" rows="6" required
                                   :invalid="$errors->has('quote')">{{ old('quote', $testimonial->quote) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>
    </div>

    <div class="lg:col-span-4">
        <x-ui.panel title="Placement">
            <div class="space-y-5">
                @if ($testimonial->avatar_url)
                    <img src="{{ $testimonial->avatar_url }}" alt="" class="size-20 rounded-full object-cover">
                @endif

                <x-ui.field label="Photo" for="avatar" :error="$errors->first('avatar')">
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                           class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                </x-ui.field>

                <x-ui.field label="Course" for="course_id" :error="$errors->first('course_id')"
                            hint="Course testimonials also appear on that course's page.">
                    <x-ui.select id="course_id" name="course_id" :invalid="$errors->has('course_id')">
                        <option value="">Not course specific</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                @selected((int) old('course_id', $testimonial->course_id) === $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Rating" for="rating" :error="$errors->first('rating')" required>
                    <x-ui.select id="rating" name="rating" :invalid="$errors->has('rating')">
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected((int) old('rating', $testimonial->rating ?? 5) === $i)>
                                {{ $i }} {{ str('star')->plural($i) }}
                            </option>
                        @endfor
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Position" for="sort_order" :error="$errors->first('sort_order')">
                    <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                                value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}"
                                :invalid="$errors->has('sort_order')" />
                </x-ui.field>

                <x-ui.checkbox name="is_published" value="1" label="Published"
                               :checked="(bool) old('is_published', $testimonial->is_published ?? true)" />

                <x-ui.checkbox name="is_featured" value="1" label="Show on the homepage"
                               :checked="(bool) old('is_featured', $testimonial->is_featured)" />
            </div>
        </x-ui.panel>
    </div>
</div>
