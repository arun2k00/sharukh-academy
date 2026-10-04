<?php

namespace App\Http\Controllers\Student;

use App\Enums\EnrollmentSource;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\EnrollmentService;
use App\Services\OrderService;
use App\Services\Payments\PaymentGatewayUnavailable;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Self-service enrolment.
 *
 * Free courses open immediately. Paid courses create a pending order and hand
 * off to a configured online gateway. If none is configured (or the one asked
 * for by name no longer is), this falls back to the manual gateway: an
 * administrator confirms the enrolment by hand, and no access is granted here.
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

        try {
            $gateway = $payments->driver($request->string('gateway')->toString() ?: null);
        } catch (PaymentGatewayUnavailable) {
            $gateway = $payments->driver('manual');
        }

        $session = $gateway->createCheckout($order);

        if ($session->redirectUrl !== null) {
            return redirect()->away($session->redirectUrl);
        }

        return redirect()->route('courses.show', $course)->with('status', $session->message);
    }
}
