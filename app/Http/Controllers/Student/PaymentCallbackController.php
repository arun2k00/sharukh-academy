<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Where the gateway's hosted checkout page redirects the buyer back to.
 *
 * UX only: it picks a message from the order's current status. It never
 * grants access itself — the webhook is the only path that can do that, so
 * there is no race between a buyer's browser and the gateway's own callback.
 */
class PaymentCallbackController extends Controller
{
    public function __invoke(Order $order): RedirectResponse
    {
        Gate::authorize('view', $order);

        if ($order->isPaid()) {
            return redirect()->route('learn.course', $order->course)
                ->with('status', 'Payment received. You are enrolled — enjoy the course.');
        }

        return redirect()->route('courses.show', $order->course)->with('status', match ($order->status->value) {
            'failed' => 'That payment did not go through. You can try again below.',
            default => 'We are still confirming your payment. This can take a minute — refresh if it does not update.',
        });
    }
}
