<x-layouts.admin title="Announcements" heading="Announcements">
    <x-slot:actions>
        <x-ui.button :href="route('admin.announcements.create')" size="sm" icon="plus">New announcement</x-ui.button>
    </x-slot:actions>

    @if ($announcements->isEmpty())
        <x-ui.empty-state icon="message" title="No announcements yet"
                          description="Post an update for all students or for those enrolled in one course. It also appears in their notification centre.">
            <x-ui.button :href="route('admin.announcements.create')">New announcement</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Title', 'Audience', 'Posted', ['label' => '', 'class' => 'text-right']]">
            @foreach ($announcements as $announcement)
                <tr>
                    <td class="max-w-md px-4 py-3">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="font-medium text-ink-900 hover:underline">
                            {{ $announcement->title }}
                        </a>
                        <p class="mt-0.5 truncate text-[0.8125rem] text-ink-500">{{ \Illuminate\Support\Str::limit($announcement->body, 100) }}</p>
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $announcement->course?->title ?? 'All students' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-ink-600">{{ $announcement->created_at->format('j M Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('admin.announcements.destroy', $announcement)" confirm="Delete this announcement?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $announcements->links() }}</div>
    @endif
</x-layouts.admin>
