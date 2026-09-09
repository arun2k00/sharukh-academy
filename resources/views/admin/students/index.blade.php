<x-layouts.admin title="Students" heading="Students">
    <x-slot:actions>
        <x-ui.button :href="route('admin.students.create')" size="sm" icon="plus">Add account</x-ui.button>
    </x-slot:actions>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-72">
            <x-ui.field label="Search" for="q">
                <x-ui.input id="q" name="q" value="{{ request('q') }}" placeholder="Name or email" />
            </x-ui.field>
        </div>
        <div class="w-full sm:w-44">
            <x-ui.field label="Role" for="role">
                <x-ui.select id="role" name="role">
                    <option value="">Everyone</option>
                    <option value="student" @selected(request('role') === 'student')>Students</option>
                    <option value="admin" @selected(request('role') === 'admin')>Administrators</option>
                </x-ui.select>
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
        @if (request()->hasAny(['q', 'role']))
            <a href="{{ route('admin.students.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($students->isEmpty())
        <x-ui.empty-state icon="users" title="No accounts match" description="Try a different search." />
    @else
        <x-admin.table :headings="['Name', 'Role', 'Courses', 'Certificates', 'Joined', ['label' => '', 'class' => 'text-right']]">
            @foreach ($students as $student)
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :src="$student->avatar_url" :name="$student->name" size="xs" />
                            <div class="min-w-0">
                                <a href="{{ route('admin.students.show', $student) }}"
                                   class="block truncate font-medium text-ink-900 hover:underline">{{ $student->name }}</a>
                                <p class="truncate text-[0.8125rem] text-ink-500">{{ $student->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if ($student->isAdmin())
                            <x-ui.badge class="bg-ink-900 text-white">Admin</x-ui.badge>
                        @else
                            <span class="text-ink-600">Student</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $student->enrollments_count }}</td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $student->certificates_count }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-ink-600">{{ $student->created_at->format('j M Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                            <a href="{{ route('admin.students.show', $student) }}"
                               class="text-[0.8125rem] text-pine-700 hover:underline">View</a>
                            <a href="{{ route('admin.students.edit', $student) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $students->links() }}</div>
    @endif
</x-layouts.admin>
