<x-layouts.admin :title="'Edit '.$instructor->name" heading="Edit instructor">
    <x-admin.page-header :title="$instructor->name" :back="route('admin.instructors.index')" back-label="All instructors" />
    <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.instructors._form')
        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Save changes</x-ui.button>
            <x-ui.button :href="route('admin.instructors.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
