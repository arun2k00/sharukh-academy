<x-layouts.tutor title="Schedule live class" heading="Schedule live class">
    <x-admin.page-header title="Schedule a live class" :back="route('tutor.live-classes.index')"
                         back-label="All live classes" />

    <form method="POST" action="{{ route('tutor.live-classes.store') }}" enctype="multipart/form-data">
        @csrf
        @include('tutor.live-classes._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Schedule class</x-ui.button>
            <x-ui.button :href="route('tutor.live-classes.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.tutor>
