<x-layouts.admin title="Edit question" heading="Edit question">
    <x-admin.page-header :title="$faq->question" :back="route('admin.faqs.index')" back-label="All questions" />
    <div class="max-w-2xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
                @csrf
                @method('PUT')
                @include('admin.faqs._form')
                <div class="mt-6 flex items-center gap-3">
                    <x-ui.button type="submit">Save changes</x-ui.button>
                    <x-ui.button :href="route('admin.faqs.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
