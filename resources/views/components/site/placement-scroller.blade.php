@php
    // Swap these files (public/client-logo/) for verified partner logos and
    // real placement data before this claim goes live.
    $logos = [
        ['file' => 'imgi_19_cocacola-corp.png', 'name' => 'Coca-Cola'],
        ['file' => 'imgi_26_walmart-corp.png', 'name' => 'Walmart'],
        ['file' => 'imgi_25_honeywell-corp.png', 'name' => 'Honeywell'],
        ['file' => 'imgi_22_wipro_new-corp.png', 'name' => 'Wipro'],
        ['file' => 'imgi_20_visa-corp.png', 'name' => 'Visa'],
        ['file' => 'imgi_21_cisco-corp.png', 'name' => 'Cisco'],
        ['file' => 'imgi_24_city-corp.png', 'name' => 'Citi'],
        ['file' => 'imgi_23_compdd.png', 'name' => 'Dell'],
        ['file' => 'imgi_8_pgp_ai_ml_logo.webp', 'name' => 'Illinois Tech'],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
    <p class="text-center text-[0.6875rem] font-medium tracking-[0.16em] text-ink-500 uppercase">
        Where our students go on to work
    </p>

    <div class="relative mt-7 [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
        <div class="marquee-track flex w-max items-center gap-16">
            @for ($i = 0; $i < 2; $i++)
                <div class="flex shrink-0 items-center gap-16" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
                    @foreach ($logos as $logo)
                        <img src="{{ asset('client-logo/'.$logo['file']) }}" alt="{{ $logo['name'] }}"
                             class="h-8 w-auto shrink-0 object-contain grayscale transition-all duration-200 hover:grayscale-0"
                             loading="lazy">
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</div>
