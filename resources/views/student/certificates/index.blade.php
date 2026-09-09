<x-layouts.app title="Certificates" heading="Certificates"
               description="Issued automatically when you complete a course.">

    @if ($certificates->isEmpty())
        <x-ui.empty-state icon="award" title="No certificates yet"
                          description="Finish every lesson in a course and your certificate is issued straight away.">
            <x-ui.button :href="route('my-courses')">Back to my courses</x-ui.button>
        </x-ui.empty-state>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($certificates as $certificate)
                <article class="border border-ink-200 bg-white">
                    <a href="{{ route('certificates.show', $certificate) }}" class="block bg-sand-100 p-4" style="container-type: inline-size;">
                        <x-site.certificate-display :certificate="$certificate" :image-route="route('certificates.image', $certificate)"
                                                    class="border border-ink-200 shadow-sm" />
                    </a>
                    <div class="p-5">
                        <h2 class="text-[1.0625rem] leading-snug">{{ $certificate->course_title }}</h2>
                        <p class="mt-1 text-[0.8125rem] text-ink-500">
                            <span class="font-mono">{{ $certificate->certificate_number }}</span>
                            · Completed {{ $certificate->completed_on->format('j F Y') }}
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <x-ui.button :href="route('certificates.show', $certificate)" variant="outline" size="sm">
                                View
                            </x-ui.button>
                            @if ($certificate->course->certificateTemplate)
                                <x-ui.button :href="route('certificates.image', ['certificate' => $certificate, 'download' => 1])" size="sm" icon="download">
                                    Download
                                </x-ui.button>
                            @else
                                <x-ui.button :href="route('certificates.print', $certificate)" size="sm" target="_blank" rel="noopener">
                                    Print
                                </x-ui.button>
                            @endif
                            <x-ui.share-menu :url="route('certificates.verify', $certificate)" :title="'My certificate — '.$certificate->course_title" label="Share" />
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.app>
