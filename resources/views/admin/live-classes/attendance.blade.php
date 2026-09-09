<x-layouts.admin :title="'Attendance — '.$liveClass->title" heading="Attendance">
    <x-admin.page-header :title="$liveClass->title" :back="route('admin.live-classes.edit', $liveClass)" back-label="Back to live class"
                         :description="$liveClass->course->title.' · '.$liveClass->scheduled_at->format('j F Y, g:ia')" />

    @if ($students->isEmpty())
        <x-ui.empty-state icon="users" title="No enrolled students" description="Attendance can be recorded once students are enrolled in this course." />
    @else
        <form method="POST" action="{{ route('admin.live-classes.attendance.update', $liveClass) }}">
            @csrf
            @method('PUT')

            <x-admin.table :headings="['Student', 'Attendance']">
                @foreach ($students as $student)
                    @php $status = $existing[$student->id] ?? 'absent'; @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <x-ui.avatar :src="$student->avatar_url" :name="$student->name" size="xs" />
                                <div class="min-w-0">
                                    <p class="truncate text-ink-900">{{ $student->name }}</p>
                                    <p class="truncate text-[0.8125rem] text-ink-500">{{ $student->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-1.5 text-sm text-ink-700">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="present"
                                           @checked($status === 'present')
                                           class="text-pine-700 focus:ring-pine-600">
                                    Present
                                </label>
                                <label class="flex items-center gap-1.5 text-sm text-ink-700">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="absent"
                                           @checked($status === 'absent')
                                           class="text-pine-700 focus:ring-pine-600">
                                    Absent
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>

            <x-ui.button type="submit" class="mt-6">Save attendance</x-ui.button>
        </form>
    @endif
</x-layouts.admin>
