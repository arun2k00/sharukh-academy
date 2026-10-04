<?php

namespace Tests\Feature\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhonePeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SALT_KEY = 'salt-key-value';

    private const SALT_INDEX = '1';

    private function configurePhonePe(): void
    {
        $settings = app(SettingsRepository::class);
        $settings->set('phonepe_merchant_id', 'MERCHANTUAT', group: 'payments');
        $settings->set('phonepe_salt_key', app(PaymentSettings::class)->encrypt(self::SALT_KEY), group: 'payments');
        $settings->set('phonepe_salt_index', self::SALT_INDEX, group: 'payments');
    }

    /**
     * @return array{order: Order, payment: Payment, merchantTransactionId: string, base64: string, signature: string}
     */
    private function preparePendingPayment(int $amountInPaise = 499900): array
    {
        $course = Course::factory()->create(['price' => $amountInPaise / 100]);
        $student = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'amount' => $amountInPaise / 100,
            'status' => OrderStatus::Pending,
        ]);

        $merchantTransactionId = "{$order->order_number}-txn";

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'phonepe',
            'gateway_order_id' => $merchantTransactionId,
            'amount' => $order->amount,
            'currency' => $order->currency,
            'status' => PaymentStatus::Pending,
        ]);

        $base64 = base64_encode(json_encode([
            'success' => true,
            'code' => 'PAYMENT_SUCCESS',
            'data' => [
                'merchantTransactionId' => $merchantTransactionId,
                'transactionId' => 'T2409100000000001',
                'amount' => $amountInPaise,
            ],
        ]));

        $signature = hash('sha256', $base64.self::SALT_KEY).'###'.self::SALT_INDEX;

        return compact('order', 'payment', 'merchantTransactionId', 'base64', 'signature');
    }

    private function fakeStatusCheck(string $code = 'PAYMENT_SUCCESS'): void
    {
        Http::fake([
            'api-preprod.phonepe.com/*' => Http::response(['success' => true, 'code' => $code], 200),
        ]);
    }

    public function test_a_valid_webhook_confirmed_by_status_check_marks_the_payment_paid(): void
    {
        $this->configurePhonePe();
        $this->fakeStatusCheck('PAYMENT_SUCCESS');

        ['order' => $order, 'payment' => $payment, 'base64' => $base64, 'signature' => $signature] = $this->preparePendingPayment();

        $this->post(route('webhooks.phonepe'), ['response' => $base64], ['X-VERIFY' => $signature])
            ->assertOk();

        $this->assertSame(PaymentStatus::Successful, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $order->user_id,
            'course_id' => $order->course_id,
            'source' => 'purchase',
        ]);
    }

    public function test_the_same_webhook_delivered_twice_only_enrolls_once(): void
    {
        $this->configurePhonePe();
        $this->fakeStatusCheck('PAYMENT_SUCCESS');

        ['base64' => $base64, 'signature' => $signature, 'order' => $order] = $this->preparePendingPayment();

        $this->post(route('webhooks.phonepe'), ['response' => $base64], ['X-VERIFY' => $signature])->assertOk();
        $this->post(route('webhooks.phonepe'), ['response' => $base64], ['X-VERIFY' => $signature])->assertOk();

        $this->assertDatabaseCount('payments', 1);
        $this->assertSame(1, Enrollment::where('user_id', $order->user_id)
            ->where('course_id', $order->course_id)
            ->count());
    }

    public function test_a_tampered_checksum_is_rejected_and_writes_nothing(): void
    {
        $this->configurePhonePe();
        $this->fakeStatusCheck('PAYMENT_SUCCESS');

        ['base64' => $base64, 'payment' => $payment, 'order' => $order] = $this->preparePendingPayment();

        $this->post(route('webhooks.phonepe'), ['response' => $base64], ['X-VERIFY' => 'not-the-real-checksum'])
            ->assertStatus(400);

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_a_webhook_body_is_never_trusted_without_a_status_check_confirming_it(): void
    {
        $this->configurePhonePe();
        // PhonePe's status API disagrees with the webhook body's optimistic "success".
        $this->fakeStatusCheck('PAYMENT_PENDING');

        ['base64' => $base64, 'signature' => $signature, 'payment' => $payment, 'order' => $order] = $this->preparePendingPayment();

        $this->post(route('webhooks.phonepe'), ['response' => $base64], ['X-VERIFY' => $signature])
            ->assertOk();

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_a_reported_amount_that_does_not_match_the_order_is_never_marked_paid(): void
    {
        $this->configurePhonePe();
        $this->fakeStatusCheck('PAYMENT_SUCCESS');

        $course = Course::factory()->create(['price' => 4999]);
        $student = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $student->id, 'course_id' => $course->id, 'amount' => 4999, 'status' => OrderStatus::Pending]);
        $merchantTransactionId = "{$order->order_number}-txn";
        $payment = Payment::create([
            'order_id' => $order->id, 'gateway' => 'phonepe', 'gateway_order_id' => $merchantTransactionId,
            'amount' => 4999, 'currency' => 'INR', 'status' => PaymentStatus::Pending,
        ]);

        $base64 = base64_encode(json_encode([
            'success' => true,
            'code' => 'PAYMENT_SUCCESS',
            'data' => ['merchantTransactionId' => $merchantTransactionId, 'transactionId' => 'T1', 'amount' => 100],
        ]));
        $signature = hash('sha256', $base64.self::SALT_KEY).'###'.self::SALT_INDEX;

        $this->post(route('webhooks.phonepe'), ['response' => $base64], ['X-VERIFY' => $signature])->assertOk();

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }
}
