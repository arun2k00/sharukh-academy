<x-layouts.admin title="Message" heading="Message">
    <x-admin.page-header :title="$message->subject ?: 'No subject'" :back="route('admin.messages.index')"
                         back-label="All messages">
        <x-slot:actions>
            <x-ui.button :href="'mailto:'.$message->email.'?subject='.rawurlencode('Re: '.($message->subject ?: 'Your enquiry'))"
                         size="sm" icon="mail">Reply by email</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-ui.panel>
                <div class="prose-academy">
                    @foreach (preg_split('/\r\n|\n/', $message->message) as $line)
                        @if (trim($line) !== '')
                            <p>{{ $line }}</p>
                        @endif
                    @endforeach
                </div>
            </x-ui.panel>
        </div>

        <div class="lg:col-span-4">
            <x-ui.panel title="Sender">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-ink-500">Name</dt>
                        <dd class="mt-0.5 text-ink-900">{{ $message->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Email</dt>
                        <dd class="mt-0.5">
                            <a href="mailto:{{ $message->email }}" class="text-pine-700 hover:underline">{{ $message->email }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Received</dt>
                        <dd class="mt-0.5 text-ink-900">{{ $message->created_at->format('j F Y, g:ia') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-ink-200 pt-4">
                    <x-admin.delete-form :action="route('admin.messages.destroy', $message)"
                                         confirm="Delete this message permanently?" />
                </div>
            </x-ui.panel>
        </div>
    </div>
</x-layouts.admin>
