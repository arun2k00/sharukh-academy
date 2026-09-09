@props(['certificate', 'imageRoute'])

{{-- Uses the course's uploaded certificate artwork when one is configured;
     falls back to the plain printable design otherwise (§1). Both are
     legitimate, finished certificates — never a placeholder. The caller
     supplies its own `container-type: inline-size` wrapper, since the plain
     design scales itself with container-query units. --}}
@if ($certificate->course->certificateTemplate)
    <img src="{{ $imageRoute }}"
         alt="Certificate awarded to {{ $certificate->student_name }} for {{ $certificate->course_title }}"
         {{ $attributes->merge(['class' => 'w-full']) }}>
@else
    <x-site.certificate :certificate="$certificate" {{ $attributes }} />
@endif
