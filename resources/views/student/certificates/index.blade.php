<x-layouts.app title="Certificates" heading="Certificates"
               description="Issued automatically when you complete a course.">

    @if ($certificates->isEmpty())
        <x-ui.empty-state icon="award" title="No certificates yet"
                          description="Finish every lesson in a course and your certificate is issued straight away.">
            <x-ui.button :href="route('my-courses')">Back to my courses</x-ui.button>
        </x-ui.empty-state>
    @else
        <ul class="divide-y divide-ink-200 border border-ink-200 bg-white">
            @foreach ($certificates as $certificate)
                <li class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div class="min-w-0">
                        <h2 class="text-[1.0625rem] leading-snug">{{ $certificate->course_title }}</h2>
                        <p class="mt-1 text-[0.8125rem] text-ink-500">
                            <span class="font-mono">{{ $certificate->certificate_number }}</span>
                            · Completed {{ $certificate->completed_on->format('j F Y') }}
                        </p>
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <x-ui.button :href="route('certificates.show', $certificate)" variant="outline" size="sm">
                            View
                        </x-ui.button>
                        <x-ui.button :href="route('certificates.print', $certificate)" size="sm" target="_blank" rel="noopener">
                            Print
                        </x-ui.button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
