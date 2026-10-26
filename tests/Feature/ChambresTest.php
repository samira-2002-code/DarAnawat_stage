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
            ->assertSee('Lits')
            ->assertSee('2 lits séparés')
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

    public function test_room_detail_pages_show_the_correct_bed_configuration_in_every_locale(): void
    {
        $bedConfigurations = [
            'en' => [
                'single' => '2 separate beds',
                'double' => '1 large bed',
                'triple' => '3 separate beds',
                'suite' => '1 large bed + private terrace',
            ],
            'fr' => [
                'single' => '2 lits séparés',
                'double' => '1 grand lit',
                'triple' => '3 lits séparés',
                'suite' => '1 grand lit + terrasse privée',
            ],
            'es' => [
                'single' => '2 camas separadas',
                'double' => '1 cama grande',
                'triple' => '3 camas separadas',
                'suite' => '1 cama grande + terraza privada',
            ],
            'ar' => [
                'single' => 'سريران منفصلان',
                'double' => 'سرير كبير واحد',
                'triple' => 'ثلاثة أسرّة منفصلة',
                'suite' => 'سرير كبير واحد + تراس خاص',
            ],
        ];

        foreach ($bedConfigurations as $locale => $rooms) {
            foreach ($rooms as $type => $bedConfiguration) {
                $this->withSession(['locale' => $locale])
                    ->get('/chambres/' . $type)
                    ->assertOk()
                    ->assertSee(__('site.common.bed_type', [], $locale))
                    ->assertSee($bedConfiguration);
            }
        }
    }

    public function test_all_room_detail_pages_show_air_conditioning_in_every_locale(): void
    {
        $airConditioningLabels = [
            'en' => ['Air conditioning', 'Available'],
            'fr' => ['Climatisation', 'Disponible'],
            'es' => ['Aire acondicionado', 'Disponible'],
            'ar' => ['تكييف الهواء', 'متوفر'],
        ];

        foreach ($airConditioningLabels as $locale => [$label, $available]) {
            foreach (['single', 'double', 'triple', 'suite'] as $type) {
                $this->withSession(['locale' => $locale])
                    ->get('/chambres/' . $type)
                    ->assertOk()
                    ->assertSee($label)
                    ->assertSee($available);
            }
        }
    }

    public function test_room_information_contains_only_capacity_beds_and_air_conditioning(): void
    {
        $labels = [
            'en' => ['Capacity', 'Beds', 'Air conditioning', 'Available', 'Size'],
            'fr' => ['Capacité', 'Lits', 'Climatisation', 'Disponible', 'Surface'],
            'es' => ['Capacidad', 'Camas', 'Aire acondicionado', 'Disponible', 'Superficie'],
            'ar' => ['السعة', 'الأسرة', 'تكييف الهواء', 'متوفر', 'المساحة'],
        ];

        foreach ($labels as $locale => [$capacity, $beds, $airConditioning, $available, $size]) {
            foreach (['single', 'double', 'triple', 'suite'] as $type) {
                $response = $this->withSession(['locale' => $locale])->get('/chambres/' . $type);
                $response->assertOk();

                $this->assertSame(
                    1,
                    preg_match('/<dl class="mt-8[^"]*">(.*?)<\/dl>/s', $response->getContent(), $matches),
                    'Expected the room information section to be rendered.'
                );
                $information = $matches[1];

                $this->assertSame(3, preg_match_all('/<div>/', $information));
                $this->assertStringContainsString($capacity, $information);
                $this->assertStringContainsString($beds, $information);
                $this->assertStringContainsString($airConditioning, $information);
                $this->assertStringContainsString($available, $information);
                $this->assertStringNotContainsString($size, $information);
                $this->assertStringNotContainsString('m²', $information);
            }
        }
    }

    public function test_unknown_room_type_returns_not_found(): void
    {
        $this->get('/chambres/inconnue')->assertNotFound();
    }
}
