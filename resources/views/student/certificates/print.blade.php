<x-layouts.print :title="'Certificate '.$certificate->certificate_number">
    <div class="mx-auto max-w-[1123px] p-4 print:max-w-none print:p-0">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 print:hidden">
            <a href="{{ route('certificates.show', $certificate) }}"
               class="inline-flex items-center gap-1.5 text-[0.8125rem] text-ink-600 hover:text-ink-900">
                <x-ui.icon name="arrow-left" class="size-3.5" />
                Back
            </a>
            <button type="button" onclick="window.print()"
                    class="bg-pine-700 px-4 py-2 text-sm font-medium text-white hover:bg-pine-800">
                Print or save as PDF
            </button>
        </div>

        <div style="container-type: inline-size;">
            <x-site.certificate :certificate="$certificate" class="shadow-sm print:shadow-none" />
        </div>

        <p class="mt-4 text-center text-[0.75rem] text-ink-500 print:mt-2">
            Verify at {{ route('certificates.verify', $certificate) }}
        </p>
    </div>
</x-layouts.print>
