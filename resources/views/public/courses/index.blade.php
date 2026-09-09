<x-layouts.public title="Courses" description="The full Oacer Academy catalogue: expert-led programmes with live classes and certification.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">The catalogue</p>
            <h1 class="mt-4 max-w-3xl text-[2rem] leading-tight sm:text-[2.5rem]">
                Every programme the academy runs
            </h1>
            <p class="mt-5 max-w-2xl text-[1.0625rem]/8 text-ink-600">
                We deliberately keep this list short. Each programme is taught by someone who does the work,
                maintained as the field changes, and paired with live sessions where the questions get answered.
            </p>

            <form method="GET" class="mt-8 flex max-w-sm gap-2">
                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
                @endif
                <x-ui.input type="search" name="q" value="{{ $search }}" placeholder="Search course titles…" class="flex-1" />
                <x-ui.button type="submit" variant="outline" icon="search">Search</x-ui.button>
            </form>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <div class="border-b border-ink-200 bg-white">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <nav class="-mb-px flex flex-wrap gap-x-7 gap-y-1 overflow-x-auto" aria-label="Filter by subject">
                    <a href="{{ route('courses.index') }}"
                       class="border-b-2 py-4 text-sm whitespace-nowrap {{ $activeCategory === null ? 'border-ink-900 text-ink-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
                        All courses
                        <span class="ml-1.5 text-ink-400 tabular-nums">{{ $categories->sum('courses_count') }}</span>
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('courses.index', ['category' => $category->slug]) }}"
                           class="border-b-2 py-4 text-sm whitespace-nowrap {{ $activeCategory?->is($category) ? 'border-ink-900 text-ink-900' : 'border-transparent text-ink-500 hover:text-ink-900' }}">
                            {{ $category->name }}
                            <span class="ml-1.5 text-ink-400 tabular-nums">{{ $category->courses_count }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
    @endif

    <section class="mx-auto max-w-6xl px-5 py-12 lg:px-8 lg:py-16">
        @if ($activeCategory?->description)
            <p class="mb-8 max-w-2xl text-[0.9375rem] text-ink-600">{{ $activeCategory->description }}</p>
        @endif

        @if ($courses->isEmpty())
            <x-ui.empty-state
                :title="$search !== '' ? 'No courses match “'.$search.'”' : ($activeCategory ? 'Nothing in '.$activeCategory->name.' yet' : 'The catalogue is being prepared')"
                description="New programmes are announced by email first. Tell us what you are looking for and we will let you know.">
                <x-ui.button :href="route('contact')">Contact the academy</x-ui.button>
                @if ($activeCategory || $search !== '')
                    <x-ui.button :href="route('courses.index')" variant="outline">See all courses</x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="border-t border-ink-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-6 px-5 py-12 lg:px-8">
            <div class="max-w-xl">
                <h2 class="text-xl">Not sure which one fits?</h2>
                <p class="mt-2 text-sm/6 text-ink-600">
                    Tell us what you are working on and we will point you at the right programme — or tell you honestly
                    that we do not have one.
                </p>
            </div>
            <x-ui.button :href="route('contact')" variant="outline">Ask the academy</x-ui.button>
        </div>
    </section>
</x-layouts.public>
