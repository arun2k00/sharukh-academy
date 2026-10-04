<x-layouts.tutor title="New course" heading="New course">
    <x-admin.page-header title="Create a course" :back="route('tutor.courses.index')" back-label="My courses"
                         description="Save the basics first — you will build the curriculum on the next screen." />

    <form method="POST" action="{{ route('tutor.courses.store') }}" enctype="multipart/form-data">
        @csrf
        @include('tutor.courses._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Create course</x-ui.button>
            <x-ui.button :href="route('tutor.courses.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.tutor>
