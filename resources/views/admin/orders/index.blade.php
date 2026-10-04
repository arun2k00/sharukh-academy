<x-layouts.admin title="Orders" heading="Orders">
    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-ui.field label="Student" for="q">
                <x-ui.input id="q" name="q" value="{{ request('q') }}" placeholder="Name or email" />
            </x-ui.field>
        </div>
        <div class="w-full sm:w-44">
            <x-ui.field label="Status" for="status">
                <x-ui.select id="status" name="status">
                    <option value="">Any status</option>
                    @foreach (\App\Enums\OrderStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>
        <div class="w-full sm:w-44">
            <x-ui.field label="Gateway" for="gateway">
                <x-ui.select id="gateway" name="gateway">
                    <option value="">Any gateway</option>
                    <option value="manual" @selected(request('gateway') === 'manual')>Manual</option>
                    <option value="razorpay" @selected(request('gateway') === 'razorpay')>Razorpay</option>
                    <option value="phonepe" @selected(request('gateway') === 'phonepe')>PhonePe</option>
                </x-ui.select>
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
        @if (request()->hasAny(['q', 'status', 'gateway']))
            <a href="{{ route('admin.orders.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($orders->isEmpty())
        <x-ui.empty-state icon="tag" title="No orders match"
                          description="Orders are created automatically when a student starts checkout on a paid course." />
    @else
        <x-admin.table :headings="['Order', 'Student', 'Course', 'Amount', 'Gateway', 'Status', '']">
            @foreach ($orders as $order)
                <tr>
                    <td class="px-4 py-3 font-mono text-[0.8125rem] text-ink-700">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.students.show', $order->user) }}"
                           class="font-medium text-ink-900 hover:underline">{{ $order->user->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $order->course->title }}</td>
                    <td class="px-4 py-3 text-ink-900 tabular-nums">
                        {{ config('academy.currency_symbol') }}{{ number_format((float) $order->amount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-[0.8125rem] text-ink-500">
                        {{ $order->payments->last()?->gateway ? ucfirst($order->payments->last()->gateway) : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :class="$order->status->badgeClasses()">{{ $order->status->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-[0.8125rem] text-pine-700 hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $orders->links() }}</div>
    @endif
</x-layouts.admin>
