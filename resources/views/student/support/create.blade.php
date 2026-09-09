<x-layouts.app title="New support request" heading="New support request">
    <div class="max-w-xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('support.store') }}" class="space-y-5">
                @csrf

                <x-ui.field label="Subject" for="subject" :error="$errors->first('subject')" required>
                    <x-ui.input id="subject" name="subject" value="{{ old('subject') }}" required
                                :invalid="$errors->has('subject')" />
                </x-ui.field>

                <x-ui.field label="Category" for="category" :error="$errors->first('category')" required>
                    <x-ui.select id="category" name="category" :invalid="$errors->has('category')">
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Describe the issue" for="message" :error="$errors->first('message')" required>
                    <x-ui.textarea id="message" name="message" rows="6" required
                                   :invalid="$errors->has('message')">{{ old('message') }}</x-ui.textarea>
                </x-ui.field>

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit">Submit request</x-ui.button>
                    <x-ui.button :href="route('support.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.app>
