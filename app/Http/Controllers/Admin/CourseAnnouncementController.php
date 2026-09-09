<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseAnnouncementRequest;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Services\AdminActivityLogger;
use App\Services\NotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Announcements (§12), sitewide or scoped to one course. Posting one both
 * lists it for students and pushes a notification to its audience.
 */
class CourseAnnouncementController extends Controller
{
    public function index(): View
    {
        return view('admin.announcements.index', [
            'announcements' => CourseAnnouncement::query()->with('course')->latest('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'announcement' => new CourseAnnouncement,
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    public function store(
        CourseAnnouncementRequest $request,
        NotificationDispatcher $notifications,
        AdminActivityLogger $activity,
    ): RedirectResponse {
        $announcement = CourseAnnouncement::create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        $notifications->announcementPosted($announcement);
        $activity->log('announcement.created', $announcement, "Posted announcement \"{$announcement->title}\"");

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement posted.');
    }

    public function edit(CourseAnnouncement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    public function update(CourseAnnouncementRequest $request, CourseAnnouncement $announcement): RedirectResponse
    {
        $announcement->update($request->validated());

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement updated.');
    }

    public function destroy(CourseAnnouncement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement removed.');
    }
}
