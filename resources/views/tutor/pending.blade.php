<x-layouts.tutor title="Almost there" heading="Almost there">
    <x-ui.empty-state icon="user" title="Your trainer profile isn't linked yet"
        description="An administrator needs to link your account to an instructor profile before you can create or manage courses here. Reach out to the academy team and they'll get you set up.">
        <x-ui.button :href="route('contact')" variant="outline">Contact the academy</x-ui.button>
    </x-ui.empty-state>
</x-layouts.tutor>
