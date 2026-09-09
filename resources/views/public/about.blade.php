<x-layouts.public title="About" description="How Edunova teaches, and who teaches here.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">About the academy</p>
            <h1 class="mt-5 max-w-3xl text-[2rem] leading-[1.15] sm:text-[2.75rem]">
                A short catalogue, taught properly, by people who still do the work.
            </h1>
            <p class="mt-6 max-w-2xl text-[1.0625rem]/8 text-ink-600">
                We are not a marketplace. There is no algorithm deciding what gets promoted and no incentive to
                publish a hundred courses of uneven quality. We run a handful of programmes, and we are responsible
                for every one of them.
            </p>
        </div>
    </section>

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

    <section class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-4">
                <h2 class="text-[1.5rem] leading-tight">How we teach</h2>
            </div>
            <div class="grid gap-y-10 lg:col-span-8">
                @foreach ([
                    ['Recorded first, live second', 'The core of every programme is recorded so you can work at your own pace and revisit anything. Live sessions exist for the parts that need a conversation — critique, judgement calls, and the questions you only think of once you have tried it.'],
                    ['Worksheets, not homework', 'Each module produces something you keep: a model, a brief, a plan. If a lesson does not change what you do on Monday, it should not be in the course.'],
                    ['Instructors who are accountable', 'Every programme has one named instructor who wrote it, teaches the live sessions and answers the questions. Nobody hands a course off to a moderator.'],
                    ['Certification that means something', 'Certificates are issued on completion of the whole curriculum, and every one carries a public verification link so it can be checked by anyone you show it to.'],
                ] as $item)
                    <div class="border-t border-ink-900 pt-5">
                        <h3 class="font-sans text-[1.0625rem] font-semibold text-ink-900">{{ $item[0] }}</h3>
                        <p class="mt-2.5 max-w-2xl text-[0.9375rem]/7 text-ink-600">{{ $item[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($instructors->isNotEmpty())
        <section class="border-y border-ink-200 bg-white">
            <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
                <x-ui.section-heading eyebrow="The faculty" title="Who teaches here" />

                <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($instructors as $instructor)
                        <article>
                            @if ($instructor->avatar_url)
                                <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}"
                                     class="aspect-[4/5] w-full object-cover" loading="lazy">
                            @else
                                <div class="flex aspect-[4/5] w-full items-center justify-center bg-ink-900">
                                    <span class="font-serif text-4xl text-sand-200">{{ str($instructor->name)->substr(0, 1) }}</span>
                                </div>
                            @endif

                            <h3 class="mt-5 text-lg">{{ $instructor->name }}</h3>
                            @if ($instructor->headline)
                                <p class="mt-1 text-[0.8125rem] text-ink-500">{{ $instructor->headline }}</p>
                            @endif
                            <p class="mt-3 text-sm/6 text-ink-600">
                                {{ \Illuminate\Support\Str::limit(strip_tags((string) $instructor->bio), 180) }}
                            </p>
                            <p class="mt-3 text-[0.8125rem] text-ink-500">
                                {{ $instructor->courses_count }} {{ str('course')->plural($instructor->courses_count) }} ·
                                {{ $instructor->experience_years }} years in practice
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        <section class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <x-ui.section-heading eyebrow="From our students" title="What people say" />
            <div class="mt-12 grid gap-10 sm:grid-cols-2">
                @foreach ($testimonials as $testimonial)
                    <x-site.testimonial :testimonial="$testimonial" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-ink-950">
        <div class="mx-auto flex max-w-6xl flex-wrap items-end justify-between gap-8 px-5 py-16 lg:px-8">
            <h2 class="max-w-xl text-[1.75rem] leading-tight text-white">
                Have a look at what we run.
            </h2>
            <x-ui.button :href="route('courses.index')" variant="light" size="lg">Browse the catalogue</x-ui.button>
        </div>
    </section>
</x-layouts.public>
