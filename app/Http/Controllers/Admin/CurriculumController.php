<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The curriculum builder: modules, their lessons and the ordering controls.
 */
class CurriculumController extends Controller
{
    public function __invoke(Course $course): View
    {
        Gate::authorize('update', $course);

        return view('admin.courses.curriculum', [
            'course' => $course,
            // Drafts are included here; the public curriculum hides them.
            'modules' => $course->curriculum(publishedLessonsOnly: false),
        ]);
    }
}
