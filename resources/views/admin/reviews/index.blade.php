<x-layouts.admin title="Reviews" heading="Reviews">
    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-ui.field label="Course" for="course">
                <x-ui.select id="course" name="course">
                    <option value="">All courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((int) request('course') === $course->id)>{{ $course->title }}</option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
        @if (request('course'))
            <a href="{{ route('admin.reviews.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($reviews->isEmpty())
        <x-ui.empty-state icon="star" title="No reviews yet" description="Reviews from enrolled students appear here for moderation." />
    @else
        <x-admin.table :headings="['Student', 'Course', 'Rating', 'Review', 'Status', ['label' => '', 'class' => 'text-right']]">
            @foreach ($reviews as $review)
                <tr>
                    <td class="px-4 py-3 text-ink-900">{{ $review->user->name }}</td>
                    <td class="px-4 py-3 text-ink-600">{{ $review->course->title }}</td>
                    <td class="px-4 py-3"><x-ui.rating :value="$review->rating" /></td>
                    <td class="max-w-sm px-4 py-3 text-[0.8125rem] text-ink-600">{{ \Illuminate\Support\Str::limit($review->review, 100) }}</td>
                    <td class="px-4 py-3">
                        @if ($review->is_approved)
                            <x-ui.badge class="bg-pine-50 text-pine-800">Visible</x-ui.badge>
                        @else
                            <x-ui.badge class="bg-sand-200 text-ink-600">Hidden</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-[0.8125rem] text-pine-700 hover:underline">
                                    {{ $review->is_approved ? 'Hide' : 'Approve' }}
                                </button>
                            </form>
                            <x-admin.delete-form :action="route('admin.reviews.destroy', $review)" confirm="Delete this review?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $reviews->links() }}</div>
    @endif
</x-layouts.admin>
