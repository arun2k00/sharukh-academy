<x-layouts.admin title="Certificates" heading="Certificates">
    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-72">
            <x-ui.field label="Search" for="q">
                <x-ui.input id="q" name="q" value="{{ request('q') }}" placeholder="Certificate number or name" />
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Search</x-ui.button>
        @if (request('q'))
            <a href="{{ route('admin.certificates.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($certificates->isEmpty())
        <x-ui.empty-state icon="award" title="No certificates issued yet"
                          description="They are created automatically when a student completes a course." />
    @else
        <x-admin.table :headings="['Number', 'Student', 'Course', 'Completed', ['label' => '', 'class' => 'text-right']]">
            @foreach ($certificates as $certificate)
                <tr>
                    <td class="px-4 py-3 font-mono text-[0.8125rem] text-ink-800">{{ $certificate->certificate_number }}</td>
                    <td class="px-4 py-3">
                        @if ($certificate->user)
                            <a href="{{ route('admin.students.show', $certificate->user) }}"
                               class="text-ink-900 hover:underline">{{ $certificate->student_name }}</a>
                        @else
                            <span class="text-ink-600">{{ $certificate->student_name }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $certificate->course_title }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-ink-600">{{ $certificate->completed_on->format('j M Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                            <a href="{{ route('certificates.verify', $certificate) }}" target="_blank" rel="noopener"
                               class="text-[0.8125rem] text-pine-700 hover:underline">Public page</a>
                            <x-admin.delete-form :action="route('admin.certificates.destroy', $certificate)"
                                                 label="Revoke"
                                                 confirm="Revoke this certificate? It is reissued if the student still meets the requirements." />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $certificates->links() }}</div>
    @endif
</x-layouts.admin>
