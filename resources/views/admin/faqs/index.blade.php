<x-layouts.admin title="FAQs" heading="Frequently asked questions">
    <x-slot:actions>
        <x-ui.button :href="route('admin.faqs.create')" size="sm" icon="plus">New question</x-ui.button>
    </x-slot:actions>

    @if ($faqs->isEmpty())
        <x-ui.empty-state icon="message" title="No questions yet"
                          description="General questions appear on the homepage and every course page.">
            <x-ui.button :href="route('admin.faqs.create')">New question</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Question', 'Scope', 'Published', ['label' => '', 'class' => 'text-right']]">
            @foreach ($faqs as $faq)
                <tr>
                    <td class="max-w-lg px-4 py-3">
                        <a href="{{ route('admin.faqs.edit', $faq) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $faq->question }}</a>
                        <p class="mt-0.5 text-[0.8125rem] text-ink-500">{{ \Illuminate\Support\Str::limit($faq->answer, 110) }}</p>
                    </td>
                    <td class="px-4 py-3 text-[0.8125rem] text-ink-500">{{ $faq->course?->title ?? 'General' }}</td>
                    <td class="px-4 py-3">
                        @if ($faq->is_published)
                            <x-ui.badge class="bg-pine-50 text-pine-800">Live</x-ui.badge>
                        @else
                            <x-ui.badge class="bg-sand-200 text-ink-600">Hidden</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.faqs.edit', $faq) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('admin.faqs.destroy', $faq)" confirm="Delete this question?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif
</x-layouts.admin>
