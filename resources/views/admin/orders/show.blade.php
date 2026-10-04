<x-layouts.admin :title="'Order '.$order->order_number" heading="Order">
    <x-admin.page-header :title="$order->order_number" :back="route('admin.orders.index')" back-label="Orders">
        <x-slot:actions>
            @can('refund', $order)
                <x-admin.delete-form :action="route('admin.orders.refund', $order)"
                                     confirm="Refund this payment? This calls the gateway directly and cannot be undone."
                                     label="Refund payment" />
            @endcan
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-8">
            <x-ui.panel title="Order details">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-ink-500">Student</dt>
                        <dd class="mt-1">
                            <a href="{{ route('admin.students.show', $order->user) }}" class="text-pine-700 hover:underline">
                                {{ $order->user->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Course</dt>
                        <dd class="mt-1">{{ $order->course->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Amount</dt>
                        <dd class="mt-1 tabular-nums">{{ config('academy.currency_symbol') }}{{ number_format((float) $order->amount, 2) }} {{ $order->currency }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Status</dt>
                        <dd class="mt-1"><x-ui.badge :class="$order->status->badgeClasses()">{{ $order->status->label() }}</x-ui.badge></dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Paid at</dt>
                        <dd class="mt-1">{{ $order->paid_at?->format('j M Y, g:ia') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-500">Enrolled via this order</dt>
                        <dd class="mt-1">{{ $order->enrollment ? 'Yes' : 'No' }}</dd>
                    </div>
                </dl>
            </x-ui.panel>

            <x-ui.panel title="Payment attempts">
                @if ($order->payments->isEmpty())
                    <p class="text-sm text-ink-500">No payment attempts recorded yet.</p>
                @else
                    <x-admin.table :headings="['Gateway', 'Gateway payment ID', 'Amount', 'Status', 'Paid at']">
                        @foreach ($order->payments as $payment)
                            <tr>
                                <td class="px-4 py-3 text-ink-900">{{ ucfirst($payment->gateway) }}</td>
                                <td class="px-4 py-3 font-mono text-[0.8125rem] text-ink-600">{{ $payment->gateway_payment_id ?? '—' }}</td>
                                <td class="px-4 py-3 tabular-nums">{{ config('academy.currency_symbol') }}{{ number_format((float) $payment->amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $payment->status->label() }}</td>
                                <td class="px-4 py-3 text-ink-600">{{ $payment->paid_at?->format('j M Y, g:ia') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </x-admin.table>
                @endif
            </x-ui.panel>
        </div>
    </div>
</x-layouts.admin>
