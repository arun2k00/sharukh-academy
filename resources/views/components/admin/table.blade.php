@props(['headings' => []])

{{-- Tables scroll inside their own container so the page never scrolls sideways. --}}
<div class="overflow-x-auto border border-ink-200 bg-white">
    <table class="w-full min-w-[38rem] text-left text-sm">
        @if ($headings !== [])
            <thead class="border-b border-ink-200 bg-sand-100">
                <tr>
                    @foreach ($headings as $heading)
                        <th scope="col"
                            class="px-4 py-2.5 text-[0.6875rem] font-medium tracking-[0.08em] text-ink-500 uppercase {{ is_array($heading) ? ($heading['class'] ?? '') : '' }}">
                            {{ is_array($heading) ? $heading['label'] : $heading }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-ink-200 [&>tr]:transition-colors [&>tr]:hover:bg-sand-50">
            {{ $slot }}
        </tbody>
    </table>
</div>
