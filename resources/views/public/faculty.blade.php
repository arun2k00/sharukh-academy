@php
    $lead = $instructors->firstWhere('is_featured', true) ?? $instructors->first();
    $rest = $instructors->reject(fn ($instructor) => $lead && $instructor->is($lead));
@endphp

<x-layouts.public title="Our Faculty" description="The instructors who write, teach and answer questions on every Edunova programme.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Our faculty</p>
            <h1 class="mt-5 max-w-3xl text-[2rem] leading-[1.15] sm:text-[2.75rem]">
                Taught by people who build this for a living.
            </h1>
            <p class="mt-6 max-w-2xl text-[1.0625rem]/8 text-ink-600">
                Every programme has one named instructor who wrote it, teaches the live sessions and answers the
                questions — nobody hands a course off to a moderator.
            </p>
        </div>
    </section>

    @if ($lead)
        <section class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-4">
                    @if ($lead->avatar_url)
                        <img src="{{ $lead->avatar_url }}" alt="{{ $lead->name }}"
                             class="aspect-[4/5] w-full object-cover" loading="lazy">
                    @else
                        <div class="flex aspect-[4/5] w-full items-center justify-center bg-ink-900">
                            <span class="font-serif text-5xl text-sand-200">{{ str($lead->name)->substr(0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-8">
                    <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Lead instructor</p>
                    <h2 class="mt-4 text-[1.75rem] leading-tight">{{ $lead->name }}</h2>
                    @if ($lead->headline)
                        <p class="mt-2 text-[0.975rem] text-ink-600">{{ $lead->headline }}</p>
                    @endif

                    @if ($lead->bio)
                        <div class="prose-academy mt-6 max-w-2xl">
                            @foreach (preg_split('/\r\n|\r|\n/', $lead->bio) as $paragraph)
                                @if (trim($paragraph) !== '')
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($lead->credentials))
                        <ul class="mt-8 grid gap-x-8 gap-y-2.5 sm:grid-cols-2">
                            @foreach ($lead->credentials as $credential)
                                <li class="flex gap-2.5 text-sm text-ink-700">
                                    <x-ui.icon name="check" class="mt-1 size-3.5 shrink-0 text-pine-600" />
                                    {{ $credential }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-ink-200 pt-5 text-[0.8125rem] text-ink-500">
                        @if ($lead->experience_years)
                            <span>{{ $lead->experience_years }}+ years in practice</span>
                        @endif
                        <span>{{ $lead->courses_count }} {{ str('programme')->plural($lead->courses_count) }} taught</span>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($rest->isNotEmpty())
        <section class="border-t border-ink-200 bg-white">
            <div class="mx-auto max-w-6xl px-5 py-16 lg:px-8 lg:py-20">
                <x-ui.section-heading eyebrow="Also teaching here" title="The rest of the team" />

                <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rest as $instructor)
                        <article class="group">
                            @if ($instructor->avatar_url)
                                <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}"
                                     class="aspect-[4/5] w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" loading="lazy">
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
                                {{ $instructor->courses_count }} {{ str('course')->plural($instructor->courses_count) }}
                                @if ($instructor->experience_years)
                                    · {{ $instructor->experience_years }} years in practice
                                @endif
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="bg-ink-950">
        <div class="mx-auto flex max-w-6xl flex-wrap items-end justify-between gap-8 px-5 py-16 lg:px-8">
            <h2 class="max-w-xl text-[1.75rem] leading-tight text-white">
                See what they teach.
            </h2>
            <x-ui.button :href="route('courses.index')" variant="light" size="lg">Browse the catalogue</x-ui.button>
        </div>
    </section>
</x-layouts.public>
