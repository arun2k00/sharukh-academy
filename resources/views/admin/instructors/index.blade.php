<x-layouts.admin title="Instructors" heading="Instructors">
    <x-slot:actions>
        <x-ui.button :href="route('admin.instructors.create')" size="sm" icon="plus">New instructor</x-ui.button>
    </x-slot:actions>

    @if ($instructors->isEmpty())
        <x-ui.empty-state icon="user" title="No instructors yet"
                          description="Courses show their instructor's name, photo and biography.">
            <x-ui.button :href="route('admin.instructors.create')">New instructor</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Instructor', 'Courses', 'Featured', ['label' => '', 'class' => 'text-right']]">
            @foreach ($instructors as $instructor)
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :src="$instructor->avatar_url" :name="$instructor->name" size="xs" />
                            <div class="min-w-0">
                                <a href="{{ route('admin.instructors.edit', $instructor) }}"
                                   class="block truncate font-medium text-ink-900 hover:underline">{{ $instructor->name }}</a>
                                @if ($instructor->headline)
                                    <p class="truncate text-[0.8125rem] text-ink-500">{{ $instructor->headline }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $instructor->courses_count }}</td>
                    <td class="px-4 py-3">
                        @if ($instructor->is_featured)
                            <x-ui.badge class="bg-pine-50 text-pine-800">Featured</x-ui.badge>
                        @else
                            <span class="text-[0.8125rem] text-ink-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.instructors.edit', $instructor) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('admin.instructors.destroy', $instructor)"
                                                 confirm="Remove this instructor? Their courses stay but lose the attribution." />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif
</x-layouts.admin>
