<x-layouts.tutor :title="$course->title" heading="Edit course">
    <x-admin.page-header :title="$course->title" :back="route('tutor.courses.index')" back-label="My courses">
        <x-slot:actions>
            <x-ui.button :href="route('tutor.courses.curriculum', $course)" variant="outline" size="sm">
                Curriculum
            </x-ui.button>
            <x-ui.button :href="route('tutor.courses.invite-codes.index', $course)" variant="outline" size="sm">
                Invite codes
            </x-ui.button>
            <x-ui.button :href="route('courses.show', $course)" variant="outline" size="sm"
                         target="_blank" rel="noopener" icon-after="external-link">
                {{ $course->isPublished() ? 'View live' : 'Preview draft' }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('tutor.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('tutor.courses._form')

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-ui.button type="submit">Save changes</x-ui.button>
            <x-ui.button :href="route('tutor.courses.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>

    <div class="mt-10 border-t border-ink-200 pt-6">
        <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Danger zone</h2>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-4 border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-ink-700">
                Deleting removes the course from the catalogue. Enrollments, progress and certificates are kept —
                contact an administrator if you need it restored.
            </p>
            <form method="POST" action="{{ route('tutor.courses.destroy', $course) }}"
                  onsubmit="return confirm('Delete {{ $course->title }}?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger" size="sm">Delete course</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.tutor>
