<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "My Bookmarks" (§16): every lesson the student has flagged for later.
 */
class BookmarkListController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('student.bookmarks.index', [
            'bookmarks' => $request->user()->lessonBookmarks()
                ->with(['lesson.module', 'course'])
                ->latest('id')
                ->get(),
        ]);
    }
}
