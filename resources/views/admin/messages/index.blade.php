<x-layouts.admin title="Messages" heading="Contact messages">
    @if ($messages->isEmpty())
        <x-ui.empty-state icon="mail" title="No messages"
                          description="Enquiries from the public contact form land here." />
    @else
        <x-admin.table :headings="['From', 'Subject', 'Received', ['label' => '', 'class' => 'text-right']]">
            @foreach ($messages as $message)
                <tr class="{{ $message->is_read ? '' : 'bg-brass-50/40' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.messages.show', $message) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $message->name }}</a>
                        <p class="text-[0.8125rem] text-ink-500">{{ $message->email }}</p>
                    </td>
                    <td class="max-w-sm px-4 py-3">
                        <p class="truncate text-ink-800">{{ $message->subject ?: 'No subject' }}</p>
                        <p class="truncate text-[0.8125rem] text-ink-500">{{ \Illuminate\Support\Str::limit($message->message, 80) }}</p>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-[0.8125rem] text-ink-500">
                        {{ $message->created_at->format('j M Y, g:ia') }}
                        @unless ($message->is_read)
                            <span class="mt-1 block text-brass-700">Unread</span>
                        @endunless
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.messages.show', $message) }}"
                               class="text-[0.8125rem] text-pine-700 hover:underline">Read</a>
                            <x-admin.delete-form :action="route('admin.messages.destroy', $message)"
                                                 confirm="Delete this message?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $messages->links() }}</div>
    @endif
</x-layouts.admin>
