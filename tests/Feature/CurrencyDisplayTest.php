<?php

namespace Tests\Feature;

use App\Support\Currency;
use InvalidArgumentException;
use Tests\TestCase;

class CurrencyDisplayTest extends TestCase
{
    public function test_mad_prices_are_formatted_in_the_currency_for_each_locale(): void
    {
        $expected = [
            'fr' => '1 090 MAD',
            'en' => '100 EUR',
            'es' => '100 EUR',
            'ar' => '1 090 MAD',
        ];

        foreach ($expected as $locale => $formattedPrice) {
            app()->setLocale($locale);

            $this->assertSame($formattedPrice, Currency::formatMad(1090));
        }
    }

    public function test_home_room_price_strings_are_converted_from_their_mad_amount(): void
    {
        app()->setLocale('en');

        $this->assertSame('87 EUR', Currency::formatMad('950 MAD / night'));
    }

    public function test_room_prices_stay_in_dirhams_on_home_rooms_details_and_booking_pages(): void
    {
        $roomPrices = [
            'single' => '790 DH',
            'double' => '1 090 DH',
            'triple' => '1 500 DH',
            'suite' => '1 690 DH',
        ];

        foreach (['fr', 'en', 'es', 'ar'] as $locale) {
            foreach (['/', '/chambres'] as $url) {
                $response = $this->withSession(['locale' => $locale])
                    ->get($url)
                    ->assertOk();

                foreach ($roomPrices as $price) {
                    $response->assertSee('<bdi dir="ltr">'.$price.'</bdi>', false);
                }
            }

            foreach ($roomPrices as $room => $price) {
                $this->withSession(['locale' => $locale])
                    ->get('/chambres/'.$room)
                    ->assertOk()
                    ->assertSee('<bdi dir="ltr">'.$price.'</bdi>', false);
            }

            $response = $this->withSession(['locale' => $locale])
                ->get(route('reservations.create'))
                ->assertOk();

            foreach ($roomPrices as $price) {
                $response->assertSee($price);
            }
        }
    }

    public function test_non_numeric_room_price_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Currency::formatMad('price unavailable');
    }
}
