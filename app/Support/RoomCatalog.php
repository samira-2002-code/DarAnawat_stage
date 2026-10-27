<?php

namespace App\Support;

use App\Models\Reservation;

class RoomCatalog
{
    public const PRICES = [
        'single' => 800,
        'double' => 1200,
        'triple' => 1500,
        'suite' => 1200,
    ];

    public static function all(): array
    {
        $images = [
            'single' => asset('images/rooms/atlas.jpg'),
            'double' => asset('images/rooms/palm.jpg'),
            'triple' => asset('images/rooms/palm.jpg'),
            'suite' => asset('images/rooms/suite.jpg'),
        ];

        $galleries = [
            'single' => [$images['single'], $images['double'], $images['suite'], asset('images/hero.jpg')],
            'double' => [$images['double'], $images['single'], $images['suite'], asset('images/hero.jpg')],
            'triple' => [$images['triple'], $images['single'], $images['suite'], asset('images/hero.jpg')],
            'suite' => [$images['suite'], $images['double'], $images['single'], asset('images/hero.jpg')],
        ];

        $surfaces = [
            'single' => '18 m²',
            'double' => '24 m²',
            'suite' => '38 m²',
        ];

        $rooms = [];

        foreach (Reservation::ROOM_CAPACITIES as $slug => $capacity) {
            $rooms[$slug] = [
                'slug' => $slug,
                'price' => self::PRICES[$slug],
                'capacity' => $capacity,
                'surface' => $surfaces[$slug] ?? null,
                'image' => $images[$slug],
                'gallery' => $galleries[$slug],
            ];
        }

        return $rooms;
    }
}
