<x-layouts.admin title="New course" heading="New course">
    <x-admin.page-header title="Create a course" :back="route('admin.courses.index')" back-label="All courses"
                         description="Save the basics first — you will build the curriculum on the next screen." />

    <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.courses._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Create course</x-ui.button>
            <x-ui.button :href="route('admin.courses.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
