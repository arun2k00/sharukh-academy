<x-layouts.admin :title="'Edit '.$careerPath->title" heading="Edit Career Plus path">
    <x-slot:actions>
        <x-ui.button :href="route('career-paths.show', $careerPath)" variant="ghost" size="sm" icon="external-link" target="_blank">
            View page
        </x-ui.button>
    </x-slot:actions>
    <x-admin.page-header :title="$careerPath->title" :back="route('admin.career-paths.index')" back-label="All Career Plus paths" />
    <div class="max-w-3xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.career-paths.update', $careerPath) }}">
                @csrf
                @method('PUT')
                @include('admin.career-paths._form')
                <div class="mt-6 flex items-center gap-3">
                    <x-ui.button type="submit">Save changes</x-ui.button>
                    <x-ui.button :href="route('admin.career-paths.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
