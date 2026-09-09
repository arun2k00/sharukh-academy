<x-layouts.admin title="Enrolments" heading="Enrolments">
    <x-slot:actions>
        <x-ui.button :href="route('admin.enrollments.create')" size="sm" icon="plus">Enrol a student</x-ui.button>
    </x-slot:actions>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-ui.field label="Student" for="q">
                <x-ui.input id="q" name="q" value="{{ request('q') }}" placeholder="Name or email" />
            </x-ui.field>
        </div>
        <div class="w-full sm:w-56">
            <x-ui.field label="Course" for="course">
                <x-ui.select id="course" name="course">
                    <option value="">All courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((int) request('course') === $course->id)>{{ $course->title }}</option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>
        <div class="w-full sm:w-44">
            <x-ui.field label="Status" for="status">
                <x-ui.select id="status" name="status">
                    <option value="">Any status</option>
                    @foreach (\App\Enums\EnrollmentStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
        @if (request()->hasAny(['q', 'course', 'status']))
            <a href="{{ route('admin.enrollments.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($enrollments->isEmpty())
        <x-ui.empty-state icon="graduation-cap" title="No enrolments match"
                          description="Enrol someone manually, or wait for a self-service enrolment.">
            <x-ui.button :href="route('admin.enrollments.create')">Enrol a student</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Student', 'Course', 'Progress', 'Source', 'Status', ['label' => '', 'class' => 'text-right']]">
            @foreach ($enrollments as $enrollment)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.students.show', $enrollment->user) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $enrollment->user->name }}</a>
                        <p class="text-[0.8125rem] text-ink-500">{{ $enrollment->user->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $enrollment->course->title }}</td>
                    <td class="px-4 py-3">
                        <div class="w-28">
                            <x-ui.progress :value="$enrollment->progress_percent" size="sm" />
                        </div>
                        <span class="mt-1 block text-[0.75rem] text-ink-500 tabular-nums">{{ $enrollment->progress_percent }}%</span>
                    </td>
                    <td class="px-4 py-3 text-[0.8125rem] text-ink-500">{{ $enrollment->source->label() }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <x-ui.select name="status" class="!py-1.5 !text-[0.8125rem]"
                                         onchange="this.form.submit()" aria-label="Enrolment status">
                                @foreach (\App\Enums\EnrollmentStatus::options() as $value => $label)
                                    <option value="{{ $value }}" @selected($enrollment->status->value === $value)>{{ $label }}</option>
                                @endforeach
                            </x-ui.select>
                            <noscript><button type="submit" class="text-[0.8125rem] text-pine-700">Save</button></noscript>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end">
                            <x-admin.delete-form :action="route('admin.enrollments.destroy', $enrollment)"
                                                 confirm="Remove this enrolment? The student loses access to the course."
                                                 label="Remove" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $enrollments->links() }}</div>
    @endif
</x-layouts.admin>
