<x-layouts.admin title="Categories" heading="Categories">
    <x-slot:actions>
        <x-ui.button :href="route('admin.categories.create')" size="sm" icon="plus">New category</x-ui.button>
    </x-slot:actions>

    @if ($categories->isEmpty())
        <x-ui.empty-state icon="tag" title="No categories yet"
                          description="Categories group the catalogue and power the course filter.">
            <x-ui.button :href="route('admin.categories.create')">New category</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Name', 'Slug', 'Courses', ['label' => '', 'class' => 'text-right']]">
            @foreach ($categories as $category)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $category->name }}</a>
                        @if ($category->description)
                            <p class="text-[0.8125rem] text-ink-500">{{ $category->description }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-[0.8125rem] text-ink-500">{{ $category->slug }}</td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $category->courses_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('admin.categories.destroy', $category)"
                                                 confirm="Delete this category? Its courses become uncategorised." />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif
</x-layouts.admin>
