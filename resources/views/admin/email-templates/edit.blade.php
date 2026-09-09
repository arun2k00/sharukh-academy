<x-layouts.admin :title="'Edit — '.$name" heading="Edit email template">
    <x-admin.page-header :title="$name" :back="route('admin.email-templates.index')" back-label="All email templates" />

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-ui.panel>
                <form method="POST" action="{{ route('admin.email-templates.update', $key) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-ui.field label="Subject" for="subject" :error="$errors->first('subject')" required>
                        <x-ui.input id="subject" name="subject" value="{{ old('subject', $subject) }}" required :invalid="$errors->has('subject')" />
                    </x-ui.field>

                    <x-ui.field label="Message" for="body" :error="$errors->first('body')" required>
                        <x-ui.textarea id="body" name="body" rows="10" required :invalid="$errors->has('body')">{{ old('body', $body) }}</x-ui.textarea>
                    </x-ui.field>

                    <x-ui.button type="submit">Save template</x-ui.button>
                </form>
            </x-ui.panel>
        </div>

        <div class="lg:col-span-4">
            <x-ui.panel title="Available variables" description="Insert these exactly as shown; they are replaced automatically.">
                <ul class="space-y-2">
                    @foreach ($variables as $variable)
                        @php $token = sprintf('%s %s %s', str_repeat('{', 2), $variable, str_repeat('}', 2)); @endphp
                        <li><code class="bg-sand-100 px-1.5 py-0.5 text-[0.8125rem]">{{ $token }}</code></li>
                    @endforeach
                </ul>
                <p class="mt-4 text-[0.8125rem] text-ink-500">
                    Only these exact tokens are replaced — anything else you type is sent exactly as written.
                </p>
            </x-ui.panel>
        </div>
    </div>
</x-layouts.admin>
