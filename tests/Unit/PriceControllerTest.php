<?php

use App\Http\Controllers\PriceController;
use PHPUnit\Framework\TestCase;

class PriceControllerTest extends TestCase
{
    public function test_it_converts_euros_to_dollars_for_integer_and_decimal_amounts(): void
    {
        $controller = new PriceController;

        self::assertSame(110.0, $controller->eurosToDollars(100.0));
        self::assertSame(12.1, $controller->convertCurrency(11.0, 'eur', 'usd'));
    }

    public function test_it_converts_dollars_to_euros_for_integer_and_decimal_amounts(): void
    {
        $controller = new PriceController;

        self::assertSame(100.0, $controller->dollarsToEuros(110.0));
        self::assertSame(10.0, $controller->convertCurrency(11.0, 'USD', 'EUR'));
    }

    public function test_it_rounds_same_currency_conversions_and_returns_zero_for_zero_amount(): void
    {
        $controller = new PriceController;

        self::assertSame(12.35, $controller->convertCurrency(12.345, 'EUR', 'EUR'));
        self::assertSame(12.35, $controller->convertCurrency(12.345, 'usd', 'USD'));
        self::assertSame(0.0, $controller->convertCurrency(0.0, 'EUR', 'USD'));
    }

    public function test_it_rejects_negative_currency_amounts(): void
    {
        $controller = new PriceController;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El importe no puede ser negativo.');

        $controller->convertCurrency(-1.0, 'EUR', 'USD');
    }

    public function test_it_rejects_unsupported_source_or_destination_currencies(): void
    {
        $controller = new PriceController;

        try {
            $controller->convertCurrency(1.0, 'GBP', 'EUR');
            self::fail('An unsupported source currency should be rejected.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame('Moneda no soportada. Usa EUR o USD.', $exception->getMessage());
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Moneda no soportada. Usa EUR o USD.');

        $controller->convertCurrency(1.0, 'EUR', 'GBP');
    }

    public function test_it_calculates_standard_and_decimal_discounts(): void
    {
        $controller = new PriceController;

        self::assertSame(80.0, $controller->calculateDiscount(100.0, 20.0));
        self::assertSame(8.99, $controller->calculateDiscount(9.99, 10.0));
    }

    public function test_it_preserves_price_for_zero_discount_and_reduces_it_to_zero_for_full_discount(): void
    {
        $controller = new PriceController;

        self::assertSame(49.99, $controller->calculateDiscount(49.99, 0.0));
        self::assertSame(0.0, $controller->calculateDiscount(49.99, 100.0));
    }

    public function test_it_rejects_negative_prices_for_discounts(): void
    {
        $controller = new PriceController;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El precio no puede ser negativo.');

        $controller->calculateDiscount(-1.0, 20.0);
    }

    public function test_it_rejects_discount_percentages_below_zero_and_above_one_hundred(): void
    {
        $controller = new PriceController;

        try {
            $controller->calculateDiscount(100.0, -1.0);
            self::fail('A negative discount percentage should be rejected.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame('El porcentaje de descuento debe estar entre 0 y 100.', $exception->getMessage());
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El porcentaje de descuento debe estar entre 0 y 100.');

        $controller->calculateDiscount(100.0, 101.0);
    }
}
