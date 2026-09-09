<x-layouts.admin :title="'Edit '.$student->name" heading="Edit account">
    <x-admin.page-header :title="$student->name" :back="route('admin.students.show', $student)" back-label="Back to profile" />

    <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.students._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Save changes</x-ui.button>
            <x-ui.button :href="route('admin.students.show', $student)" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>

    @can('delete', $student)
        <div class="mt-10 border-t border-ink-200 pt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 border border-red-200 bg-red-50 p-4">
                <p class="text-sm text-ink-700">
                    Removing an account revokes access immediately. Enrolments, progress and certificates are retained.
                </p>
                <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                      onsubmit="return confirm('Remove {{ $student->name }}?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" size="sm">Remove account</x-ui.button>
                </form>
            </div>
        </div>
    @endcan
</x-layouts.admin>
