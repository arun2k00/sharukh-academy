<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayUnavailable;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class OrderRefundController extends Controller
{
    public function __invoke(Order $order, PaymentManager $payments): RedirectResponse
    {
        Gate::authorize('refund', $order);

        $payment = $order->payments()->where('status', 'successful')->latest('id')->first();

        if ($payment === null) {
            return back()->with('status', 'This order has no successful payment to refund.');
        }

        try {
            $payments->driver($payment->gateway)->refund($payment);
        } catch (PaymentGatewayUnavailable $e) {
            return back()->with('status', 'Refund failed: '.$e->getMessage());
        }

        return back()->with('status', 'Payment refunded.');
    }
}
