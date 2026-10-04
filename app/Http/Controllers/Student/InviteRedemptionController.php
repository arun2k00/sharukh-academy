<?php

namespace App\Http\Controllers\Student;

use App\Enums\EnrollmentSource;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\EnrollmentService;
use App\Services\InviteCodeInvalid;
use App\Services\InviteCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class InviteRedemptionController extends Controller
{
    public function __invoke(
        Request $request,
        Course $course,
        InviteCodeService $inviteCodes,
        EnrollmentService $enrollments,
    ): RedirectResponse {
        Gate::authorize('enroll', $course);

        $validated = $request->validate(['code' => ['required', 'string', 'max:20']]);

        try {
            $inviteCode = $inviteCodes->redeem($validated['code'], $course);
        } catch (InviteCodeInvalid $e) {
            throw ValidationException::withMessages(['code' => $e->getMessage()]);
        }

        $enrollments->enroll($request->user(), $course, EnrollmentSource::Invite, inviteCode: $inviteCode);

        return redirect()->route('learn.course', $course)
            ->with('status', 'Code accepted — you are enrolled. Enjoy the course.');
    }
}
