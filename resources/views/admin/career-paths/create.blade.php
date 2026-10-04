<x-layouts.admin title="New Career Plus path" heading="New Career Plus path">
    <x-admin.page-header title="New Career Plus path" :back="route('admin.career-paths.index')" back-label="All Career Plus paths" />
    <div class="max-w-3xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.career-paths.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.career-paths._form')
                <div class="mt-6 flex items-center gap-3">
                    <x-ui.button type="submit">Create Career Plus path</x-ui.button>
                    <x-ui.button :href="route('admin.career-paths.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
