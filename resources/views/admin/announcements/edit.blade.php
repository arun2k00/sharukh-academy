<x-layouts.admin title="Edit announcement" heading="Edit announcement">
    <x-admin.page-header :title="$announcement->title" :back="route('admin.announcements.index')" back-label="All announcements" />

    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
        @csrf
        @method('PUT')
        @include('admin.announcements._form')
        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Save changes</x-ui.button>
            <x-ui.button :href="route('admin.announcements.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
