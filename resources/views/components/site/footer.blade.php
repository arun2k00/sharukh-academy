@props(['settings' => [], 'footerCourses' => []])

@php
    $email = $settings['contact_email'] ?? config('academy.support_email');
    $phone = $settings['contact_phone'] ?? config('academy.support_phone');
    $socials = array_filter([
        'LinkedIn' => $settings['social_linkedin'] ?? null,
        'YouTube' => $settings['social_youtube'] ?? null,
        'Instagram' => $settings['social_instagram'] ?? null,
        'X' => $settings['social_x'] ?? null,
    ]);
@endphp

<footer class="border-t border-ink-200 bg-white">
    <div class="mx-auto max-w-6xl px-5 py-14 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-site.logo />
                <p class="mt-4 max-w-xs text-sm/6 text-ink-600">
                    {{ $settings['footer_note'] ?? 'A focused online academy for practitioners who want depth, not a catalogue of shortcuts.' }}
                </p>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-[0.6875rem] font-medium tracking-[0.14em] text-ink-500 uppercase">Courses</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @forelse ($footerCourses as $course)
                        <li>
                            <a href="{{ route('courses.show', $course) }}" class="text-ink-700 hover:text-ink-950">{{ $course->title }}</a>
                        </li>
                    @empty
                        <li class="text-ink-500">Courses are being prepared.</li>
                    @endforelse
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-[0.6875rem] font-medium tracking-[0.14em] text-ink-500 uppercase">Academy</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="text-ink-700 hover:text-ink-950">About</a></li>
                    <li><a href="{{ route('courses.index') }}" class="text-ink-700 hover:text-ink-950">All courses</a></li>
                    <li><a href="{{ route('contact') }}" class="text-ink-700 hover:text-ink-950">Contact</a></li>
                    <li><a href="{{ route('login') }}" class="text-ink-700 hover:text-ink-950">Student sign in</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-[0.6875rem] font-medium tracking-[0.14em] text-ink-500 uppercase">Get in touch</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="mailto:{{ $email }}" class="text-ink-700 hover:text-ink-950">{{ $email }}</a></li>
                    <li><a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="text-ink-700 hover:text-ink-950">{{ $phone }}</a></li>
                    @if (! empty($settings['contact_address']))
                        <li class="text-ink-600">{{ $settings['contact_address'] }}</li>
                    @endif
                </ul>

                @if ($socials !== [])
                    <ul class="mt-5 flex flex-wrap gap-4 text-[0.8125rem]">
                        @foreach ($socials as $label => $url)
                            <li>
                                <a href="{{ $url }}" rel="noopener noreferrer" target="_blank"
                                   class="text-ink-500 underline-offset-4 hover:text-ink-900 hover:underline">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-2 border-t border-ink-200 pt-6 text-[0.8125rem] text-ink-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ config('academy.name') }}. All rights reserved.</p>
            <p>Verify a certificate at <span class="text-ink-700">{{ str(url('/verify-certificate'))->after('://') }}</span></p>
        </div>
    </div>
</footer>
