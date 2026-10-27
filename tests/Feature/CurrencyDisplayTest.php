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
        $expected = [
            'fr' => ['rooms_url' => '/chambres', 'rooms' => ['790 MAD', '1 090 MAD', '1 690 MAD'], 'detail_url' => '/chambres/double', 'detail_price' => '1 090 MAD', 'home' => ['790 MAD', '1 090 MAD', '1 690 MAD']],
            'en' => ['rooms_url' => '/chambres', 'rooms' => ['73 EUR', '100 EUR', '155 EUR'], 'detail_url' => '/chambres/double', 'detail_price' => '100 EUR', 'home' => ['73 EUR', '100 EUR', '155 EUR']],
            'es' => ['rooms_url' => '/chambres', 'rooms' => ['73 EUR', '100 EUR', '155 EUR'], 'detail_url' => '/chambres/double', 'detail_price' => '100 EUR', 'home' => ['73 EUR', '100 EUR', '155 EUR']],
            'ar' => ['rooms_url' => '/chambres', 'rooms' => ['790 MAD', '1 090 MAD', '1 690 MAD'], 'detail_url' => '/chambres/double', 'detail_price' => '1 090 MAD', 'home' => ['790 MAD', '1 090 MAD', '1 690 MAD']],
        ];

        foreach ($expected as $locale => $prices) {
            $this->withSession(['locale' => $locale])
                ->get('/')
                ->assertOk()
                ->assertSee('<bdi dir="ltr">800 DH</bdi>', false)
                ->assertSee('<bdi dir="ltr">1 200 DH</bdi>', false)
                ->assertSee('<bdi dir="ltr">1 500 DH</bdi>', false);

            $this->withSession(['locale' => $locale])
                ->get('/chambres')
                ->assertOk()
                ->assertSee('<bdi dir="ltr">800 DH</bdi>', false)
                ->assertSee('<bdi dir="ltr">1 200 DH</bdi>', false)
                ->assertSee('<bdi dir="ltr">1 500 DH</bdi>', false);

            foreach (['single' => '800 DH', 'double' => '1 200 DH', 'triple' => '1 500 DH', 'suite' => '1 200 DH'] as $room => $price) {
                $this->withSession(['locale' => $locale])
                    ->get('/chambres/'.$room)
                    ->assertOk()
                    ->assertSee('<bdi dir="ltr">'.$price.'</bdi>', false);
            }

            $this->withSession(['locale' => $locale])
                ->get(route('reservations.create'))
                ->assertOk()
                ->assertSee('800 DH')
                ->assertSee('1 200 DH')
                ->assertSee('1 500 DH');
        }
    }

    public function test_non_numeric_room_price_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Currency::formatMad('price unavailable');
    }
}
