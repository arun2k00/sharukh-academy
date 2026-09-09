<x-layouts.public title="Certificate verification"
                  :description="$certificate->student_name.' completed '.$certificate->course_title.' — verified certificate '.$certificate->certificate_number"
                  :og-image="$certificate->course->certificateTemplate ? route('certificates.verify-image', $certificate) : null">
    <div class="mx-auto max-w-3xl px-5 py-16 lg:px-8 lg:py-20">
        <x-ui.badge class="bg-pine-50 text-pine-700">Verified certificate</x-ui.badge>

        <h1 class="mt-5 text-[2rem] leading-tight">This certificate is genuine</h1>
        <p class="mt-4 text-[0.975rem]/7 text-ink-600">
            The record below was issued by {{ config('academy.name') }} and has not been altered.
        </p>

        @if ($certificate->course->certificateTemplate)
            <div class="mt-8 bg-sand-100 p-4">
                <x-site.certificate-display :certificate="$certificate" :image-route="route('certificates.verify-image', $certificate)"
                                            class="border border-ink-200 shadow-sm" />
            </div>
        @endif

        <dl class="mt-10 divide-y divide-ink-200 border-y border-ink-200">
            <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-6">
                <dt class="text-[0.8125rem] text-ink-500">Certificate number</dt>
                <dd class="font-mono text-[0.9375rem] text-ink-900 sm:col-span-2">{{ $certificate->certificate_number }}</dd>
            </div>
            <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-6">
                <dt class="text-[0.8125rem] text-ink-500">Awarded to</dt>
                <dd class="text-[0.9375rem] text-ink-900 sm:col-span-2">{{ $certificate->student_name }}</dd>
            </div>
            <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-6">
                <dt class="text-[0.8125rem] text-ink-500">Course</dt>
                <dd class="text-[0.9375rem] text-ink-900 sm:col-span-2">{{ $certificate->course_title }}</dd>
            </div>
            @if ($certificate->instructor_name)
                <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-6">
                    <dt class="text-[0.8125rem] text-ink-500">Instructor</dt>
                    <dd class="text-[0.9375rem] text-ink-900 sm:col-span-2">{{ $certificate->instructor_name }}</dd>
                </div>
            @endif
            <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-6">
                <dt class="text-[0.8125rem] text-ink-500">Completed on</dt>
                <dd class="text-[0.9375rem] text-ink-900 sm:col-span-2">{{ $certificate->completed_on->format('j F Y') }}</dd>
            </div>
            <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-6">
                <dt class="text-[0.8125rem] text-ink-500">Issued</dt>
                <dd class="text-[0.9375rem] text-ink-900 sm:col-span-2">{{ $certificate->issued_at->format('j F Y') }}</dd>
            </div>
        </dl>

        <p class="mt-8 text-[0.8125rem] text-ink-500">
            Questions about this certificate? Write to
            <a href="mailto:{{ config('academy.support_email') }}" class="text-pine-700 hover:underline">{{ config('academy.support_email') }}</a>.
        </p>
    </div>
</x-layouts.public>
