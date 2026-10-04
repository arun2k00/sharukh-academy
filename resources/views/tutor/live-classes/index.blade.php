<x-layouts.tutor title="Live classes" heading="Live classes">
    <x-slot:actions>
        <x-ui.button :href="route('tutor.live-classes.create')" size="sm" icon="plus">Schedule class</x-ui.button>
    </x-slot:actions>

    <nav class="mb-5 flex gap-6 border-b border-ink-200" aria-label="Filter">
        @foreach (['upcoming' => 'Upcoming', 'past' => 'Past'] as $value => $label)
            <a href="{{ route('tutor.live-classes.index', ['scope' => $value]) }}"
               class="-mb-px border-b-2 py-2.5 text-sm {{ $scope === $value ? 'border-ink-900 text-ink-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if ($liveClasses->isEmpty())
        <x-ui.empty-state icon="broadcast" title="No {{ $scope }} classes"
                          description="Live sessions are attached to one of your courses; only enrolled students can join.">
            <x-ui.button :href="route('tutor.live-classes.create')">Schedule a class</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Class', 'Course', 'When', 'Status', 'Files', ['label' => '', 'class' => 'text-right']]">
            @foreach ($liveClasses as $liveClass)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('tutor.live-classes.edit', $liveClass) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $liveClass->title }}</a>
                        @if (blank($liveClass->zoom_url))
                            <p class="mt-0.5 text-[0.75rem] text-brass-700">No Zoom link yet</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $liveClass->course->title }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-ink-600">
                        {{ $liveClass->scheduled_at->format('j M Y, g:ia') }}
                        <span class="block text-[0.75rem] text-ink-400">{{ $liveClass->duration_minutes }} min</span>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :class="$liveClass->status->badgeClasses()">{{ $liveClass->status->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $liveClass->resources_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                            <a href="{{ route('tutor.live-classes.edit', $liveClass) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('tutor.live-classes.destroy', $liveClass)"
                                                 confirm="Remove this live class?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $liveClasses->links() }}</div>
    @endif
</x-layouts.tutor>
