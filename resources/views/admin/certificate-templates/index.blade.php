<x-layouts.admin title="Certificate templates" heading="Certificate templates">
    <x-slot:actions>
        <x-ui.button :href="route('admin.certificate-templates.create')" size="sm" icon="plus">Upload template</x-ui.button>
    </x-slot:actions>

    <x-ui.alert variant="info" class="mb-6">
        <p class="font-medium text-ink-900">Recommended certificate artwork</p>
        <p class="mt-1">A4 landscape, ratio 1.414:1, at least 2000px on the long edge (print quality). PNG or JPG.</p>
    </x-ui.alert>

    @if ($templates->isEmpty())
        <x-ui.empty-state icon="award" title="No certificate templates yet"
                          description="Upload artwork designed in Canva, Photoshop or similar. Courses without a template use the plain printable certificate.">
            <x-ui.button :href="route('admin.certificate-templates.create')">Upload template</x-ui.button>
        </x-ui.empty-state>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($templates as $template)
                <article class="border border-ink-200 bg-white">
                    <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="block aspect-[1.414/1] overflow-hidden bg-ink-100">
                        <img src="{{ $template->image_url }}" alt="" class="size-full object-cover">
                    </a>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-[0.9375rem] font-medium text-ink-900">{{ $template->name }}</h2>
                            @if ($template->is_active)
                                <x-ui.badge class="bg-pine-50 text-pine-800">Active</x-ui.badge>
                            @else
                                <x-ui.badge class="bg-sand-200 text-ink-600">Inactive</x-ui.badge>
                            @endif
                        </div>
                        <p class="mt-1 text-[0.8125rem] text-ink-500">
                            {{ $template->image_width }}×{{ $template->image_height }}px ·
                            {{ $template->courses_count }} {{ str('course')->plural($template->courses_count) }}
                            @if ($template->show_qr_code) · QR code @endif
                        </p>
                        <div class="mt-3 flex items-center gap-3">
                            <a href="{{ route('admin.certificate-templates.edit', $template) }}"
                               class="text-[0.8125rem] text-pine-700 hover:underline">Edit positions</a>
                            <x-admin.delete-form :action="route('admin.certificate-templates.destroy', $template)"
                                                 confirm="Delete this template?" />
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
