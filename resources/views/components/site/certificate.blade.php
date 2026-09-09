@props(['certificate'])

{{-- The certificate artwork itself, shared by the on-screen and print views.
     Sizing is in container-query units so it scales with whatever box it is
     placed in — the wrapper must set `container-type: inline-size`. --}}
<div {{ $attributes->merge(['class' => 'relative aspect-[297/210] w-full bg-white']) }}>
    <div class="absolute inset-[3%] border border-ink-300"></div>
    <div class="absolute inset-[3.9%] border border-ink-200"></div>

    <div class="absolute inset-[7.5%] flex flex-col items-center justify-between text-center">
        <div class="flex flex-col items-center">
            <div class="flex items-center gap-[1cqw]">
                <span class="flex aspect-square w-[4cqw] items-center justify-center border border-pine-700 font-serif text-[2cqw] text-pine-700">O</span>
                <span class="text-left leading-none">
                    <span class="block font-serif text-[2.4cqw] text-ink-900">Oacer</span>
                    <span class="mt-[0.3cqw] block text-[1cqw] font-medium tracking-[0.22em] text-ink-500 uppercase">Academy</span>
                </span>
            </div>

            <p class="mt-[3cqw] text-[1.15cqw] font-medium tracking-[0.3em] text-ink-500 uppercase">
                Certificate of completion
            </p>
        </div>

        <div class="flex flex-col items-center">
            <p class="text-[1.4cqw] text-ink-500">This is to certify that</p>

            <p class="mt-[1cqw] font-serif text-[5cqw] leading-tight text-ink-950">
                {{ $certificate->student_name }}
            </p>

            <div class="mt-[1.5cqw] h-px w-[45cqw] bg-ink-300"></div>

            <p class="mt-[2cqw] text-[1.4cqw] text-ink-500">has successfully completed</p>

            <p class="mt-[1cqw] max-w-[75cqw] font-serif text-[2.8cqw] leading-snug text-ink-900">
                {{ $certificate->course_title }}
            </p>
        </div>

        <div class="flex w-full items-end justify-between gap-[4cqw] text-left">
            <div>
                <p class="text-[0.95cqw] tracking-[0.12em] text-ink-400 uppercase">Completed</p>
                <p class="mt-[0.6cqw] text-[1.3cqw] text-ink-800">{{ $certificate->completed_on->format('j F Y') }}</p>
            </div>

            @if ($certificate->instructor_name)
                <div class="text-center">
                    <p class="font-serif text-[1.6cqw] text-ink-800 italic">{{ $certificate->instructor_name }}</p>
                    <div class="mx-auto mt-[0.6cqw] h-px w-[18cqw] bg-ink-300"></div>
                    <p class="mt-[0.6cqw] text-[0.95cqw] tracking-[0.12em] text-ink-400 uppercase">Instructor</p>
                </div>
            @endif

            <div class="text-right">
                <p class="text-[0.95cqw] tracking-[0.12em] text-ink-400 uppercase">Certificate no.</p>
                <p class="mt-[0.6cqw] font-mono text-[1.3cqw] text-ink-800">{{ $certificate->certificate_number }}</p>
            </div>
        </div>
    </div>
</div>
