<x-layouts.admin :title="$student->name" heading="Student">
    <x-admin.page-header :title="$student->name" :back="route('admin.students.index')" back-label="All students"
                         :description="$student->email">
        <x-slot:actions>
            <x-ui.button :href="route('admin.students.edit', $student)" variant="outline" size="sm">Edit account</x-ui.button>
            <x-ui.button :href="route('admin.enrollments.create')" size="sm" icon="plus">Enroll in a course</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <dl class="grid divide-y divide-ink-200 border border-ink-200 bg-white sm:grid-cols-4 sm:divide-x sm:divide-y-0">
        <x-ui.stat label="Enrollments" :value="$student->enrollments->count()" />
        <x-ui.stat label="Lessons completed" :value="$lessonsCompleted" />
        <x-ui.stat label="Certificates" :value="$student->certificates->count()" />
        <x-ui.stat label="Last active"
                   :value="$student->last_login_at?->diffForHumans(short: true) ?? 'Never'" />
    </dl>

    <div class="mt-8 grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-ui.panel title="Enrollments and progress" :padded="false">
                @if ($student->enrollments->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Not enrolled in anything yet.</p>
                @else
                    <ul class="divide-y divide-ink-200">
                        @foreach ($student->enrollments as $enrollment)
                            <li class="px-5 py-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.courses.edit', $enrollment->course) }}"
                                           class="text-sm font-medium text-ink-900 hover:underline">{{ $enrollment->course->title }}</a>
                                        <p class="mt-0.5 text-[0.8125rem] text-ink-500">
                                            {{ $enrollment->source->label() }} ·
                                            enrolled {{ $enrollment->enrolled_at?->format('j M Y') ?? '—' }}
                                        </p>
                                    </div>
                                    <x-ui.badge :class="$enrollment->status->badgeClasses()">
                                        {{ $enrollment->status->label() }}
                                    </x-ui.badge>
                                </div>

                                <div class="mt-3 max-w-sm">
                                    <x-ui.progress :value="$enrollment->progress_percent" size="sm" />
                                </div>

                                <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}"
                                      class="mt-4 flex flex-wrap items-end gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <div class="w-40">
                                        <x-ui.label for="status-{{ $enrollment->id }}">Change status</x-ui.label>
                                        <x-ui.select id="status-{{ $enrollment->id }}" name="status" class="mt-1.5">
                                            @foreach (\App\Enums\EnrollmentStatus::options() as $value => $label)
                                                <option value="{{ $value }}" @selected($enrollment->status->value === $value)>{{ $label }}</option>
                                            @endforeach
                                        </x-ui.select>
                                    </div>
                                    <x-ui.button type="submit" variant="outline" size="sm">Update</x-ui.button>
                                    <x-admin.delete-form :action="route('admin.enrollments.destroy', $enrollment)"
                                                         class="pb-2" label="Remove enrollment"
                                                         confirm="Remove this enrollment? The student loses access." />
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.panel>
        </div>

        <div class="space-y-6 lg:col-span-4">
            <x-ui.panel title="Account">
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">Role</dt>
                        <dd class="text-ink-900">{{ $student->role->label() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">Email verified</dt>
                        <dd class="text-ink-900">{{ $student->hasVerifiedEmail() ? 'Yes' : 'No' }}</dd>
                    </div>
                    @if ($student->phone)
                        <div class="flex justify-between gap-4">
                            <dt class="text-ink-500">Phone</dt>
                            <dd class="text-ink-900">{{ $student->phone }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">Joined</dt>
                        <dd class="text-ink-900">{{ $student->created_at->format('j M Y') }}</dd>
                    </div>
                </dl>

                @if ($student->bio)
                    <p class="mt-5 border-t border-ink-200 pt-4 text-[0.8125rem]/6 text-ink-600">{{ $student->bio }}</p>
                @endif
            </x-ui.panel>

            <x-ui.panel title="Certificates" :padded="false">
                @if ($student->certificates->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-ink-500">None issued yet.</p>
                @else
                    <ul class="divide-y divide-ink-200">
                        @foreach ($student->certificates as $certificate)
                            <li class="px-5 py-3">
                                <p class="truncate text-sm text-ink-900">{{ $certificate->course_title }}</p>
                                <p class="mt-0.5 font-mono text-[0.75rem] text-ink-500">{{ $certificate->certificate_number }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.panel>
        </div>
    </div>
</x-layouts.admin>
