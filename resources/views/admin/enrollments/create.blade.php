<x-layouts.admin title="Enroll a student" heading="Enroll a student">
    <x-admin.page-header title="Manual enrollment" :back="route('admin.enrollments.index')" back-label="All enrollments"
                         description="Grants immediate access without any payment being processed." />

    <div class="max-w-2xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.enrollments.store') }}" class="space-y-5">
                @csrf

                <x-ui.field label="Student" for="user_id" :error="$errors->first('user_id')" required>
                    <x-ui.select id="user_id" name="user_id" :invalid="$errors->has('user_id')">
                        <option value="">Choose a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected((int) old('user_id') === $student->id)>
                                {{ $student->name }} — {{ $student->email }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Course" for="course_id" :error="$errors->first('course_id')" required>
                    <x-ui.select id="course_id" name="course_id" :invalid="$errors->has('course_id')">
                        <option value="">Choose a course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected((int) old('course_id') === $course->id)>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Status" for="status" :error="$errors->first('status')" required>
                    <x-ui.select id="status" name="status" :invalid="$errors->has('status')">
                        @foreach (\App\Enums\EnrollmentStatus::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Access expires" for="expires_at" :error="$errors->first('expires_at')"
                            hint="Optional. Leave blank for access that does not expire.">
                    <x-ui.input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at') }}"
                                :invalid="$errors->has('expires_at')" />
                </x-ui.field>

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit">Enroll student</x-ui.button>
                    <x-ui.button :href="route('admin.enrollments.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
