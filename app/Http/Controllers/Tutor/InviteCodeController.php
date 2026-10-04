<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\InviteCodeRequest;
use App\Models\Course;
use App\Models\InviteCode;
use App\Services\InviteCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InviteCodeController extends Controller
{
    public function index(Course $course): View
    {
        Gate::authorize('update', $course);

        return view('tutor.courses.invite-codes', [
            'course' => $course,
            'inviteCodes' => $course->inviteCodes()->latest()->get(),
        ]);
    }

    public function store(InviteCodeRequest $request, Course $course, InviteCodeService $inviteCodes): RedirectResponse
    {
        $inviteCodes->generate(
            $course,
            $request->user(),
            $request->integer('max_uses') ?: null,
            $request->filled('expires_at') ? $request->date('expires_at') : null,
        );

        return redirect()->route('tutor.courses.invite-codes.index', $course)
            ->with('status', 'Invite code generated.');
    }

    public function destroy(InviteCode $inviteCode, InviteCodeService $inviteCodes): RedirectResponse
    {
        $course = $inviteCode->loadMissing('course')->course;

        Gate::authorize('update', $course);

        $inviteCodes->revoke($inviteCode);

        return redirect()->route('tutor.courses.invite-codes.index', $course)
            ->with('status', 'Invite code revoked.');
    }
}
