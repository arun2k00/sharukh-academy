<x-layouts.admin :title="'Edit '.$category->name" heading="Edit category">
    <x-admin.page-header :title="$category->name" :back="route('admin.categories.index')" back-label="All categories" />
    <div class="max-w-xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')
                @include('admin.categories._form')
                <div class="mt-6 flex items-center gap-3">
                    <x-ui.button type="submit">Save changes</x-ui.button>
                    <x-ui.button :href="route('admin.categories.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
