<x-layouts.admin title="New testimonial" heading="New testimonial">
    <x-admin.page-header title="New testimonial" :back="route('admin.testimonials.index')" back-label="All testimonials" />
    <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.testimonials._form')
        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Create testimonial</x-ui.button>
            <x-ui.button :href="route('admin.testimonials.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
