<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Review moderation (§22): approve, hide, or remove.
 */
class CourseReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = CourseReview::query()
            ->when($request->integer('course'), fn ($query, $courseId) => $query->where('course_id', $courseId))
            ->with(['user', 'course'])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    public function toggle(CourseReview $review, AdminActivityLogger $activity): RedirectResponse
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        $activity->log('review.toggled', $review, ($review->is_approved ? 'Approved' : 'Hid').' a review on '.$review->course->title);

        return back()->with('status', $review->is_approved ? 'Review approved.' : 'Review hidden.');
    }

    public function destroy(CourseReview $review, AdminActivityLogger $activity): RedirectResponse
    {
        $activity->log('review.deleted', null, "Deleted a review on {$review->course->title}");

        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
