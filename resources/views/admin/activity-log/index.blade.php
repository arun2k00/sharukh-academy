<x-layouts.admin title="Activity log" heading="Activity log"
                 description="A record of significant admin actions, for accountability and debugging.">

    @if ($entries->isEmpty())
        <x-ui.empty-state icon="list" title="Nothing logged yet" description="Actions like creating a course or enrolling a student will appear here." />
    @else
        <x-admin.table :headings="['Action', 'By', 'When']">
            @foreach ($entries as $entry)
                <tr>
                    <td class="px-4 py-3 text-ink-900">{{ $entry->description }}</td>
                    <td class="px-4 py-3 text-ink-600">{{ $entry->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-[0.8125rem] text-ink-500">{{ $entry->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $entries->links() }}</div>
    @endif
</x-layouts.admin>
