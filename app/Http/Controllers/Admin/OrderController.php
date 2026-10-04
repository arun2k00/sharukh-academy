<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Order::class);

        $status = OrderStatus::tryFrom($request->string('status')->toString());
        $gateway = $request->string('gateway')->toString();

        $orders = Order::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($gateway !== '', fn ($query) => $query->whereHas(
                'payments',
                fn ($payments) => $payments->where('gateway', $gateway),
            ))
            ->when($request->filled('q'), fn ($query) => $query->whereHas(
                'user',
                fn ($user) => $user->search($request->string('q')->toString()),
            ))
            ->with(['user', 'course', 'payments'])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'activeStatus' => $status,
            'activeGateway' => $gateway,
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['user', 'course', 'payments', 'enrollment']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
