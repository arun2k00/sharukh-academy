<x-layouts.admin :title="'Edit testimonial'" heading="Edit testimonial">
    <x-admin.page-header :title="$testimonial->name" :back="route('admin.testimonials.index')" back-label="All testimonials" />
    <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.testimonials._form')
        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Save changes</x-ui.button>
            <x-ui.button :href="route('admin.testimonials.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
