<x-layouts.admin title="New announcement" heading="New announcement">
    <x-admin.page-header title="Post an announcement" :back="route('admin.announcements.index')" back-label="All announcements"
                         description="Posting sends a notification immediately to the chosen audience." />

    <form method="POST" action="{{ route('admin.announcements.store') }}">
        @csrf
        @include('admin.announcements._form')
        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Post announcement</x-ui.button>
            <x-ui.button :href="route('admin.announcements.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
