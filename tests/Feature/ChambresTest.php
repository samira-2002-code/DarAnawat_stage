<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChambresTest extends TestCase
{
    public function test_chambres_page_displays_the_three_room_types(): void
    {
        $response = $this->get('/chambres');

        $response->assertOk()
            ->assertSee('Nos Chambres')
            ->assertSee('Chambre Single')
            ->assertSee('Chambre Double')
            ->assertSee('Suite Dar Anawat')
            ->assertSee('Voir détails')
            ->assertSee('Chambres');
    }

    public function test_room_cards_link_to_their_detail_pages(): void
    {
        $this->get('/chambres')
            ->assertOk()
            ->assertSee(route('chambre.details', ['type' => 'single']))
            ->assertSee(route('chambre.details', ['type' => 'double']))
            ->assertSee(route('chambre.details', ['type' => 'suite']));
    }

    public function test_room_detail_page_displays_room_information_gallery_and_amenities(): void
    {
        $this->get('/chambres/single')
            ->assertOk()
            ->assertSee('Chambre Single')
            ->assertSee('790')
            ->assertSee('Un cocon chaleureux et paisible pensé pour offrir un séjour confortable et authentique au cœur de Dar Anawat.')
            ->assertSee('1 personne')
            ->assertSee('Lit simple')
            ->assertSee('18 m²')
            ->assertSee('Équipements')
            ->assertSee('Wi-Fi gratuit')
            ->assertSee('Télévision')
            ->assertSee('Réserver')
            ->assertSee('Chambre Double');
    }

    public function test_each_supported_room_type_has_a_detail_page(): void
    {
        foreach (['double' => 'Chambre Double', 'suite' => 'Suite Dar Anawat'] as $type => $name) {
            $this->get('/chambres/' . $type)
                ->assertOk()
                ->assertSee($name);
        }
    }

    public function test_unknown_room_type_returns_not_found(): void
    {
        $this->get('/chambres/inconnue')->assertNotFound();
    }
}
