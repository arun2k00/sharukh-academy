<x-layouts.app :title="$ticket->subject">
    <a href="{{ route('support.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">
        <x-ui.icon name="arrow-left" class="size-3.5" />
        All requests
    </a>

    <div class="flex flex-wrap items-start justify-between gap-4">
        <h1 class="text-[1.5rem] leading-tight">{{ $ticket->subject }}</h1>
        <x-ui.badge :class="$ticket->status->badgeClasses()">{{ $ticket->status->label() }}</x-ui.badge>
    </div>
    <p class="mt-1.5 text-sm text-ink-500">Submitted {{ $ticket->created_at->format('j F Y, g:ia') }}</p>

    <div class="mt-7 space-y-4">
        <div class="border border-ink-200 bg-white p-5">
            <p class="text-[0.8125rem] font-medium text-ink-900">You</p>
            <p class="mt-2 whitespace-pre-line text-[0.9375rem]/7 text-ink-700">{{ $ticket->message }}</p>
        </div>

        @foreach ($ticket->replies as $reply)
            <div class="border p-5 {{ $reply->user_id === $ticket->user_id ? 'border-ink-200 bg-white' : 'border-pine-200 bg-pine-50' }}">
                <p class="text-[0.8125rem] font-medium text-ink-900">
                    {{ $reply->user_id === $ticket->user_id ? 'You' : ($reply->user->name.' · '.config('academy.name')) }}
                </p>
                <p class="mt-2 whitespace-pre-line text-[0.9375rem]/7 text-ink-700">{{ $reply->message }}</p>
                <p class="mt-2 text-[0.75rem] text-ink-400">{{ $reply->created_at->format('j M Y, g:ia') }}</p>
            </div>
        @endforeach
    </div>

    @if ($ticket->status !== \App\Enums\SupportTicketStatus::Resolved)
        <form method="POST" action="{{ route('support.replies.store', $ticket) }}" class="mt-7 space-y-4">
            @csrf
            <x-ui.field label="Reply" for="message">
                <x-ui.textarea id="message" name="message" rows="4" required></x-ui.textarea>
            </x-ui.field>
            <x-ui.button type="submit">Send reply</x-ui.button>
        </form>
    @else
        <x-ui.alert variant="info" class="mt-7">
            This request is resolved. Replying will reopen it.
            <form method="POST" action="{{ route('support.replies.store', $ticket) }}" class="mt-3 space-y-3">
                @csrf
                <x-ui.textarea name="message" rows="3" required placeholder="Still need help with this?"></x-ui.textarea>
                <x-ui.button type="submit" size="sm">Reopen with a reply</x-ui.button>
            </form>
        </x-ui.alert>
    @endif
</x-layouts.app>
