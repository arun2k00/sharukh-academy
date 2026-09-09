<x-layouts.admin title="New question" heading="New question">
    <x-admin.page-header title="New question" :back="route('admin.faqs.index')" back-label="All questions" />
    <div class="max-w-2xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.faqs.store') }}">
                @csrf
                @include('admin.faqs._form')
                <div class="mt-6 flex items-center gap-3">
                    <x-ui.button type="submit">Add question</x-ui.button>
                    <x-ui.button :href="route('admin.faqs.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
