<x-layouts.tutor title="New lesson" heading="New lesson">
    <x-admin.page-header title="Add a lesson" :back="route('tutor.courses.curriculum', $course)"
                         back-label="Back to curriculum" :description="$course->title" />

    <form method="POST" action="{{ route('tutor.lessons.store', $course) }}">
        @csrf
        @include('tutor.lessons._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Create lesson</x-ui.button>
            <x-ui.button :href="route('tutor.courses.curriculum', $course)" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.tutor>
