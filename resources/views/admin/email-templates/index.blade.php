<x-layouts.admin title="Email templates" heading="Email templates"
                 description="Edit the wording of automated emails. Password reset uses Laravel's own secure email and is not editable here.">

    <x-admin.table :headings="['Email', 'Status', ['label' => '', 'class' => 'text-right']]">
        @foreach ($templates as $template)
            <tr>
                <td class="px-4 py-3 font-medium text-ink-900">{{ $template['name'] }}</td>
                <td class="px-4 py-3">
                    @if ($template['customised'])
                        <x-ui.badge class="bg-pine-50 text-pine-800">Customised</x-ui.badge>
                    @else
                        <x-ui.badge class="bg-sand-200 text-ink-600">Default</x-ui.badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-end">
                        <a href="{{ route('admin.email-templates.edit', $template['key']) }}" class="text-[0.8125rem] text-pine-700 hover:underline">Edit</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>
</x-layouts.admin>
