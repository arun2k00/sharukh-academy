<x-layouts.admin title="New career path" heading="New career path">
    <x-admin.page-header title="New career path" :back="route('admin.career-paths.index')" back-label="All career paths" />
    <div class="max-w-3xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.career-paths.store') }}">
                @csrf
                @include('admin.career-paths._form')
                <div class="mt-6 flex items-center gap-3">
                    <x-ui.button type="submit">Create career path</x-ui.button>
                    <x-ui.button :href="route('admin.career-paths.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
