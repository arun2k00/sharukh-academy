<?php

namespace App\Http\Controllers\Student;

use App\Enums\EnrollmentSource;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\EnrollmentService;
use App\Services\OrderService;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Self-service enrolment.
 *
 * Free courses open immediately. Paid courses create a pending order and hand
 * off to the configured gateway — with the default manual gateway that means an
 * administrator confirms the enrolment, and no access is granted here.
 */
class EnrollmentController extends Controller
{
    public function __invoke(
        Request $request,
        Course $course,
        EnrollmentService $enrollments,
        OrderService $orders,
        PaymentManager $payments,
    ): RedirectResponse {
        Gate::authorize('enroll', $course);

        $user = $request->user();

        if ($course->isFree()) {
            $enrollments->enroll($user, $course, EnrollmentSource::Free);

            return redirect()->route('learn.course', $course)
                ->with('status', 'You are enrolled. Enjoy the course.');
        }

        $order = $orders->openFor($user, $course);
        $session = $payments->driver()->createCheckout($order);

        if ($session->redirectUrl !== null) {
            return redirect()->away($session->redirectUrl);
        }

        return redirect()->route('courses.show', $course)->with('status', $session->message);
    }
}
