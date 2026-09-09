<x-layouts.app title="My attendance" heading="My attendance"
               description="Live classes you have attended, across every course you are enrolled in.">

    @if ($classes->isEmpty())
        <x-ui.empty-state icon="broadcast" title="No past live classes yet"
                          description="Attendance for a live class appears here once it has taken place." />
    @else
        <x-admin.table :headings="['Live class', 'Course', 'Date', 'Attendance']">
            @foreach ($classes as $liveClass)
                @php $status = $liveClass->attendances->first()?->status; @endphp
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('live-classes.show', $liveClass) }}" class="text-ink-900 hover:underline">{{ $liveClass->title }}</a>
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $liveClass->course->title }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-ink-600">{{ $liveClass->scheduled_at->format('j M Y') }}</td>
                    <td class="px-4 py-3">
                        @if ($status)
                            <x-ui.badge :class="$status->badgeClasses()">{{ $status->label() }}</x-ui.badge>
                        @else
                            <span class="text-[0.8125rem] text-ink-400">Not recorded</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif
</x-layouts.app>
