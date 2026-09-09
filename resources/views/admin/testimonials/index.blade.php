<x-layouts.admin title="Testimonials" heading="Testimonials">
    <x-slot:actions>
        <x-ui.button :href="route('admin.testimonials.create')" size="sm" icon="plus">New testimonial</x-ui.button>
    </x-slot:actions>

    @if ($testimonials->isEmpty())
        <x-ui.empty-state icon="star" title="No testimonials yet"
                          description="Featured testimonials appear on the homepage; course testimonials appear on that course page.">
            <x-ui.button :href="route('admin.testimonials.create')">New testimonial</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Person', 'Quote', 'Course', 'Rating', 'Visibility', ['label' => '', 'class' => 'text-right']]">
            @foreach ($testimonials as $testimonial)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium text-ink-900">{{ $testimonial->name }}</p>
                        <p class="text-[0.8125rem] text-ink-500">{{ $testimonial->role }}</p>
                    </td>
                    <td class="max-w-sm px-4 py-3 text-[0.8125rem] text-ink-600">
                        {{ \Illuminate\Support\Str::limit($testimonial->quote, 90) }}
                    </td>
                    <td class="px-4 py-3 text-[0.8125rem] text-ink-500">{{ $testimonial->course?->title ?? '—' }}</td>
                    <td class="px-4 py-3"><x-ui.rating :value="$testimonial->rating" /></td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1.5">
                            @if ($testimonial->is_featured)
                                <x-ui.badge class="bg-pine-50 text-pine-800">Homepage</x-ui.badge>
                            @endif
                            @unless ($testimonial->is_published)
                                <x-ui.badge class="bg-sand-200 text-ink-600">Hidden</x-ui.badge>
                            @endunless
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('admin.testimonials.destroy', $testimonial)"
                                                 confirm="Delete this testimonial?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif
</x-layouts.admin>
