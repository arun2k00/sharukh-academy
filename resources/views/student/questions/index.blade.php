<x-layouts.app :title="'Questions — '.$course->title" :heading="'Questions — '.$course->title"
               description="Ask a question about this course. Your instructor and other students can reply.">

    <a href="{{ route('learn.course', $course) }}" class="mb-6 inline-flex items-center gap-1.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">
        <x-ui.icon name="arrow-left" class="size-3.5" />
        Back to the course
    </a>

    <x-ui.panel title="Ask a question" class="mb-8">
        <form method="POST" action="{{ route('questions.store', $course) }}" class="space-y-4">
            @csrf
            <x-ui.textarea name="question" rows="3" required placeholder="What would you like to ask?">{{ old('question') }}</x-ui.textarea>
            <x-ui.button type="submit">Post question</x-ui.button>
        </form>
    </x-ui.panel>

    @if ($questions->isEmpty())
        <x-ui.empty-state icon="message" title="No questions yet" description="Be the first to ask something about this course." />
    @else
        <div class="space-y-6">
            @foreach ($questions as $question)
                <div class="border border-ink-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <x-ui.avatar :src="$question->user->avatar_url" :name="$question->user->name" size="xs" />
                            <div>
                                <p class="text-sm font-medium text-ink-900">{{ $question->user->name }}</p>
                                <p class="text-[0.75rem] text-ink-400">{{ $question->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if (auth()->user()->isAdmin() || $question->user_id === auth()->id())
                            <form method="POST" action="{{ route('questions.destroy', $question) }}" onsubmit="return confirm('Delete this question?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[0.75rem] text-ink-400 hover:text-red-700">Delete</button>
                            </form>
                        @endif
                    </div>
                    <p class="mt-3 text-[0.9375rem]/7 text-ink-700">{{ $question->question }}</p>
                    @if ($question->lesson)
                        <p class="mt-2 text-[0.75rem] text-ink-500">Re: {{ $question->lesson->title }}</p>
                    @endif

                    @if ($question->replies->isNotEmpty())
                        <div class="mt-4 space-y-3 border-t border-ink-100 pt-4 pl-4">
                            @foreach ($question->replies as $reply)
                                <div class="{{ $reply->is_pinned ? 'border-l-2 border-pine-600 pl-3' : '' }}">
                                    <div class="flex items-center gap-2">
                                        <p class="text-[0.8125rem] font-medium text-ink-900">{{ $reply->user->name }}</p>
                                        @if ($reply->user->isAdmin())
                                            <x-ui.badge class="bg-pine-50 text-pine-700">Instructor</x-ui.badge>
                                        @endif
                                        @if ($reply->is_pinned)
                                            <x-ui.badge class="bg-brass-50 text-brass-700">Pinned</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-[0.875rem]/6 text-ink-700">{{ $reply->reply }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('questions.replies.store', $question) }}" class="mt-4 flex gap-2">
                        @csrf
                        <x-ui.input name="reply" placeholder="Write a reply…" class="flex-1" required />
                        <x-ui.button type="submit" size="sm">Reply</x-ui.button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
