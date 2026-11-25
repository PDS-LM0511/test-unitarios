<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PriceController extends Controller
{
    public const EUR_TO_USD_RATE = 1.10;

    public function index(): View
    {
        return view('calculator', [
            'result' => null,
            'resultLabel' => null,
        ]);
    }

    public function calculate(Request $request): View
    {
        $validated = $request->validate([
            'operation' => ['required', Rule::in(['conversion', 'discount'])],
        ]);

        if ($validated['operation'] === 'conversion') {
            $values = $request->validate([
                'amount' => ['required', 'numeric', 'min:0'],
                'from' => ['required', Rule::in(['EUR', 'USD'])],
                'to' => ['required', Rule::in(['EUR', 'USD'])],
            ]);

            $result = $this->convertCurrency(
                (float) $values['amount'],
                $values['from'],
                $values['to'],
            );
            $resultLabel = "{$values['from']} → {$values['to']}";
        } else {
            $values = $request->validate([
                'price' => ['required', 'numeric', 'min:0'],
                'discount_percentage' => ['required', 'numeric', 'between:0,100'],
            ]);

            $result = $this->calculateDiscount(
                (float) $values['price'],
                (float) $values['discount_percentage'],
            );
            $resultLabel = 'Precio final con descuento';
        }

        return view('calculator', compact('result', 'resultLabel'));
    }

    public function convertCurrency(float $amount, string $from, string $to): float
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('El importe no puede ser negativo.');
        }

        $from = strtoupper($from);
        $to = strtoupper($to);

        if (! in_array($from, ['EUR', 'USD'], true) || ! in_array($to, ['EUR', 'USD'], true)) {
            throw new \InvalidArgumentException('Moneda no soportada. Usa EUR o USD.');
        }

        if ($from === $to) {
            return round($amount, 2);
        }

        $convertedAmount = $from === 'EUR'
            ? $amount * self::EUR_TO_USD_RATE
            : $amount / self::EUR_TO_USD_RATE;

        return round($convertedAmount, 2);
    }

    public function eurosToDollars(float $euros): float
    {
        return $this->convertCurrency($euros, 'EUR', 'USD');
    }

    public function dollarsToEuros(float $dollars): float
    {
        return $this->convertCurrency($dollars, 'USD', 'EUR');
    }

    public function calculateDiscount(float $price, float $discountPercentage): float
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('El precio no puede ser negativo.');
        }

        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new \InvalidArgumentException('El porcentaje de descuento debe estar entre 0 y 100.');
        }

        return round($price - ($price * ($discountPercentage / 100)), 2);
    }
}
