<x-layouts.app title="Bookmarks" heading="My bookmarks"
               description="Lessons you have saved to come back to later.">

    @if ($bookmarks->isEmpty())
        <x-ui.empty-state icon="tag" title="No bookmarks yet"
                          description="Bookmark a lesson while learning to find it here quickly next time.">
            <x-ui.button :href="route('my-courses')">Back to my courses</x-ui.button>
        </x-ui.empty-state>
    @else
        <ul class="divide-y divide-ink-200 border border-ink-200 bg-white">
            @foreach ($bookmarks as $bookmark)
                <li class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <p class="text-[0.8125rem] text-ink-500">{{ $bookmark->course->title }}</p>
                        <a href="{{ route('learn.lesson', [$bookmark->course, $bookmark->lesson]) }}"
                           class="text-[0.9375rem] font-medium text-ink-900 hover:underline">{{ $bookmark->lesson->title }}</a>
                        @if ($bookmark->lesson->module)
                            <p class="mt-0.5 text-[0.8125rem] text-ink-500">{{ $bookmark->lesson->module->title }}</p>
                        @endif
                    </div>
                    <x-ui.button :href="route('learn.lesson', [$bookmark->course, $bookmark->lesson])" variant="outline" size="sm">
                        Open lesson
                    </x-ui.button>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
