<?php

namespace Tests\Unit\Support;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_rupees_convert_to_paise(): void
    {
        $this->assertSame(799900, Money::toSubunit('7999.00'));
        $this->assertSame(100, Money::toSubunit(1));
        $this->assertSame(0, Money::toSubunit(0));
    }

    public function test_paise_convert_back_to_rupees(): void
    {
        $this->assertSame(7999.0, Money::fromSubunit(799900));
        $this->assertSame(1.0, Money::fromSubunit(100));
        $this->assertSame(0.5, Money::fromSubunit(50));
    }

    public function test_fractional_rupee_amounts_round_trip_without_drift(): void
    {
        $this->assertSame(129900, Money::toSubunit('1299.00'));
        $this->assertSame(1299.0, Money::fromSubunit(129900));
    }
}
