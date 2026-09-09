<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseQuestionReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseQuestionReplyDestroyController extends Controller
{
    public function __invoke(Request $request, CourseQuestionReply $reply): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $reply->user_id === $user->id, 403);

        $reply->delete();

        return back()->with('status', 'Reply removed.');
    }
}
