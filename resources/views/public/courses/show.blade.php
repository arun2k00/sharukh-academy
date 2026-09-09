<x-layouts.public :title="$course->title" :description="$course->short_description">
    {{-- Course hero --}}
    <section class="border-b border-ink-200 bg-white">
        <div class="mx-auto max-w-6xl px-5 pt-8 lg:px-8">
            <x-ui.breadcrumbs :items="[
                ['label' => 'Courses', 'url' => route('courses.index')],
                ['label' => $course->title],
            ]" />
        </div>

        <div class="mx-auto grid max-w-6xl gap-10 px-5 py-10 lg:grid-cols-12 lg:gap-14 lg:px-8 lg:py-14">
            <div class="lg:col-span-7">
                @if ($course->category)
                    <p class="text-[0.6875rem] font-medium tracking-[0.14em] text-pine-700 uppercase">{{ $course->category->name }}</p>
                @endif

                <h1 class="mt-4 text-[2rem] leading-[1.15] sm:text-[2.5rem]">{{ $course->title }}</h1>

                <p class="mt-5 max-w-2xl text-[1.0625rem]/8 text-ink-600">{{ $course->short_description }}</p>

                @if ($course->instructor)
                    <div class="mt-7 flex items-center gap-3">
                        <x-ui.avatar :src="$course->instructor->avatar_url" :name="$course->instructor->name" size="sm" />
                        <div>
                            <p class="text-sm text-ink-900">Taught by {{ $course->instructor->name }}</p>
                            @if ($course->instructor->headline)
                                <p class="text-[0.8125rem] text-ink-500">{{ $course->instructor->headline }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <dl class="mt-8 grid grid-cols-2 gap-x-8 gap-y-5 border-t border-ink-200 pt-7 sm:grid-cols-4">
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Lessons</dt>
                        <dd class="mt-1.5 font-serif text-xl text-ink-900 tabular-nums">{{ $lessonCount }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Length</dt>
                        <dd class="mt-1.5 font-serif text-xl text-ink-900">{{ $course->duration_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Live classes</dt>
                        <dd class="mt-1.5 font-serif text-xl text-ink-900 tabular-nums">{{ $liveClassCount }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.6875rem] tracking-[0.08em] text-ink-500 uppercase">Level</dt>
                        <dd class="mt-1.5 font-serif text-xl text-ink-900">{{ $course->level->label() }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Enrolment panel --}}
            <div class="lg:col-span-5">
                <div class="border border-ink-200 lg:sticky lg:top-24">
                    @if ($course->thumbnail_url)
                        <img src="{{ $course->thumbnail_url }}" alt="" class="aspect-[16/9] w-full object-cover">
                    @endif

                    <div class="p-6">
                        <div class="flex items-baseline gap-3">
                            <p class="font-serif text-[1.75rem] text-ink-900">{{ $course->price_label }}</p>
                            @if ($course->compare_at_price && $course->compare_at_price > $course->price)
                                <p class="text-sm text-ink-400 line-through">
                                    {{ config('academy.currency_symbol') }}{{ number_format((float) $course->compare_at_price, 0) }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-5 space-y-3">
                            @if ($hasAccess)
                                <x-ui.button :href="route('learn.course', $course)" size="lg" class="w-full">
                                    Continue learning
                                </x-ui.button>
                                <p class="text-center text-[0.8125rem] text-ink-500">
                                    You are enrolled — {{ $enrollment?->progress_percent ?? 0 }}% complete
                                </p>
                            @elseif (auth()->check())
                                <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="lg" class="w-full">
                                        {{ $course->isFree() ? 'Enrol for free' : ($paymentsOnline ? 'Enrol now' : 'Request enrolment') }}
                                    </x-ui.button>
                                </form>

                                @if (! $course->isFree() && ! $paymentsOnline)
                                    <p class="text-center text-[0.8125rem] text-ink-500">
                                        Our team will contact you to arrange payment and open the course.
                                    </p>
                                @endif
                            @else
                                <x-ui.button :href="route('login')" size="lg" class="w-full">
                                    Sign in to enrol
                                </x-ui.button>
                                <p class="text-center text-[0.8125rem] text-ink-500">
                                    New here?
                                    <a href="{{ route('register') }}" class="text-pine-700 hover:underline">Create an account</a>
                                </p>
                            @endif
                        </div>

                        <ul class="mt-6 space-y-2.5 border-t border-ink-200 pt-6 text-sm text-ink-700">
                            <li class="flex gap-2.5">
                                <x-ui.icon name="play-circle" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                {{ $lessonCount }} recorded lessons, yours for the life of the enrolment
                            </li>
                            @if ($liveClassCount > 0)
                                <li class="flex gap-2.5">
                                    <x-ui.icon name="broadcast" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                    {{ $liveClassCount }} live {{ str('class')->plural($liveClassCount) }} with your instructor
                                </li>
                            @endif
                            @if ($resourceCount > 0)
                                <li class="flex gap-2.5">
                                    <x-ui.icon name="download" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                    {{ $resourceCount }} downloadable {{ str('resource')->plural($resourceCount) }}
                                </li>
                            @endif
                            @if ($course->certificate_enabled)
                                <li class="flex gap-2.5">
                                    <x-ui.icon name="award" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                    Verifiable certificate on completion
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- What you will learn --}}
    @if (! empty($course->outcomes))
        <section class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-16">
            <h2 class="text-[1.5rem] leading-tight">What you will be able to do</h2>
            <ul class="mt-8 grid gap-x-10 gap-y-4 sm:grid-cols-2">
                @foreach ($course->outcomes as $outcome)
                    <li class="flex gap-3 text-[0.9375rem]/7 text-ink-700">
                        <x-ui.icon name="check" class="mt-1.5 size-4 shrink-0 text-pine-600" />
                        {{ $outcome }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Overview, audience and requirements --}}
    <section class="border-t border-ink-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 lg:grid-cols-12 lg:gap-14 lg:px-8 lg:py-16">
            <div class="lg:col-span-7">
                <h2 class="text-[1.5rem] leading-tight">About this course</h2>
                <div class="prose-academy mt-6">
                    @foreach (preg_split('/\r\n\r\n|\n\n/', (string) $course->description) as $paragraph)
                        @if (trim($paragraph) !== '')
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="space-y-10 lg:col-span-5">
                @if (! empty($course->audience))
                    <div>
                        <h3 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Who it is for</h3>
                        <ul class="mt-4 space-y-2.5 border-t border-ink-200 pt-4">
                            @foreach ($course->audience as $item)
                                <li class="text-[0.9375rem]/7 text-ink-700">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($course->requirements))
                    <div>
                        <h3 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">What you need</h3>
                        <ul class="mt-4 space-y-2.5 border-t border-ink-200 pt-4">
                            @foreach ($course->requirements as $item)
                                <li class="text-[0.9375rem]/7 text-ink-700">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Curriculum --}}
    <section class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-16">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-[1.5rem] leading-tight">Curriculum</h2>
                <p class="mt-2 text-sm text-ink-600">
                    {{ $curriculum->count() }} {{ str('module')->plural($curriculum->count()) }} ·
                    {{ $lessonCount }} {{ str('lesson')->plural($lessonCount) }} ·
                    {{ $course->duration_label }}
                </p>
            </div>
            @unless ($hasAccess)
                <p class="text-[0.8125rem] text-ink-500">Lessons marked <span class="text-pine-700">Preview</span> are free to watch.</p>
            @endunless
        </div>

        <div class="mt-8">
            @if ($curriculum->isEmpty())
                <x-ui.empty-state title="The curriculum is being finalised"
                                  description="This course is published but its lessons are still being uploaded." />
            @else
                <x-site.curriculum :course="$course" :modules="$curriculum" :has-access="$hasAccess" />
            @endif
        </div>
    </section>

    {{-- Instructor --}}
    @if ($course->instructor)
        <section class="border-y border-ink-200 bg-white">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 lg:grid-cols-12 lg:gap-14 lg:px-8 lg:py-16">
                <div class="lg:col-span-4">
                    @if ($course->instructor->avatar_url)
                        <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->name }}"
                             class="aspect-square w-full max-w-xs object-cover" loading="lazy">
                    @else
                        <div class="flex aspect-square w-full max-w-xs items-center justify-center bg-ink-900">
                            <span class="font-serif text-5xl text-sand-200">{{ str($course->instructor->name)->substr(0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-8">
                    <p class="text-[0.6875rem] font-medium tracking-[0.14em] text-pine-700 uppercase">Your instructor</p>
                    <h2 class="mt-4 text-[1.5rem] leading-tight">{{ $course->instructor->name }}</h2>
                    @if ($course->instructor->headline)
                        <p class="mt-2 text-[0.9375rem] text-ink-600">{{ $course->instructor->headline }}</p>
                    @endif

                    @if ($course->instructor->bio)
                        <div class="prose-academy mt-5 max-w-2xl">
                            @foreach (preg_split('/\r\n\r\n|\n\n/', $course->instructor->bio) as $paragraph)
                                @if (trim($paragraph) !== '')
                                    <p>{{ trim($paragraph) }}</p>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($course->instructor->credentials))
                        <ul class="mt-7 grid gap-x-8 gap-y-2 sm:grid-cols-2">
                            @foreach ($course->instructor->credentials as $credential)
                                <li class="flex gap-2.5 text-sm text-ink-700">
                                    <x-ui.icon name="check" class="mt-1 size-3.5 shrink-0 text-pine-600" />
                                    {{ $credential }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if ($course->testimonials->isNotEmpty())
        <section class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-16">
            <h2 class="text-[1.5rem] leading-tight">From students on this course</h2>
            <div class="mt-10 grid gap-10 lg:grid-cols-3">
                @foreach ($course->testimonials as $testimonial)
                    <x-site.testimonial :testimonial="$testimonial" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if ($faqs->isNotEmpty())
        <section class="border-t border-ink-200 bg-white">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 lg:grid-cols-12 lg:gap-14 lg:px-8 lg:py-16">
                <div class="lg:col-span-4">
                    <h2 class="text-[1.5rem] leading-tight">Common questions</h2>
                    <p class="mt-4 text-sm/6 text-ink-600">
                        Anything else,
                        <a href="{{ route('contact') }}" class="text-pine-700 underline underline-offset-2">just ask</a>.
                    </p>
                </div>
                <div class="lg:col-span-8">
                    <x-ui.accordion :items="$faqs->map(fn ($faq) => ['question' => $faq->question, 'answer' => $faq->answer])->all()" :open="0" />
                </div>
            </div>
        </section>
    @endif

    {{-- Closing enrolment prompt --}}
    <section class="bg-ink-950">
        <div class="mx-auto flex max-w-6xl flex-wrap items-end justify-between gap-8 px-5 py-14 lg:px-8 lg:py-16">
            <div class="max-w-xl">
                <h2 class="text-[1.75rem] leading-tight text-white">{{ $course->title }}</h2>
                <p class="mt-3 text-[0.975rem]/7 text-white/60">
                    {{ $lessonCount }} lessons, {{ $course->duration_label }} of instruction
                    @if ($liveClassCount > 0), plus {{ $liveClassCount }} live {{ str('session')->plural($liveClassCount) }}@endif.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <p class="font-serif text-2xl text-white">{{ $course->price_label }}</p>
                @if ($hasAccess)
                    <x-ui.button :href="route('learn.course', $course)" variant="light" size="lg">Continue learning</x-ui.button>
                @elseif (auth()->check())
                    <form method="POST" action="{{ route('courses.enroll', $course) }}">
                        @csrf
                        <x-ui.button type="submit" variant="light" size="lg">
                            {{ $course->isFree() ? 'Enrol for free' : ($paymentsOnline ? 'Enrol now' : 'Request enrolment') }}
                        </x-ui.button>
                    </form>
                @else
                    <x-ui.button :href="route('login')" variant="light" size="lg">Sign in to enrol</x-ui.button>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
