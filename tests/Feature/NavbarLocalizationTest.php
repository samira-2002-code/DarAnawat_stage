<?php

namespace Tests\Feature;

use Tests\TestCase;

class NavbarLocalizationTest extends TestCase
{
    public function test_room_navigation_label_uses_the_current_locale(): void
    {
        foreach ([
            'en' => 'ROOMS',
            'fr' => 'CHAMBRES',
            'ar' => 'الغرف',
        ] as $locale => $label) {
            $this->withSession(['locale' => $locale])
                ->get('/chambres')
                ->assertOk()
                ->assertSee($label)
                ->assertSee(route('chambres'));
        }
    }

    public function test_language_switch_is_stored_in_session_and_survives_navigation(): void
    {
        $this->withHeader('referer', 'http://localhost/chambres')
            ->get('/language/en')
            ->assertRedirect('http://localhost/chambres')
            ->assertSessionHas('locale', 'en');

        $this->get('/chambres')
            ->assertOk()
            ->assertSee('Our Rooms')
            ->assertSee('ROOMS');
    }

    public function test_spanish_switch_is_stored_and_retains_the_current_page(): void
    {
        $this->withHeader('referer', 'http://localhost/chambres/single')
            ->get('/language/es')
            ->assertRedirect('http://localhost/chambres/single')
            ->assertSessionHas('locale', 'es');

        $this->get('/chambres/single')
            ->assertOk()
            ->assertSee('Habitación individual')
            ->assertSee('HABITACIONES');
    }

    public function test_default_locale_is_french(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="fr" dir="ltr">', false)
            ->assertSee('VOTRE ÉVASION MAROCAINE');
    }

    public function test_arabic_pages_use_rtl_and_translated_content(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get('/chambres/single')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('غرفة فردية')
            ->assertSee('المرافق');
    }

    public function test_home_rooms_and_details_are_translated_in_each_locale(): void
    {
        $localizedContent = [
            'fr' => ['VOTRE ÉVASION MAROCAINE', 'Nos Chambres', 'Chambre Single'],
            'en' => ['YOUR MOROCCAN ESCAPE', 'Our Rooms', 'Single Room'],
            'es' => ['SU ESCAPADA MARROQUÍ', 'Nuestras habitaciones', 'Habitación individual'],
            'ar' => ['رحلتكم المغربية', 'غرفنا', 'غرفة فردية'],
        ];

        foreach ($localizedContent as $locale => [$homeText, $roomsText, $detailsText]) {
            $this->withSession(['locale' => $locale])
                ->get('/')
                ->assertOk()
                ->assertSee($homeText)
                ->assertSee('ES');

            $this->withSession(['locale' => $locale])
                ->get('/chambres')
                ->assertOk()
                ->assertSee($roomsText);

            $this->withSession(['locale' => $locale])
                ->get('/chambres/single')
                ->assertOk()
                ->assertSee($detailsText);
        }
    }

    public function test_unsupported_locale_is_not_accepted(): void
    {
        $this->get('/language/de')->assertNotFound();
    }
}
