<x-layouts.auth title="Confirm password" heading="Confirm your password"
                subheading="This is a secure area. Please confirm your password before continuing.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <x-ui.field label="Password" for="password" :error="$errors->first('password')">
            <x-ui.input id="password" name="password" type="password"
                        autocomplete="current-password" required autofocus :invalid="$errors->has('password')" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">Confirm</x-ui.button>
    </form>
</x-layouts.auth>
