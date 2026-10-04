<?php

namespace App\Http\Controllers\Tutor;

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

        return view('tutor.courses.curriculum', [
            'course' => $course,
            'modules' => $course->curriculum(publishedLessonsOnly: false),
        ]);
    }
}
