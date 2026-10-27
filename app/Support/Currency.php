<?php

namespace App\Support;

use InvalidArgumentException;

class Currency
{
    public static function formatDh(int|float $price): string
    {
        return number_format($price, 0, ',', ' ').' DH';
    }

    public static function formatMad(int|float|string $madPrice, ?string $locale = null): string
    {
        $baseCurrency = config('currency.base');
        $locale ??= app()->getLocale();
        $currency = $locale === 'ar'
            ? $baseCurrency
            : config('currency.display_by_locale.'.$locale);
        $rates = config('currency.rates', []);

        if (! is_string($currency) || ! isset($rates[$currency], $rates[$baseCurrency])) {
            throw new InvalidArgumentException('No display exchange rate is configured for the current locale.');
        }

        if (is_string($madPrice)) {
            if (! preg_match('/\d+(?:[.,]\d+)?/', $madPrice, $matches)) {
                throw new InvalidArgumentException('The room price must contain a numeric MAD amount.');
            }

            $madPrice = (float) str_replace(',', '.', $matches[0]);
        }

        $displayPrice = round(($madPrice / $rates[$baseCurrency]) * $rates[$currency]);
        $formattedPrice = number_format($displayPrice, 0, ',', ' ');

        return $formattedPrice.' '.$currency;
    }
}
