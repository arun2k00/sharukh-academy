@props(['unreadMessages' => 0, 'openSupportTickets' => 0, 'unansweredQuestions' => 0])

@php
    $groups = [
        'Overview' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
            ['label' => 'Activity log', 'icon' => 'list', 'route' => 'admin.activity-log.index', 'pattern' => 'admin.activity-log.*'],
        ],
        'Learning' => [
            ['label' => 'Courses', 'icon' => 'book-open', 'route' => 'admin.courses.index', 'pattern' => 'admin.courses.*'],
            ['label' => 'Live classes', 'icon' => 'broadcast', 'route' => 'admin.live-classes.index', 'pattern' => 'admin.live-classes.*'],
            ['label' => 'Certificates', 'icon' => 'award', 'route' => 'admin.certificates.index', 'pattern' => 'admin.certificates.index|admin.certificates.destroy'],
            ['label' => 'Certificate templates', 'icon' => 'sliders', 'route' => 'admin.certificate-templates.index', 'pattern' => 'admin.certificate-templates.*'],
            ['label' => 'Announcements', 'icon' => 'message', 'route' => 'admin.announcements.index', 'pattern' => 'admin.announcements.*'],
        ],
        'People' => [
            ['label' => 'Students', 'icon' => 'users', 'route' => 'admin.students.index', 'pattern' => 'admin.students.*'],
            ['label' => 'Enrolments', 'icon' => 'graduation-cap', 'route' => 'admin.enrollments.index', 'pattern' => 'admin.enrollments.*'],
        ],
        'Support' => [
            ['label' => 'Support tickets', 'icon' => 'message', 'route' => 'admin.support-tickets.index', 'pattern' => 'admin.support-tickets.*', 'badge' => $openSupportTickets],
            ['label' => 'Course questions', 'icon' => 'message', 'route' => 'admin.questions.index', 'pattern' => 'admin.questions.*', 'badge' => $unansweredQuestions],
            ['label' => 'Reviews', 'icon' => 'star', 'route' => 'admin.reviews.index', 'pattern' => 'admin.reviews.*'],
            ['label' => 'Messages', 'icon' => 'mail', 'route' => 'admin.messages.index', 'pattern' => 'admin.messages.*', 'badge' => $unreadMessages],
        ],
        'Website' => [
            ['label' => 'Instructors', 'icon' => 'user', 'route' => 'admin.instructors.index', 'pattern' => 'admin.instructors.*'],
            ['label' => 'Categories', 'icon' => 'tag', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*'],
            ['label' => 'Testimonials', 'icon' => 'star', 'route' => 'admin.testimonials.index', 'pattern' => 'admin.testimonials.*'],
            ['label' => 'FAQs', 'icon' => 'message', 'route' => 'admin.faqs.index', 'pattern' => 'admin.faqs.*'],
        ],
        'System' => [
            ['label' => 'Email templates', 'icon' => 'mail', 'route' => 'admin.email-templates.index', 'pattern' => 'admin.email-templates.*'],
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
                    @php $active = request()->routeIs(...explode('|', $item['pattern'])); @endphp
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
