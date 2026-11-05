<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use LogicException;

class ReservationPricing
{
    public static function amountFor(string $roomType, string $checkIn, string $checkOut): int
    {
        $nightlyRate = config('rooms.rates_mad.'.$roomType);

        if (! is_int($nightlyRate) || $nightlyRate < 1) {
            throw new LogicException('A valid room rate must be configured for every reservable room.');
        }

        $nights = CarbonImmutable::parse($checkIn)->diffInDays(CarbonImmutable::parse($checkOut));

        if ($nights < 1) {
            throw new LogicException('A reservation must include at least one night.');
        }

        return $nightlyRate * $nights;
    }
}
