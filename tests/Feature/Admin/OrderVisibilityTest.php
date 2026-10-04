<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_sees_all_orders(): void
    {
        Order::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index'))
            ->assertOk();
    }

    public function test_a_non_admin_cannot_view_orders(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_filtering_by_status_only_shows_matching_orders(): void
    {
        $paid = Order::factory()->paid()->create();
        $pending = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['status' => 'paid']))
            ->assertOk()
            ->assertSee($paid->order_number)
            ->assertDontSee($pending->order_number);
    }

    public function test_a_student_may_view_only_their_own_order_receipt(): void
    {
        $order = Order::factory()->create();
        $stranger = $this->student();

        $this->actingAs($order->user)
            ->get(route('payments.razorpay.callback', $order))
            ->assertRedirect();

        $this->actingAs($stranger)
            ->get(route('payments.razorpay.callback', $order))
            ->assertForbidden();
    }

    public function test_an_admin_can_refund_a_paid_order_via_the_manual_gateway(): void
    {
        $order = Order::factory()->paid()->create();
        Payment::factory()->create([
            'order_id' => $order->id,
            'gateway' => 'manual',
            'status' => PaymentStatus::Successful,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.orders.refund', $order))
            ->assertRedirect();

        $this->assertSame(OrderStatus::Refunded, $order->fresh()->status);
    }

    public function test_an_order_with_no_successful_payment_cannot_be_refunded(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($this->admin())
            ->post(route('admin.orders.refund', $order))
            ->assertForbidden();
    }
}
