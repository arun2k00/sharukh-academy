<x-layouts.public :description="$settings['site_tagline'] ?? null">
    {{-- Hero --}}
    <section class="border-b border-ink-200 overflow-hidden">
        <div class="mx-auto grid max-w-[1600px] gap-12 px-5 py-16 lg:grid-cols-12 lg:items-center lg:gap-16 lg:px-8 lg:py-20 xl:py-28">
            <div class="lg:col-span-7 xl:col-span-6">
                <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">
                    A focused data engineering academy
                </p>

                <h1 class="mt-5 max-w-2xl text-[2.125rem] leading-[1.15] whitespace-pre-line sm:text-[2.75rem] lg:text-[2.75rem] xl:text-[3rem]">
                    {{ $settings['hero_heading'] ?? 'Learn one craft properly, from people who practise it.' }}
                </h1>

                <p class="mt-6 max-w-md text-balance text-[1.0625rem]/8 text-ink-600">
                    {{ $settings['hero_subheading'] ?? 'Six focused programmes, each with live classes, production-grade projects and a certificate that means something.' }}
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <x-ui.button :href="route('courses.index')" size="lg" icon-after="arrow-right">Explore courses</x-ui.button>
                    <x-ui.button :href="route('about')" variant="outline" size="lg">How we teach</x-ui.button>
                </div>

                <p class="mt-8 text-[0.8125rem] text-ink-500">
                    Live classes included · Production-grade projects · Certificate on completion
                </p>
            </div>

            <div class="relative lg:col-span-5 xl:col-span-6">
                {{-- Soft brand-coloured field behind the artwork, rather than a hard image edge. --}}
                <div class="absolute inset-0 -z-10 scale-90 rounded-full bg-linear-to-br from-pine-200 via-pine-100 to-sand-100 blur-2xl"
                     aria-hidden="true"></div>

                <img src="{{ asset('banner.png') }}" alt=""
                     class="mx-auto w-full max-w-md object-contain drop-shadow-xl transition-transform duration-500 hover:-translate-y-1 lg:max-w-lg xl:max-w-xl">
            </div>
        </div>
    </section>

    {{-- Measured facts, presented as a rule-divided strip rather than stat cards --}}
    <section class="border-b border-ink-200 bg-texture-dots">
        <dl class="mx-auto grid max-w-[1600px] divide-y divide-ink-200 px-5 sm:grid-cols-3 sm:divide-x sm:divide-y-0 lg:px-8">
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

    <section class="border-b border-ink-200 bg-white py-10">
        <x-site.placement-scroller class="mx-auto max-w-[1600px] px-5 lg:px-12" />
    </section>

    {{-- Featured courses --}}
    <section class="mx-auto max-w-[1600px] px-5 py-16 lg:px-8 lg:py-20">
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
        <section class="border-b border-ink-200 bg-texture-dots">
            <div class="mx-auto max-w-[1600px] px-5 py-16 lg:px-8 lg:py-20">
                <x-ui.section-heading eyebrow="Live sessions" title="Upcoming live classes" />

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($upcomingClasses as $liveClass)
                        <a href="{{ route('live-classes.show', $liveClass) }}" class="card-interactive group block border border-ink-100 bg-white p-5">
                            <p class="text-[0.6875rem] font-medium tracking-[0.12em] text-pine-700 uppercase">{{ $liveClass->course->title }}</p>
                            <h3 class="mt-2 text-[1.0625rem] leading-snug group-hover:underline group-hover:underline-offset-4">
                                {{ $liveClass->title }}
                            </h3>
                            <p class="mt-3 flex items-center gap-1.5 text-[0.8125rem] text-ink-600">
                                <x-ui.icon name="calendar" class="size-3.5 text-ink-400" />
                                {{ $liveClass->scheduled_at->format('j F, g:ia') }}
                            </p>

                            <div x-data="{
                                    target: new Date('{{ $liveClass->scheduled_at->toIso8601String() }}').getTime(),
                                    remaining: 0,
                                    tick() { this.remaining = Math.max(0, this.target - Date.now()); },
                                    get d() { return Math.floor(this.remaining / 86400000); },
                                    get h() { return Math.floor((this.remaining % 86400000) / 3600000); },
                                    get m() { return Math.floor((this.remaining % 3600000) / 60000); },
                                 }"
                                 x-init="tick(); setInterval(() => tick(), 30000)"
                                 class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-blush-50 px-3 py-1 text-[0.75rem] font-medium text-blush-600">
                                <x-ui.icon name="clock" class="size-3.5 shrink-0" />
                                <span x-show="d > 0">Starts in <span x-text="d"></span>d <span x-text="h"></span>h</span>
                                <span x-show="d === 0">Starts in <span x-text="h"></span>h <span x-text="m"></span>m</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- How it works --}}
    <section class="border-y border-ink-200 bg-texture-dots">
        <div class="mx-auto max-w-[1600px] px-5 py-16 lg:px-8 lg:py-20">
            <x-ui.section-heading eyebrow="How the academy works" title="Structured to fit around real work" />

            <div class="mt-12 grid gap-x-10 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Study at your own pace', 'Recorded lessons stay available for the life of your enrolment. Pick up exactly where you left off, on any device.'],
                    ['Live classes with your instructor', 'Scheduled Zoom sessions for questions, critique and the parts that are easier shown than written.'],
                    ['Material you can actually use', 'Templates, worksheets and reference files attached to the lessons that need them.'],
                    ['Finish with proof', 'Complete the curriculum and your certificate is issued automatically, with a public verification link.'],
                ] as $index => $item)
                    <div class="group border-t-2 border-ink-900 pt-5 transition-colors duration-200 hover:border-pine-600">
                        <p class="font-mono text-[0.8125rem] text-ink-400 tabular-nums transition-colors group-hover:text-pine-600">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="mt-3 font-sans text-[0.975rem] font-semibold text-ink-900">{{ $item[0] }}</h3>
                        <p class="mt-2 text-sm/6 text-ink-600">{{ $item[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="border-y border-ink-200 bg-texture-dots">
            <div class="mx-auto max-w-[1600px] px-5 py-16 lg:px-8 lg:py-20">
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
        <section class="mx-auto max-w-[1600px] px-5 py-16 lg:px-8 lg:py-20">
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
        <div class="mx-auto max-w-[1600px] px-5 py-16 lg:px-8 lg:py-20">
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
