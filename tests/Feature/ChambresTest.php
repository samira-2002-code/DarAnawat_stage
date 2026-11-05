<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChambresTest extends TestCase
{
    public function test_chambres_page_displays_all_four_room_types_and_their_prices(): void
    {
        $response = $this->get('/chambres');

        $response->assertOk()
            ->assertSee('Nos chambres')
            ->assertSee('Chambre Single')
            ->assertSee('Chambre Double')
            ->assertSee('Chambre Triple')
            ->assertSee('Suite')
            ->assertSee('800 DH')
            ->assertSee('1 200 DH')
            ->assertSee('1 500 DH')
            ->assertSee('Voir les détails')
            ->assertSee('Réserver')
            ->assertSee('CHAMBRES');
    }

    public function test_room_cards_link_to_their_detail_pages(): void
    {
        $this->get('/chambres')
            ->assertOk()
            ->assertSee(route('chambre.details', ['type' => 'single']))
            ->assertSee(route('chambre.details', ['type' => 'double']))
            ->assertSee(route('chambre.details', ['type' => 'triple']))
            ->assertSee(route('chambre.details', ['type' => 'suite']));
    }

    public function test_room_detail_page_displays_room_information_gallery_and_amenities(): void
    {
        $this->get('/chambres/single')
            ->assertOk()
            ->assertSee('Chambre Single')
            ->assertSee('800 DH')
            ->assertSee('Une chambre sereine pour une personne, où les matières naturelles et les détails marocains s’accordent au confort contemporain.')
            ->assertSee('1 personne')
            ->assertSee('Lit simple')
            ->assertSee('18 m²')
            ->assertSee('Le petit-déjeuner est inclus dans votre réservation.')
            ->assertSee('Le plaisir de retrouver le calme après la ville.')
            ->assertSee('Équipements')
            ->assertSee('Wi-Fi gratuit')
            ->assertSee('Télévision')
            ->assertSee('Réserver')
            ->assertSee('Chambre Double');
    }

    public function test_each_supported_room_type_has_a_detail_page(): void
    {
        foreach (['double' => 'Chambre Double', 'triple' => 'Chambre Triple', 'suite' => 'Suite'] as $type => $name) {
            $this->get('/chambres/' . $type)
                ->assertOk()
                ->assertSee($name);
        }
    }

    public function test_reservation_form_lists_each_room_with_its_exact_price(): void
    {
        $this->get(route('reservations.create'))
            ->assertOk()
            ->assertSee('Chambre Single — 800 DH / nuit')
            ->assertSee('Chambre Double — 1 200 DH / nuit')
            ->assertSee('Chambre Triple — 1 500 DH / nuit')
            ->assertSee('Suite — 1 200 DH / nuit');
    }

    public function test_unknown_room_type_returns_not_found(): void
    {
        $this->get('/chambres/inconnue')->assertNotFound();
    }
}
