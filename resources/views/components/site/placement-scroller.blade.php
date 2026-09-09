@php
    // Styled name badges rather than fetched company artwork — swap these
    // for real, verified partner logos before this claim goes live.
    $companies = [
        'Tata', 'TCS', 'Infosys', 'LTIMindtree', 'Capgemini', 'Mahindra',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
    <p class="text-center text-[0.6875rem] font-medium tracking-[0.16em] text-ink-500 uppercase">
        Where our students go on to work
    </p>

    <div class="relative mt-6 [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
        <div class="marquee-track flex w-max items-center gap-12">
            @for ($i = 0; $i < 2; $i++)
                <div class="flex shrink-0 items-center gap-12" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
                    @foreach ($companies as $company)
                        <span class="text-xl font-semibold tracking-tight text-ink-400 grayscale transition-all hover:text-ink-700 hover:grayscale-0">
                            {{ $company }}
                        </span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</div>
