<x-layouts.admin :title="$ticket->subject" heading="Support ticket">
    <x-admin.page-header :title="$ticket->subject" :back="route('admin.support-tickets.index')" back-label="All tickets"
                         :description="$ticket->user->name.' · '.$ticket->user->email">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.support-tickets.status', $ticket) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <x-ui.select name="status" onchange="this.form.submit()" class="!py-1.5">
                    @foreach (\App\Enums\SupportTicketStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected($ticket->status->value === $value)>{{ $label }}</option>
                    @endforeach
                </x-ui.select>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="space-y-4">
        <div class="border border-ink-200 bg-white p-5">
            <p class="text-[0.8125rem] font-medium text-ink-900">{{ $ticket->user->name }}</p>
            <p class="mt-2 whitespace-pre-line text-[0.9375rem]/7 text-ink-700">{{ $ticket->message }}</p>
            <p class="mt-2 text-[0.75rem] text-ink-400">{{ $ticket->created_at->format('j M Y, g:ia') }}</p>
        </div>

        @foreach ($ticket->replies as $reply)
            <div class="border p-5 {{ $reply->user_id === $ticket->user_id ? 'border-ink-200 bg-white' : 'border-pine-200 bg-pine-50' }}">
                <p class="text-[0.8125rem] font-medium text-ink-900">
                    {{ $reply->user_id === $ticket->user_id ? $ticket->user->name : $reply->user->name.' · '.config('academy.name') }}
                </p>
                <p class="mt-2 whitespace-pre-line text-[0.9375rem]/7 text-ink-700">{{ $reply->message }}</p>
                <p class="mt-2 text-[0.75rem] text-ink-400">{{ $reply->created_at->format('j M Y, g:ia') }}</p>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.support-tickets.replies.store', $ticket) }}" class="mt-7 space-y-4">
        @csrf
        <x-ui.field label="Reply" for="message">
            <x-ui.textarea id="message" name="message" rows="4" required></x-ui.textarea>
        </x-ui.field>
        <x-ui.button type="submit">Send reply</x-ui.button>
    </form>
</x-layouts.admin>
