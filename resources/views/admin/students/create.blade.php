<x-layouts.admin title="Add account" heading="Add account">
    <x-admin.page-header title="Create an account" :back="route('admin.students.index')" back-label="All students"
                         description="Useful for enrolling someone who paid offline, or adding a second administrator." />

    <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.students._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Create account</x-ui.button>
            <x-ui.button :href="route('admin.students.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
