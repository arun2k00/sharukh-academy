@php
    $stageList = $stages->keys();
    $firstCourse = $careerPath->courses->first();
@endphp

<x-layouts.public :title="$careerPath->title"
                  :description="$careerPath->subtitle ?? \Illuminate\Support\Str::limit($careerPath->description, 155)">
    <x-slot:topBar>
        <div x-data="countdownTimer('{{ $careerPath->slug }}', 14)" x-show="!dismissed" x-cloak
             class="relative bg-ink-950 px-10 py-2.5 text-center">
            <p class="text-[0.8125rem] text-white">
                <x-ui.icon name="clock" class="-mt-0.5 mr-1 inline size-4 text-blush-100" />
                This enrollment window closes in
                <span class="font-mono font-semibold text-blush-100 tabular-nums">
                    <span x-text="hh"></span>:<span x-text="mm"></span>:<span x-text="ss"></span>
                </span>
                <a href="#roadmap" class="ml-1 underline underline-offset-2 hover:text-blush-100">Enroll now</a>
            </p>
            <button type="button" @click="dismissed = true" aria-label="Dismiss"
                    class="absolute top-1/2 right-3 -translate-y-1/2 p-1 text-white/40 hover:text-white">
                <x-ui.icon name="close" class="size-3.5" />
            </button>
        </div>
    </x-slot:topBar>

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-ink-950">
        <div class="absolute inset-0 -z-10 bg-linear-to-br from-pine-900/50 via-ink-950 to-ink-950"></div>
        <div class="bg-texture-dots-overlay absolute inset-0 -z-10 opacity-60" aria-hidden="true"></div>
        <div class="hero-blob absolute -top-16 -left-16 -z-10 size-96 rounded-full bg-pine-500/30 blur-3xl" aria-hidden="true"></div>
        <div class="hero-blob-alt absolute top-1/3 -right-24 -z-10 size-[26rem] rounded-full bg-pine-400/20 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-[1400px] gap-16 px-5 py-16 lg:grid-cols-12 lg:items-start lg:gap-10 lg:px-8 lg:py-24">
            <div class="lg:col-span-7">
                @unless ($careerPath->is_published)
                    <p class="mb-4 inline-flex items-center gap-1.5 bg-white px-2.5 py-1 text-[0.6875rem] font-medium tracking-wide text-ink-900 uppercase">
                        Draft. Only visible to you as an administrator
                    </p>
                @endunless

                <p class="flex items-center gap-2 text-[0.6875rem] font-medium tracking-[0.16em] text-pine-300 uppercase">
                    <x-ui.icon name="graduation-cap" class="size-4" />
                    Career Plus roadmap
                </p>

                <h1 class="mt-5 max-w-xl font-serif text-[2.25rem] leading-[1.12] text-white sm:text-[3rem] lg:text-[3.35rem]">
                    {{ $careerPath->title }}
                </h1>

                @if ($careerPath->subtitle)
                    <p class="mt-5 max-w-xl text-[1.0625rem]/8 text-white/70">{{ $careerPath->subtitle }}</p>
                @endif

                <p class="mt-4 max-w-xl text-sm/7 text-white/55">{{ \Illuminate\Support\Str::limit($careerPath->description, 160) }}</p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    @if ($firstCourse)
                        <x-ui.button :href="route('courses.show', $firstCourse)" variant="light" size="lg" icon-after="arrow-right">
                            Start with the first course
                        </x-ui.button>
                    @endif
                    <x-ui.button href="#roadmap" variant="outline" size="lg"
                                 class="!border-white/25 !bg-transparent !text-white hover:!bg-white/10">
                        See the full roadmap
                    </x-ui.button>
                </div>
            </div>

            <div class="hidden lg:col-span-5 lg:block">
                <img src="{{ $careerPath->banner_url ?? asset('hero.webp') }}" alt=""
                     class="mx-auto w-full max-w-lg object-contain drop-shadow-2xl">
            </div>
        </div>
    </section>

    {{-- Stats strip --}}
    <section class="bg-texture-dark">
        <div class="mx-auto max-w-[1400px] px-5 py-10 lg:px-8">
            <dl class="grid grid-cols-2 gap-x-6 gap-y-8 lg:grid-cols-4 lg:divide-x lg:divide-white/10">
                <div class="lg:pr-6">
                    <dt class="text-[0.75rem] text-white/50">Courses</dt>
                    <dd class="mt-1.5 font-serif text-3xl text-white tabular-nums">{{ $stats['courses'] }}</dd>
                </div>
                <div class="lg:px-6">
                    <dt class="text-[0.75rem] text-white/50">Stages</dt>
                    <dd class="mt-1.5 font-serif text-3xl text-white tabular-nums">{{ $stageList->count() }}</dd>
                </div>
                <div class="lg:px-6">
                    <dt class="text-[0.75rem] text-white/50">Hours of instruction</dt>
                    <dd class="mt-1.5 font-serif text-3xl text-white tabular-nums">{{ $stats['hours'] }}</dd>
                </div>
                <div class="lg:pl-6">
                    <dt class="text-[0.75rem] text-white/50">{{ $stats['students'] > 0 ? 'Students on this path' : 'Finishes with' }}</dt>
                    <dd class="mt-1.5 font-serif text-3xl text-white tabular-nums">
                        {{ $stats['students'] > 0 ? number_format($stats['students']) : ($careerPath->hasCapstone() ? 'A capstone' : 'Self-paced') }}
                    </dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- Instructor credibility strip — real credentials, not stock logos --}}
    @if ($instructors->isNotEmpty())
        <section class="border-b border-ink-200 bg-white py-6">
            <div class="mx-auto flex max-w-[1400px] flex-wrap items-center justify-center gap-x-10 gap-y-3 px-5 lg:px-8">
                @foreach ($instructors->flatMap(fn ($i) => $i->credentials ?? [])->unique()->take(4) as $credential)
                    <p class="flex items-center gap-2 text-[0.8125rem] font-medium text-ink-700">
                        <x-ui.icon name="check-circle" class="size-4 text-pine-600" />
                        {{ $credential }}
                    </p>
                @endforeach
            </div>
        </section>
    @endif

    {{-- What you'll walk away able to do --}}
    @if (! empty($outcomes))
        <section class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
            <x-ui.section-heading eyebrow="The outcome" title="What you'll walk away able to do"
                description="Not a list of topics covered. These are the actual, specific things you'll be able to do once you've worked through this path." />

            <div class="mt-10 grid gap-x-10 gap-y-5 sm:grid-cols-2">
                @foreach ($outcomes as $outcome)
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center bg-pine-50 text-pine-700">
                            <x-ui.icon name="check" class="size-3.5" />
                        </span>
                        <p class="text-[0.975rem]/7 text-ink-700">{{ $outcome }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- The roadmap --}}
    <section id="roadmap" class="border-t border-ink-200 bg-texture-dots">
        <div class="mx-auto max-w-[1400px] scroll-mt-8 px-5 py-16 lg:px-8 lg:py-20">
            <x-ui.section-heading eyebrow="The roadmap" title="Every step, in order"
                description="Or take the same path one course at a time: each step below is a complete, independently enrollable programme on its own." />

            <ol class="mt-12 space-y-14">
                @foreach ($stages as $stageLabel => $coursesInStage)
                    <li>
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center border border-pine-700 bg-white font-mono text-sm text-pine-700">
                                {{ $loop->iteration }}
                            </span>
                            <p class="text-[0.8125rem] font-medium tracking-[0.16em] text-ink-900 uppercase">{{ $stageLabel }}</p>
                        </div>

                        <div class="mt-6 ml-4 grid gap-6 border-l border-ink-200 py-2 pl-8 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($coursesInStage as $course)
                                <x-site.course-card :course="$course" :has-access="$accessByCourseId[$course->id] ?? false" />
                            @endforeach
                        </div>
                    </li>
                @endforeach

                @if ($careerPath->hasCapstone())
                    <li>
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center border border-brass-600 bg-white font-mono text-sm text-brass-700">
                                {{ $stageList->count() + 1 }}
                            </span>
                            <p class="text-[0.8125rem] font-medium tracking-[0.16em] text-ink-900 uppercase">Real-world project</p>
                        </div>
                        <div class="mt-6 ml-4 border-l border-ink-200 py-2 pl-8">
                            <div class="border border-dashed border-brass-300 bg-brass-50/40 p-6">
                                @if ($careerPath->capstone_title)
                                    <h3 class="font-serif text-lg leading-snug text-ink-900">{{ $careerPath->capstone_title }}</h3>
                                @endif
                                @if ($careerPath->capstone_description)
                                    <p class="mt-2 text-sm/6 text-ink-600">{{ $careerPath->capstone_description }}</p>
                                @endif
                            </div>
                        </div>
                    </li>
                @endif

                <li class="flex items-center gap-3">
                    <span class="flex size-9 shrink-0 items-center justify-center bg-pine-700 text-white">
                        <x-ui.icon name="award" class="size-4" />
                    </span>
                    <p class="text-[0.8125rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Job-ready</p>
                </li>
            </ol>
        </div>
    </section>

    {{-- Is this for you? --}}
    @if (! empty($audience) || ! empty($requirements))
        <section class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
            <x-ui.section-heading eyebrow="Fit check" title="Is this the right path for you?" />

            <div class="mt-10 grid gap-10 lg:grid-cols-2">
                @if (! empty($audience))
                    <div class="border border-ink-200 bg-white p-6 sm:p-8">
                        <p class="flex items-center gap-2 text-[0.8125rem] font-medium tracking-[0.1em] text-pine-700 uppercase">
                            <x-ui.icon name="check-circle" class="size-4" />
                            This path is for you if
                        </p>
                        <ul class="mt-5 space-y-3">
                            @foreach ($audience as $line)
                                <li class="flex items-start gap-2.5 text-sm/6 text-ink-700">
                                    <span class="mt-2 size-1 shrink-0 rounded-full bg-ink-400"></span>
                                    {{ $line }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($requirements))
                    <div class="border border-ink-200 bg-sand-50 p-6 sm:p-8">
                        <p class="flex items-center gap-2 text-[0.8125rem] font-medium tracking-[0.1em] text-ink-700 uppercase">
                            <x-ui.icon name="sliders" class="size-4" />
                            What you need to start
                        </p>
                        <ul class="mt-5 space-y-3">
                            @foreach ($requirements as $line)
                                <li class="flex items-start gap-2.5 text-sm/6 text-ink-700">
                                    <span class="mt-2 size-1 shrink-0 rounded-full bg-ink-400"></span>
                                    {{ $line }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Meet your instructors --}}
    @if ($instructors->isNotEmpty())
        <section class="border-y border-ink-200 bg-texture-dots">
            <div class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
                <x-ui.section-heading eyebrow="Who teaches it" title="Meet your instructors" />

                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($instructors as $instructor)
                        <a href="{{ route('faculty') }}" class="group flex flex-col gap-4 border border-ink-200 bg-white p-6 transition-shadow duration-200 hover:shadow-md">
                            <div class="flex items-start gap-4">
                                <x-ui.avatar :src="$instructor->avatar_url" :name="$instructor->name" size="lg" />
                                <div class="min-w-0">
                                    <p class="font-medium text-ink-900 transition-colors group-hover:text-pine-700">{{ $instructor->name }}</p>
                                    @if ($instructor->headline)
                                        <p class="mt-1 text-[0.8125rem]/6 text-ink-600">{{ $instructor->headline }}</p>
                                    @endif
                                </div>
                            </div>
                            @if ($instructor->experience_years)
                                <p class="flex w-fit items-center gap-1.5 border border-ink-200 bg-sand-50 px-2.5 py-1 text-[0.75rem] text-ink-600">
                                    <x-ui.icon name="award" class="size-3.5 text-pine-600" />
                                    {{ $instructor->experience_years }}+ years experience
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- The offer --}}
    <section class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
        <x-ui.section-heading align="center" eyebrow="Two ways to enroll" title="Take it step by step, or all at once" />

        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            <div class="border border-ink-200 bg-white p-8">
                <p class="text-[0.8125rem] font-medium tracking-[0.1em] text-ink-500 uppercase">One course at a time</p>
                <p class="mt-3 font-serif text-2xl text-ink-900">₹{{ number_format($stats['individualTotal'], 0) }} total</p>
                <p class="mt-2 text-sm/6 text-ink-600">
                    Enroll in each of the {{ $stats['courses'] }} courses separately, in the order shown above. Pay as you go,
                    and nothing commits you to the rest of the path.
                </p>
                @if ($firstCourse)
                    <x-ui.button :href="route('courses.show', $firstCourse)" variant="outline" class="mt-6 w-full">
                        Start with the first course
                    </x-ui.button>
                @endif
            </div>

            @if ($careerPath->bundleCourse)
                @php $bundle = $careerPath->bundleCourse; @endphp
                <div class="relative border-2 border-pine-700 bg-white p-8">
                    <p class="absolute -top-3 left-8 bg-pine-700 px-3 py-1 text-[0.6875rem] font-medium tracking-[0.1em] text-white uppercase">
                        Best value
                    </p>
                    <p class="text-[0.8125rem] font-medium tracking-[0.1em] text-pine-700 uppercase">All-in-one bundle</p>
                    <div class="mt-3 flex items-baseline gap-3">
                        <p class="font-serif text-2xl text-ink-900">{{ $bundle->price_label }}</p>
                        <p class="text-sm text-ink-400 line-through">₹{{ number_format($stats['individualTotal'], 0) }}</p>
                        @if ($stats['savingsPercent'])
                            <span class="bg-pine-50 px-2 py-0.5 text-[0.75rem] font-medium text-pine-700">
                                Save {{ $stats['savingsPercent'] }}%
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm/6 text-ink-600">
                        <span class="font-medium text-ink-900">{{ $bundle->title }}</span> covers this entire path in one
                        enrollment, one certificate, one place to track progress.
                    </p>
                    <x-ui.button :href="route('courses.show', $bundle)" class="mt-6 w-full">Enroll in the bundle</x-ui.button>
                </div>
            @endif
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-[0.8125rem] text-ink-600">
            @if ($allLifetimeAccess)
                <span class="flex items-center gap-1.5">
                    <x-ui.icon name="check" class="size-4 text-pine-600" />
                    Lifetime access to every course
                </span>
            @endif
            <span class="flex items-center gap-1.5">
                <x-ui.icon name="check" class="size-4 text-pine-600" />
                Certificate on completion
            </span>
            <span class="flex items-center gap-1.5">
                <x-ui.icon name="check" class="size-4 text-pine-600" />
                Live classes with your instructor
            </span>
        </div>
    </section>

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="border-y border-ink-200 bg-texture-dots">
            <div class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
                <x-ui.section-heading eyebrow="From our students" title="What people say after finishing" />

                <div class="mt-12 grid gap-10 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <x-site.testimonial :testimonial="$testimonial" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if ($faqs->isNotEmpty())
        <section class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-4">
                    <x-ui.section-heading eyebrow="Questions" title="Before you enroll" />
                    <p class="mt-6 text-sm/6 text-ink-600">
                        Something not covered here?
                        <a href="{{ route('contact') }}" class="text-pine-700 underline underline-offset-2">Write to us</a>
                        and we will answer within a working day.
                    </p>
                </div>

                <div class="lg:col-span-8">
                    <x-ui.accordion :items="$faqs->map(fn ($faq) => ['question' => $faq->question, 'answer' => $faq->answer])->all()" :open="0" />
                </div>
            </div>
        </section>
    @endif

    {{-- Closing call to action --}}
    <section class="relative overflow-hidden bg-ink-950">
        <div class="hero-blob absolute -left-16 -top-16 -z-10 size-80 rounded-full bg-pine-500/60 blur-3xl" aria-hidden="true"></div>
        <div class="hero-blob-alt absolute -right-16 -bottom-16 -z-10 size-80 rounded-full bg-pine-400/50 blur-3xl" aria-hidden="true"></div>
        <div class="bg-texture-dots-overlay absolute inset-0 -z-10" aria-hidden="true"></div>

        <div class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-20">
            <div class="grid gap-8 lg:grid-cols-12 lg:items-end lg:gap-16">
                <div class="lg:col-span-8">
                    <h2 class="text-[1.875rem] leading-tight text-white sm:text-[2.25rem]">
                        Ready to start {{ $careerPath->title }}?
                    </h2>
                    <p class="mt-4 max-w-xl text-[0.975rem]/7 text-white/60">
                        Enroll in the first course above. Each one is yours on its own, so you can go at your own pace
                        through the rest of the path.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
                    @if ($firstCourse)
                        <x-ui.button :href="route('courses.show', $firstCourse)" variant="light" size="lg">Start now</x-ui.button>
                    @endif
                    <x-ui.button :href="route('career-paths.index')" variant="outline" size="lg"
                                 class="!border-white/25 !bg-transparent !text-white hover:!bg-white/10">
                        Other Career Plus paths
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    {{-- Sticky enroll bar — appears once the hero's own CTA scrolls out of view --}}
    @if ($firstCourse && ! $careerPath->courses->every(fn ($course) => $accessByCourseId[$course->id] ?? false))
        <div x-data="{ show: false, dismissed: false }"
             x-init="window.addEventListener('scroll', () => { show = window.scrollY > 700 })"
             x-show="show && ! dismissed" x-transition x-cloak
             class="fixed inset-x-0 bottom-0 z-40 border-t border-ink-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-[1400px] items-center justify-between gap-4 px-5 py-3 lg:px-8">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-ink-900">{{ $careerPath->title }}</p>
                    <p class="text-[0.75rem] text-ink-500">
                        {{ $stats['courses'] }} courses
                        @if ($careerPath->bundleCourse)
                            · bundle from {{ $careerPath->bundleCourse->price_label }}
                        @endif
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <x-ui.button :href="route('courses.show', $firstCourse)" size="sm">Start now</x-ui.button>
                    <button type="button" @click="dismissed = true" aria-label="Dismiss" class="text-ink-400 hover:text-ink-700">
                        <x-ui.icon name="close" class="size-4" />
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-layouts.public>
