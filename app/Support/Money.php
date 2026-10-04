<?php

namespace App\Support;

/**
 * Converts between the rupee amounts this app stores (`decimal:2`) and the
 * smallest-currency-unit integers Razorpay and PhonePe require (paise) — the
 * one conversion that would otherwise risk a 100x billing bug if inlined at
 * every call site.
 */
final class Money
{
    public static function toSubunit(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public static function fromSubunit(int $amount): float
    {
        return round($amount / 100, 2);
    }
}
