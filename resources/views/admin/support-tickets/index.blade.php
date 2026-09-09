<x-layouts.admin title="Support tickets" heading="Support tickets">
    <nav class="mb-5 flex gap-6 border-b border-ink-200" aria-label="Filter">
        <a href="{{ route('admin.support-tickets.index') }}"
           class="-mb-px border-b-2 py-2.5 text-sm {{ ! $activeStatus ? 'border-ink-900 text-ink-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
            All
        </a>
        @foreach (\App\Enums\SupportTicketStatus::cases() as $status)
            <a href="{{ route('admin.support-tickets.index', ['status' => $status->value]) }}"
               class="-mb-px border-b-2 py-2.5 text-sm {{ $activeStatus === $status ? 'border-ink-900 text-ink-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
                {{ $status->label() }}
            </a>
        @endforeach
    </nav>

    @if ($tickets->isEmpty())
        <x-ui.empty-state icon="message" title="No support requests" description="Student support requests appear here." />
    @else
        <x-admin.table :headings="['Subject', 'Student', 'Category', 'Status', 'Submitted']">
            @foreach ($tickets as $ticket)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="font-medium text-ink-900 hover:underline">
                            {{ $ticket->subject }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $ticket->user->name }}</td>
                    <td class="px-4 py-3 text-[0.8125rem] text-ink-500">{{ \App\Http\Controllers\Student\SupportTicketController::CATEGORIES[$ticket->category] ?? $ticket->category }}</td>
                    <td class="px-4 py-3"><x-ui.badge :class="$ticket->status->badgeClasses()">{{ $ticket->status->label() }}</x-ui.badge></td>
                    <td class="px-4 py-3 whitespace-nowrap text-ink-600">{{ $ticket->created_at->format('j M Y') }}</td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $tickets->links() }}</div>
    @endif
</x-layouts.admin>
