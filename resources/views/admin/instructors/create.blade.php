<x-layouts.admin title="New instructor" heading="New instructor">
    <x-admin.page-header title="New instructor" :back="route('admin.instructors.index')" back-label="All instructors" />
    <form method="POST" action="{{ route('admin.instructors.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.instructors._form')
        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Create instructor</x-ui.button>
            <x-ui.button :href="route('admin.instructors.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
