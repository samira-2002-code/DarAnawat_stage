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

    public function test_room_prices_use_the_same_currency_on_rooms_and_details_pages(): void
    {
        $expected = [
            'fr' => ['rooms_url' => '/chambres', 'rooms' => ['790 MAD', '1 090 MAD', '1 690 MAD'], 'detail_url' => '/chambres/double', 'detail_price' => '1 090 MAD', 'home' => ['950 MAD', '1 150 MAD', '1 650 MAD']],
            'en' => ['rooms_url' => '/chambres', 'rooms' => ['73 EUR', '100 EUR', '155 EUR'], 'detail_url' => '/chambres/double', 'detail_price' => '100 EUR', 'home' => ['87 EUR', '106 EUR', '152 EUR']],
            'es' => ['rooms_url' => '/chambres', 'rooms' => ['73 EUR', '100 EUR', '155 EUR'], 'detail_url' => '/chambres/double', 'detail_price' => '100 EUR', 'home' => ['87 EUR', '106 EUR', '152 EUR']],
            'ar' => ['rooms_url' => '/chambres', 'rooms' => ['790 MAD', '1 090 MAD', '1 690 MAD'], 'detail_url' => '/chambres/double', 'detail_price' => '1 090 MAD', 'home' => ['950 MAD', '1 150 MAD', '1 650 MAD']],
        ];

        foreach ($expected as $locale => $prices) {
            $this->withSession(['locale' => $locale])
                ->get('/')
                ->assertOk()
                ->assertSee('<bdi dir="ltr">'.$prices['home'][0].'</bdi>', false)
                ->assertSee('<bdi dir="ltr">'.$prices['home'][1].'</bdi>', false)
                ->assertSee('<bdi dir="ltr">'.$prices['home'][2].'</bdi>', false);

            $this->withSession(['locale' => $locale])
                ->get($prices['rooms_url'])
                ->assertOk()
                ->assertSee('<bdi dir="ltr">'.$prices['rooms'][0].'</bdi>', false)
                ->assertSee('<bdi dir="ltr">'.$prices['rooms'][1].'</bdi>', false)
                ->assertSee('<bdi dir="ltr">'.$prices['rooms'][2].'</bdi>', false);

            $this->withSession(['locale' => $locale])
                ->get($prices['detail_url'])
                ->assertOk()
                ->assertSee('<bdi dir="ltr">'.$prices['detail_price'].'</bdi>', false);
        }
    }

    public function test_non_numeric_room_price_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Currency::formatMad('price unavailable');
    }
}
