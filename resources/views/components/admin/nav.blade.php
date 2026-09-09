@props(['unreadMessages' => 0])

@php
    $groups = [
        'Overview' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ],
        'Learning' => [
            ['label' => 'Courses', 'icon' => 'book-open', 'route' => 'admin.courses.index', 'pattern' => 'admin.courses.*'],
            ['label' => 'Live classes', 'icon' => 'broadcast', 'route' => 'admin.live-classes.index', 'pattern' => 'admin.live-classes.*'],
            ['label' => 'Certificates', 'icon' => 'award', 'route' => 'admin.certificates.index', 'pattern' => 'admin.certificates.*'],
        ],
        'People' => [
            ['label' => 'Students', 'icon' => 'users', 'route' => 'admin.students.index', 'pattern' => 'admin.students.*'],
            ['label' => 'Enrolments', 'icon' => 'graduation-cap', 'route' => 'admin.enrollments.index', 'pattern' => 'admin.enrollments.*'],
        ],
        'Website' => [
            ['label' => 'Instructors', 'icon' => 'user', 'route' => 'admin.instructors.index', 'pattern' => 'admin.instructors.*'],
            ['label' => 'Categories', 'icon' => 'tag', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*'],
            ['label' => 'Testimonials', 'icon' => 'star', 'route' => 'admin.testimonials.index', 'pattern' => 'admin.testimonials.*'],
            ['label' => 'FAQs', 'icon' => 'message', 'route' => 'admin.faqs.index', 'pattern' => 'admin.faqs.*'],
            ['label' => 'Messages', 'icon' => 'mail', 'route' => 'admin.messages.index', 'pattern' => 'admin.messages.*', 'badge' => $unreadMessages],
        ],
        'System' => [
            ['label' => 'Settings', 'icon' => 'sliders', 'route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*'],
        ],
    ];
@endphp

<nav class="space-y-6" aria-label="Administration">
    @foreach ($groups as $group => $items)
        <div>
            <p class="px-3 text-[0.625rem] font-medium tracking-[0.14em] text-ink-400 uppercase">{{ $group }}</p>
            <div class="mt-2 space-y-0.5">
                @foreach ($items as $item)
                    @php $active = request()->routeIs($item['pattern']); @endphp
                    <a href="{{ route($item['route']) }}"
                       @if($active) aria-current="page" @endif
                       class="flex items-center gap-3 px-3 py-2 text-sm {{ $active ? 'bg-white text-ink-900' : 'text-ink-300 hover:bg-ink-800 hover:text-white' }}">
                        <x-ui.icon :name="$item['icon']" class="size-[1.05rem] shrink-0" />
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if (! empty($item['badge']))
                            <span class="bg-brass-500 px-1.5 text-[0.6875rem] font-medium text-white tabular-nums">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
