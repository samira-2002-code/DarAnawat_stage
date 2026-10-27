<?php

namespace Tests\Feature;

use Tests\TestCase;

class FooterContactTest extends TestCase
{
    public function test_footer_displays_official_clickable_contact_and_social_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="tel:+212763099428"', false)
            ->assertSee('+212 763-099428')
            ->assertSee('href="mailto:www.dar.ennawat@gmail.com"', false)
            ->assertSee('www.dar.ennawat@gmail.com')
            ->assertSee('48 Derb Jamaa, Rue Riad Zitoun el Kdim, Marrakech 40000, Morocco')
            ->assertSee('href="https://www.instagram.com/riaddarennawat/" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('href="https://wa.me/212763099428" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('id="contact"', false);
    }

    public function test_footer_contact_actions_are_localized_in_every_supported_language(): void
    {
        foreach ([
            'en' => ['Quick links', 'Message us on WhatsApp', 'Follow us on Instagram'],
            'fr' => ['Liens rapides', 'Écrivez-nous sur WhatsApp', 'Retrouvez-nous sur Instagram'],
            'es' => ['Enlaces rápidos', 'Escríbanos por WhatsApp', 'Síganos en Instagram'],
            'ar' => ['روابط سريعة', 'راسلونا عبر واتساب', 'تابعونا على إنستغرام'],
        ] as $locale => [$navigation, $whatsapp, $instagram]) {
            $this->withSession(['locale' => $locale])
                ->get('/')
                ->assertOk()
                ->assertSee($navigation)
                ->assertSee($whatsapp)
                ->assertSee($instagram)
                ->assertSee('48 Derb Jamaa, Rue Riad Zitoun el Kdim, Marrakech 40000, Morocco');
        }
    }
}
