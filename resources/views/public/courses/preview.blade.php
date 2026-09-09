<x-layouts.public :title="$lesson->title.' — preview'" :description="$lesson->description">
    <section class="border-b border-ink-200 bg-white">
        <div class="mx-auto max-w-4xl px-5 py-8 lg:px-8">
            <x-ui.breadcrumbs :items="[
                ['label' => 'Courses', 'url' => route('courses.index')],
                ['label' => $course->title, 'url' => route('courses.show', $course)],
                ['label' => 'Free preview'],
            ]" />
        </div>
    </section>

    <div class="mx-auto max-w-4xl px-5 py-10 lg:px-8 lg:py-14">
        <x-ui.badge class="bg-pine-50 text-pine-700">Free preview lesson</x-ui.badge>

        <h1 class="mt-4 text-[1.75rem] leading-tight">{{ $lesson->title }}</h1>

        <div class="mt-7">
            <x-ui.video-embed :src="$lesson->embed_url" :title="$lesson->title" />
        </div>

        @if ($lesson->description)
            <p class="mt-6 text-[0.975rem]/7 text-ink-700">{{ $lesson->description }}</p>
        @endif

        <div class="mt-10 border border-ink-200 bg-white p-6 sm:flex sm:items-center sm:justify-between sm:gap-6">
            <div>
                <h2 class="font-serif text-lg text-ink-900">Continue with the full course</h2>
                <p class="mt-1.5 text-sm text-ink-600">
                    {{ $course->title }} — {{ $course->duration_label }} of instruction, plus live classes and resources.
                </p>
            </div>
            <x-ui.button :href="route('courses.show', $course)" class="mt-5 shrink-0 sm:mt-0">
                View the course
            </x-ui.button>
        </div>
    </div>
</x-layouts.public>
