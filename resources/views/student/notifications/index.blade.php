<x-layouts.app title="Notifications" heading="Notifications">
    @if ($notifications->isNotEmpty())
        <div class="mb-5 flex justify-end">
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="text-[0.8125rem] font-medium text-pine-700 hover:underline">Mark all as read</button>
            </form>
        </div>
    @endif

    @if ($notifications->isEmpty())
        <x-ui.empty-state icon="mail" title="You're all caught up" description="New updates about your courses and live classes will appear here." />
    @else
        <ul class="divide-y divide-ink-200 border border-ink-200 bg-white">
            @foreach ($notifications as $notification)
                <li class="flex items-start gap-4 p-5 {{ $notification->read_at ? '' : 'bg-pine-50/40' }}">
                    <span class="mt-1 size-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-transparent' : 'bg-pine-600' }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[0.9375rem] {{ $notification->read_at ? 'text-ink-800' : 'font-medium text-ink-900' }}">
                            {{ $notification->data['title'] ?? 'Update' }}
                        </p>
                        <p class="mt-1 text-[0.8125rem] text-ink-600">{{ $notification->data['body'] ?? '' }}</p>
                        <p class="mt-1.5 text-[0.75rem] text-ink-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        @if (! empty($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="text-[0.8125rem] text-pine-700 hover:underline">Open</a>
                        @endif
                        @unless ($notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button type="submit" class="text-[0.75rem] text-ink-500 hover:text-ink-900">Mark read</button>
                            </form>
                        @endunless
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-5">{{ $notifications->links() }}</div>
    @endif
</x-layouts.app>
