<x-layouts.admin title="Course questions" heading="Course questions"
                 description="Unanswered questions appear first.">

    @if ($questions->isEmpty())
        <x-ui.empty-state icon="message" title="No questions yet" description="Student questions from every course appear here." />
    @else
        <x-admin.table :headings="['Question', 'Course', 'Asked by', 'Replies', ['label' => '', 'class' => 'text-right']]">
            @foreach ($questions as $question)
                <tr class="{{ $question->replies_count === 0 ? 'bg-brass-50/40' : '' }}">
                    <td class="max-w-md px-4 py-3 text-ink-900">{{ \Illuminate\Support\Str::limit($question->question, 100) }}</td>
                    <td class="px-4 py-3 text-ink-600">{{ $question->course->title }}</td>
                    <td class="px-4 py-3 text-ink-600">{{ $question->user->name }}</td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $question->replies_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end">
                            <a href="{{ route('questions.index', $question->course) }}" class="text-[0.8125rem] text-pine-700 hover:underline">
                                {{ $question->replies_count === 0 ? 'Answer' : 'Open' }}
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $questions->links() }}</div>
    @endif
</x-layouts.admin>
