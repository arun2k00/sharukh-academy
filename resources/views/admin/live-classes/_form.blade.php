@php /** @var \App\Models\LiveClass $liveClass */ @endphp

<div class="grid gap-6 lg:grid-cols-12">
    <div class="space-y-6 lg:col-span-8">
        <x-ui.panel title="Session details">
            <div class="space-y-5">
                <x-ui.field label="Title" for="title" :error="$errors->first('title')" required>
                    <x-ui.input id="title" name="title" value="{{ old('title', $liveClass->title) }}" required
                                placeholder="Live: reviewing your market sizing" :invalid="$errors->has('title')" />
                </x-ui.field>

                <x-ui.field label="URL slug" for="slug" :error="$errors->first('slug')"
                            hint="Leave blank to generate it from the title. Must be unique across all live classes.">
                    <x-ui.input id="slug" name="slug" value="{{ old('slug', $liveClass->slug) }}"
                                :invalid="$errors->has('slug')" />
                </x-ui.field>

                <x-ui.field label="Course" for="course_id" :error="$errors->first('course_id')"
                            hint="Only students enrolled in this course can see or join the session." required>
                    <x-ui.select id="course_id" name="course_id" :invalid="$errors->has('course_id')">
                        <option value="">Choose a course</option>
                        @foreach ($courses as $courseOption)
                            <option value="{{ $courseOption->id }}"
                                @selected((int) old('course_id', $liveClass->course_id) === $courseOption->id)>{{ $courseOption->title }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Description" for="description" :error="$errors->first('description')">
                    <x-ui.textarea id="description" name="description" rows="5"
                                   :invalid="$errors->has('description')">{{ old('description', $liveClass->description) }}</x-ui.textarea>
                </x-ui.field>

                <x-ui.field label="How to prepare" for="preparation_notes" :error="$errors->first('preparation_notes')"
                            hint="Shown prominently to students before the session.">
                    <x-ui.textarea id="preparation_notes" name="preparation_notes" rows="3"
                                   :invalid="$errors->has('preparation_notes')">{{ old('preparation_notes', $liveClass->preparation_notes) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Zoom"
                    description="The meeting link is never rendered into a page — students are redirected to it only inside the join window.">
            <div class="space-y-5">
                <x-ui.field label="Meeting link" for="zoom_url" :error="$errors->first('zoom_url')"
                            hint="Must be an https link on a Zoom domain.">
                    <x-ui.input id="zoom_url" name="zoom_url" value="{{ old('zoom_url', $liveClass->zoom_url) }}"
                                placeholder="https://us02web.zoom.us/j/1234567890" :invalid="$errors->has('zoom_url')" />
                </x-ui.field>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.field label="Meeting ID" for="zoom_meeting_id" :error="$errors->first('zoom_meeting_id')">
                        <x-ui.input id="zoom_meeting_id" name="zoom_meeting_id"
                                    value="{{ old('zoom_meeting_id', $liveClass->zoom_meeting_id) }}"
                                    :invalid="$errors->has('zoom_meeting_id')" />
                    </x-ui.field>

                    <x-ui.field label="Passcode" for="zoom_passcode" :error="$errors->first('zoom_passcode')">
                        <x-ui.input id="zoom_passcode" name="zoom_passcode"
                                    value="{{ old('zoom_passcode', $liveClass->zoom_passcode) }}"
                                    :invalid="$errors->has('zoom_passcode')" />
                    </x-ui.field>
                </div>

                <x-ui.field label="Recording link" for="recording_url" :error="$errors->first('recording_url')"
                            hint="Add after the session. A YouTube link is embedded; anything else opens in a new tab.">
                    <x-ui.input id="recording_url" name="recording_url"
                                value="{{ old('recording_url', $liveClass->recording_url) }}"
                                :invalid="$errors->has('recording_url')" />
                </x-ui.field>
            </div>
        </x-ui.panel>
    </div>

    <div class="space-y-6 lg:col-span-4">
        <x-ui.panel title="Schedule">
            <div class="space-y-5">
                <x-ui.field label="Date and time" for="scheduled_at" :error="$errors->first('scheduled_at')" required>
                    <x-ui.input id="scheduled_at" name="scheduled_at" type="datetime-local" required
                                value="{{ old('scheduled_at', $liveClass->scheduled_at?->format('Y-m-d\TH:i')) }}"
                                :invalid="$errors->has('scheduled_at')" />
                </x-ui.field>

                <x-ui.field label="Duration (minutes)" for="duration_minutes" :error="$errors->first('duration_minutes')" required>
                    <x-ui.input id="duration_minutes" name="duration_minutes" type="number" min="5" max="600" required
                                value="{{ old('duration_minutes', $liveClass->duration_minutes ?? 60) }}"
                                :invalid="$errors->has('duration_minutes')" />
                </x-ui.field>

                <x-ui.field label="Status" for="status" :error="$errors->first('status')" required>
                    <x-ui.select id="status" name="status" :invalid="$errors->has('status')">
                        @foreach (\App\Enums\LiveClassStatus::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $liveClass->status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Instructor" for="instructor_id" :error="$errors->first('instructor_id')">
                    <x-ui.select id="instructor_id" name="instructor_id" :invalid="$errors->has('instructor_id')">
                        <option value="">Course instructor</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}"
                                @selected((int) old('instructor_id', $liveClass->instructor_id) === $instructor->id)>{{ $instructor->name }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.checkbox name="is_featured" value="1" label="Feature on the homepage"
                               :checked="(bool) old('is_featured', $liveClass->is_featured)" />

                <p class="border-t border-ink-200 pt-4 text-[0.8125rem] text-ink-500">
                    The join button unlocks {{ config('academy.live_join_window') }} minutes before the start time.
                </p>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Banner image" description="Shown at the top of this class's public share page.">
            @if ($liveClass->banner_url)
                <img src="{{ $liveClass->banner_url }}" alt="" class="mb-4 aspect-[16/9] w-full border border-ink-200 object-cover">
            @endif
            <x-ui.field label="Banner" for="banner" :error="$errors->first('banner')">
                <input id="banner" name="banner" type="file" accept="image/*"
                       class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
            </x-ui.field>
        </x-ui.panel>
    </div>
</div>
