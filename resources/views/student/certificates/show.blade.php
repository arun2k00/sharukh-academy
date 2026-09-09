<x-layouts.app :title="'Certificate — '.$certificate->course_title">
    <a href="{{ route('certificates.index') }}"
       class="mb-4 inline-flex items-center gap-1.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">
        <x-ui.icon name="arrow-left" class="size-3.5" />
        All certificates
    </a>

    <h1 class="text-[1.6rem] leading-tight">{{ $certificate->course_title }}</h1>
    <p class="mt-1.5 text-sm text-ink-600">
        Issued {{ $certificate->issued_at->format('j F Y') }}
    </p>

    <div class="mt-7 border border-ink-200 bg-white p-4 sm:p-8" style="container-type: inline-size;">
        <x-site.certificate :certificate="$certificate" />
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <x-ui.button :href="route('certificates.print', $certificate)" target="_blank" rel="noopener">
            Open printable version
        </x-ui.button>
        <x-ui.button :href="route('certificates.verify', $certificate)" variant="outline" target="_blank" rel="noopener"
                     icon-after="external-link">
            Public verification page
        </x-ui.button>
    </div>

    <p class="mt-5 max-w-2xl text-[0.8125rem]/6 text-ink-500">
        Share the verification link with an employer and they can confirm this certificate is genuine without
        needing an account. Use your browser's "Save as PDF" option on the printable version to keep a copy.
    </p>
</x-layouts.app>
