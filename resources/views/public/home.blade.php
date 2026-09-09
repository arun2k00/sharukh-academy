<x-layouts.public :description="$settings['site_tagline'] ?? null">
    {{-- Hero --}}
    <section class="border-b border-ink-200">
        <div class="mx-auto grid max-w-6xl gap-12 px-5 py-16 lg:grid-cols-12 lg:gap-16 lg:px-8 lg:py-24">
            <div class="lg:col-span-7">
                <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">
                    A focused online academy
                </p>

                <h1 class="mt-5 text-[2.25rem] leading-[1.1] sm:text-[3rem] lg:text-[3.25rem]">
                    {{ $settings['hero_heading'] ?? 'Learn one craft properly, from people who practise it.' }}
                </h1>

                <p class="mt-6 max-w-xl text-[1.0625rem]/8 text-ink-600">
                    {{ $settings['hero_subheading'] ?? 'A small, deliberate catalogue of programmes. Recorded lessons you can work through at your own pace, live sessions with your instructor, and the material you need to apply it the same week.' }}
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <x-ui.button :href="route('courses.index')" size="lg" icon-after="arrow-right">Explore courses</x-ui.button>
                    <x-ui.button :href="route('about')" variant="outline" size="lg">How we teach</x-ui.button>
                </div>

                <p class="mt-8 text-[0.8125rem] text-ink-500">
                    Live classes included · Downloadable resources · Certificate on completion
                </p>
            </div>

            <div class="lg:col-span-5">
                @if ($featuredCourses->isNotEmpty())
                    @php $spotlight = $featuredCourses->first(); @endphp
                    {{-- A composed information panel rather than a decorative image. --}}
                    <a href="{{ route('courses.show', $spotlight) }}" class="group block bg-ink-950">
                        @if ($spotlight->thumbnail_url)
                            <img src="{{ $spotlight->thumbnail_url }}" alt="" class="aspect-[16/9] w-full object-cover">
                        @endif

                        <div class="p-6">
                            <p class="text-[0.6875rem] font-medium tracking-[0.14em] text-white/50 uppercase">Now enrolling</p>
                            <p class="mt-3 font-serif text-[1.375rem]/tight text-white group-hover:underline group-hover:underline-offset-4">
                                {{ $spotlight->title }}
                            </p>

                            <dl class="mt-5 grid grid-cols-3 gap-4 border-t border-white/15 pt-5 text-white/70">
                                <div>
                                    <dt class="text-[0.6875rem] tracking-wide uppercase">Lessons</dt>
                                    <dd class="mt-1 font-serif text-lg text-white tabular-nums">{{ $spotlight->lessons_count }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[0.6875rem] tracking-wide uppercase">Length</dt>
                                    <dd class="mt-1 font-serif text-lg text-white">{{ $spotlight->duration_label }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[0.6875rem] tracking-wide uppercase">Level</dt>
                                    <dd class="mt-1 font-serif text-lg text-white">{{ $spotlight->level->label() }}</dd>
                                </div>
                            </dl>

                            <p class="mt-5 flex items-center gap-2 text-sm font-medium text-white">
                                See the full curriculum
                                <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                            </p>
                        </div>
                    </a>
                @else
                    <div class="flex aspect-[4/5] flex-col justify-end bg-ink-950 p-8">
                        <p class="font-serif text-[1.5rem]/tight text-white">
                            Small cohorts. Real practitioners. Work you can show.
                        </p>
                        <p class="mt-4 text-sm/6 text-white/50">{{ config('academy.name') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Measured facts, presented as a rule-divided strip rather than stat cards --}}
    <section class="border-b border-ink-200 bg-white">
        <dl class="mx-auto grid max-w-6xl divide-y divide-ink-200 px-5 sm:grid-cols-3 sm:divide-x sm:divide-y-0 lg:px-8">
            <div class="py-8 sm:pr-8">
                <dt class="text-[0.8125rem] text-ink-500">Programmes offered</dt>
                <dd class="mt-2 font-serif text-3xl text-ink-900 tabular-nums">{{ $stats['courses'] }}</dd>
            </div>
            <div class="py-8 sm:px-8">
                <dt class="text-[0.8125rem] text-ink-500">Students enrolled</dt>
                <dd class="mt-2 font-serif text-3xl text-ink-900 tabular-nums">{{ number_format($stats['students']) }}</dd>
            </div>
            <div class="py-8 sm:pl-8">
                <dt class="text-[0.8125rem] text-ink-500">Hours of instruction</dt>
                <dd class="mt-2 font-serif text-3xl text-ink-900 tabular-nums">{{ $stats['lessonHours'] }}</dd>
            </div>
        </dl>
    </section>

    {{-- Featured courses --}}
    <section class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="The catalogue"
                title="Programmes open for enrolment"
                description="We keep the list short on purpose. Each programme is maintained, taught live, and updated as the field moves." />

            <a href="{{ route('courses.index') }}" class="flex items-center gap-1.5 text-sm font-medium text-pine-700 hover:text-pine-800">
                All courses
                <x-ui.icon name="arrow-right" class="size-4" />
            </a>
        </div>

        @if ($featuredCourses->isEmpty())
            <x-ui.empty-state class="mt-10" title="Courses are on their way"
                description="Our first programmes are being finalised. Leave us a note and we will tell you the moment enrolment opens.">
                <x-ui.button :href="route('contact')" variant="outline">Contact the academy</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredCourses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Upcoming live classes --}}
    @if ($upcomingClasses->isNotEmpty())
        <section class="border-b border-ink-200 bg-white">
            <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
                <x-ui.section-heading eyebrow="Live sessions" title="Upcoming live classes" />

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($upcomingClasses as $liveClass)
                        <a href="{{ route('live-classes.show', $liveClass) }}" class="group block border border-ink-200 p-5 hover:border-ink-400">
                            <p class="text-[0.6875rem] font-medium tracking-[0.12em] text-pine-700 uppercase">{{ $liveClass->course->title }}</p>
                            <h3 class="mt-2 text-[1.0625rem] leading-snug group-hover:underline group-hover:underline-offset-4">
                                {{ $liveClass->title }}
                            </h3>
                            <p class="mt-3 flex items-center gap-1.5 text-[0.8125rem] text-ink-600">
                                <x-ui.icon name="calendar" class="size-3.5 text-ink-400" />
                                {{ $liveClass->scheduled_at->format('j F, g:ia') }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- How it works --}}
    <section class="border-y border-ink-200 bg-white">
        <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <x-ui.section-heading eyebrow="How the academy works" title="Structured to fit around real work" />

            <div class="mt-12 grid gap-x-10 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Study at your own pace', 'Recorded lessons stay available for the life of your enrolment. Pick up exactly where you left off, on any device.'],
                    ['Live classes with your instructor', 'Scheduled Zoom sessions for questions, critique and the parts that are easier shown than written.'],
                    ['Material you can actually use', 'Templates, worksheets and reference files attached to the lessons that need them.'],
                    ['Finish with proof', 'Complete the curriculum and your certificate is issued automatically, with a public verification link.'],
                ] as $index => $item)
                    <div class="border-t border-ink-900 pt-5">
                        <p class="font-mono text-[0.8125rem] text-ink-400 tabular-nums">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="mt-3 font-sans text-[0.975rem] font-semibold text-ink-900">{{ $item[0] }}</h3>
                        <p class="mt-2 text-sm/6 text-ink-600">{{ $item[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Instructor --}}
    @if ($leadInstructor)
        <section class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-4">
                    @if ($leadInstructor->avatar_url)
                        <img src="{{ $leadInstructor->avatar_url }}" alt="{{ $leadInstructor->name }}"
                             class="aspect-[4/5] w-full object-cover" loading="lazy">
                    @else
                        <div class="flex aspect-[4/5] w-full items-center justify-center bg-ink-900">
                            <span class="font-serif text-5xl text-sand-200">{{ str($leadInstructor->name)->substr(0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-8">
                    <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Who teaches here</p>
                    <h2 class="mt-4 text-[1.75rem] leading-tight">{{ $leadInstructor->name }}</h2>
                    @if ($leadInstructor->headline)
                        <p class="mt-2 text-[0.975rem] text-ink-600">{{ $leadInstructor->headline }}</p>
                    @endif

                    @if ($leadInstructor->bio)
                        <div class="prose-academy mt-6 max-w-2xl">
                            @foreach (preg_split('/\r\n|\r|\n/', $leadInstructor->bio) as $paragraph)
                                @if (trim($paragraph) !== '')
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($leadInstructor->credentials))
                        <ul class="mt-8 grid gap-x-8 gap-y-2.5 sm:grid-cols-2">
                            @foreach ($leadInstructor->credentials as $credential)
                                <li class="flex gap-2.5 text-sm text-ink-700">
                                    <x-ui.icon name="check" class="mt-1 size-3.5 shrink-0 text-pine-600" />
                                    {{ $credential }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($leadInstructor->experience_years)
                        <p class="mt-8 border-t border-ink-200 pt-5 text-[0.8125rem] text-ink-500">
                            {{ $leadInstructor->experience_years }} years in practice
                        </p>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="border-y border-ink-200 bg-white">
            <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
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
        <section class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-4">
                    <x-ui.section-heading eyebrow="Questions" title="Before you enrol" />
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
    <section class="bg-ink-950">
        <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <div class="grid gap-8 lg:grid-cols-12 lg:items-end lg:gap-16">
                <div class="lg:col-span-8">
                    <h2 class="text-[1.875rem] leading-tight text-white sm:text-[2.25rem]">
                        Start the programme that matches where you are now.
                    </h2>
                    <p class="mt-4 max-w-xl text-[0.975rem]/7 text-white/60">
                        Create an account to see the full curriculum, watch the preview lessons and enrol.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
                    <x-ui.button :href="route('courses.index')" variant="light" size="lg">Browse courses</x-ui.button>
                    @guest
                        <x-ui.button :href="route('register')" variant="outline" size="lg"
                                     class="!border-white/25 !bg-transparent !text-white hover:!bg-white/10">
                            Create account
                        </x-ui.button>
                    @endguest
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
