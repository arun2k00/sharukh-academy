<?php

namespace Tests\Feature\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\AcademyNotification;
use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RazorpayWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret';

    private function configureWebhookSecret(): void
    {
        $settings = app(SettingsRepository::class);
        $settings->set('razorpay_webhook_secret', app(PaymentSettings::class)->encrypt(self::SECRET), group: 'payments');
    }

    /**
     * @return array{order: Order, payment: Payment, body: string, signature: string}
     */
    private function preparePendingPayment(int $amountInPaise = 799900): array
    {
        $course = Course::factory()->create(['price' => $amountInPaise / 100]);
        $student = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'amount' => $amountInPaise / 100,
            'status' => OrderStatus::Pending,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'razorpay',
            'gateway_order_id' => 'plink_test123',
            'amount' => $order->amount,
            'currency' => $order->currency,
            'status' => PaymentStatus::Pending,
        ]);

        $body = json_encode([
            'entity' => 'event',
            'event' => 'payment_link.paid',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test456',
                        'amount' => $amountInPaise,
                        'notes' => ['payment_id' => (string) $payment->id, 'order_id' => (string) $order->id],
                    ],
                ],
            ],
        ]);

        $signature = hash_hmac('sha256', $body, self::SECRET);

        return compact('order', 'payment', 'body', 'signature');
    }

    public function test_a_valid_signature_marks_the_payment_paid_and_enrolls_the_student(): void
    {
        Notification::fake();
        $this->configureWebhookSecret();

        ['order' => $order, 'payment' => $payment, 'body' => $body, 'signature' => $signature] = $this->preparePendingPayment();

        $this->call('POST', route('webhooks.razorpay'), [], [], [], [
            'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $body)->assertOk();

        $this->assertSame(PaymentStatus::Successful, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $order->user_id,
            'course_id' => $order->course_id,
            'source' => 'purchase',
        ]);

        Notification::assertSentTo($order->user, AcademyNotification::class);
    }

    public function test_the_same_webhook_delivered_twice_only_enrolls_once(): void
    {
        $this->configureWebhookSecret();

        ['body' => $body, 'signature' => $signature, 'order' => $order] = $this->preparePendingPayment();

        $headers = ['HTTP_X_RAZORPAY_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'];

        $this->call('POST', route('webhooks.razorpay'), [], [], [], $headers, $body)->assertOk();
        $this->call('POST', route('webhooks.razorpay'), [], [], [], $headers, $body)->assertOk();

        $this->assertDatabaseCount('payments', 1);
        $this->assertSame(1, Enrollment::where('user_id', $order->user_id)
            ->where('course_id', $order->course_id)
            ->count());
    }

    public function test_a_tampered_signature_is_rejected_and_writes_nothing(): void
    {
        $this->configureWebhookSecret();

        ['body' => $body, 'payment' => $payment, 'order' => $order] = $this->preparePendingPayment();

        $this->call('POST', route('webhooks.razorpay'), [], [], [], [
            'HTTP_X_RAZORPAY_SIGNATURE' => 'not-the-real-signature',
            'CONTENT_TYPE' => 'application/json',
        ], $body)->assertStatus(400);

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_webhook_for_an_unknown_payment_is_acknowledged_without_writing_anything(): void
    {
        $this->configureWebhookSecret();

        $body = json_encode([
            'event' => 'payment_link.paid',
            'payload' => ['payment' => ['entity' => ['id' => 'pay_ghost', 'amount' => 100, 'notes' => ['payment_id' => '999999']]]],
        ]);
        $signature = hash_hmac('sha256', $body, self::SECRET);

        $this->call('POST', route('webhooks.razorpay'), [], [], [], [
            'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $body)->assertOk();

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_reported_amount_that_does_not_match_the_order_is_never_marked_paid(): void
    {
        $this->configureWebhookSecret();

        $course = Course::factory()->create(['price' => 7999]);
        $student = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $student->id, 'course_id' => $course->id, 'amount' => 7999, 'status' => OrderStatus::Pending]);
        $payment = Payment::create([
            'order_id' => $order->id, 'gateway' => 'razorpay', 'gateway_order_id' => 'plink_x',
            'amount' => 7999, 'currency' => 'INR', 'status' => PaymentStatus::Pending,
        ]);

        // Reported amount (100 paise = ₹1) is far below the order's ₹7999.
        $body = json_encode([
            'event' => 'payment_link.paid',
            'payload' => ['payment' => ['entity' => ['id' => 'pay_low', 'amount' => 100, 'notes' => ['payment_id' => (string) $payment->id]]]],
        ]);
        $signature = hash_hmac('sha256', $body, self::SECRET);

        $this->call('POST', route('webhooks.razorpay'), [], [], [], [
            'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $body)->assertOk();

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_a_failure_event_marks_the_payment_failed_and_leaves_the_order_pending(): void
    {
        $this->configureWebhookSecret();

        ['order' => $order, 'payment' => $payment] = $this->preparePendingPayment();

        $body = json_encode([
            'event' => 'payment_link.cancelled',
            'payload' => ['payment_link' => ['entity' => ['notes' => ['payment_id' => (string) $payment->id]]]],
        ]);
        $signature = hash_hmac('sha256', $body, self::SECRET);

        $this->call('POST', route('webhooks.razorpay'), [], [], [], [
            'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $body)->assertOk();

        $this->assertSame(PaymentStatus::Failed, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }
}
