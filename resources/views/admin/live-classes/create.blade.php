<x-layouts.admin title="Schedule live class" heading="Schedule live class">
    <x-admin.page-header title="Schedule a live class" :back="route('admin.live-classes.index')"
                         back-label="All live classes" />

    <form method="POST" action="{{ route('admin.live-classes.store') }}">
        @csrf
        @include('admin.live-classes._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Schedule class</x-ui.button>
            <x-ui.button :href="route('admin.live-classes.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
