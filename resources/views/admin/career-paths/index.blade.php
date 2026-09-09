<x-layouts.admin title="Career Plus" heading="Career Plus">
    <x-slot:actions>
        <x-ui.button :href="route('admin.career-paths.create')" size="sm" icon="plus">New Career Plus path</x-ui.button>
    </x-slot:actions>

    @if ($careerPaths->isEmpty())
        <x-ui.empty-state icon="graduation-cap" title="No Career Plus paths yet"
                          description="Group existing courses into a staged roadmap, e.g. a job-ready programme that sequences several skill courses.">
            <x-ui.button :href="route('admin.career-paths.create')">New Career Plus path</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Title', 'Courses', 'Status', ['label' => '', 'class' => 'text-right']]">
            @foreach ($careerPaths as $careerPath)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.career-paths.edit', $careerPath) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $careerPath->title }}</a>
                        @if ($careerPath->subtitle)
                            <p class="text-[0.8125rem] text-ink-500">{{ $careerPath->subtitle }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $careerPath->courses_count }}</td>
                    <td class="px-4 py-3">
                        @if ($careerPath->is_published)
                            <x-ui.badge class="bg-pine-50 text-pine-800">Published</x-ui.badge>
                        @else
                            <x-ui.badge class="bg-ink-100 text-ink-500">Draft</x-ui.badge>
                        @endif
                        @if ($careerPath->is_featured)
                            <x-ui.badge class="bg-brass-50 text-brass-700">Featured</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.career-paths.edit', $careerPath) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('admin.career-paths.destroy', $careerPath)"
                                                 confirm="Delete this Career Plus path? The courses in it are not affected." />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif
</x-layouts.admin>
