<x-layouts.app title="Support" heading="Support">
    <x-slot:actions>
        <x-ui.button :href="route('support.create')" size="sm" icon="plus">New request</x-ui.button>
    </x-slot:actions>

    @if ($tickets->isEmpty())
        <x-ui.empty-state icon="message" title="No support requests yet"
                          description="Have a question about a course or your account? We usually reply within a working day.">
            <x-ui.button :href="route('support.create')">Contact support</x-ui.button>
        </x-ui.empty-state>
    @else
        <ul class="divide-y divide-ink-200 border border-ink-200 bg-white">
            @foreach ($tickets as $ticket)
                <li class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <a href="{{ route('support.show', $ticket) }}" class="text-[0.9375rem] font-medium text-ink-900 hover:underline">
                            {{ $ticket->subject }}
                        </a>
                        <p class="mt-0.5 text-[0.8125rem] text-ink-500">
                            {{ $categories[$ticket->category] ?? $ticket->category }}
                            · {{ $ticket->created_at->format('j M Y') }}
                        </p>
                    </div>
                    <x-ui.badge :class="$ticket->status->badgeClasses()">{{ $ticket->status->label() }}</x-ui.badge>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
