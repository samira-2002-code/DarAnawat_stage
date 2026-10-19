<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['fr', 'en', 'ar'], true), 404);

    $request->session()->put('locale', $locale);
    app()->setLocale($locale);

    $referer = $request->headers->get('referer');

    if (
        $referer
        && in_array(parse_url($referer, PHP_URL_SCHEME), ['http', 'https'], true)
        && strcasecmp((string) parse_url($referer, PHP_URL_HOST), $request->getHost()) === 0
    ) {
        return redirect()->to($referer);
    }

    return redirect()->route('home');
})->name('language.switch');

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/chambres', function () {
    return view('chambres');
})->name('chambres');

Route::get('/chambres/{type}', function (string $type) {
    $rooms = [
        'single' => [
            'slug' => 'single',
            'name' => 'Chambre Single',
            'price' => 790,
            'description' => 'Un cocon chaleureux et paisible pensé pour offrir un séjour confortable et authentique au cœur de Dar Anawat. Cette chambre associe matières naturelles, détails marocains et confort contemporain.',
            'short_description' => 'Un cocon chaleureux et paisible, idéal pour un séjour en toute simplicité.',
            'capacity' => '1 personne',
            'bed' => 'Lit simple',
            'surface' => '18 m²',
            'image' => asset('images/rooms/atlas.jpg'),
            'gallery' => [
                asset('images/rooms/atlas.jpg'),
                asset('images/rooms/palm.jpg'),
                asset('images/rooms/suite.jpg'),
                asset('images/hero.jpg'),
            ],
        ],
        'double' => [
            'slug' => 'double',
            'name' => 'Chambre Double',
            'price' => 1090,
            'description' => 'Une chambre lumineuse et confortable, idéale pour un séjour à deux. Les matières naturelles et les détails marocains s’accordent au confort contemporain de Dar Anawat.',
            'short_description' => 'Une chambre lumineuse et confortable, parfaite pour un séjour à deux.',
            'capacity' => '2 personnes',
            'bed' => 'Lit queen size',
            'surface' => '24 m²',
            'image' => asset('images/rooms/palm.jpg'),
            'gallery' => [
                asset('images/rooms/palm.jpg'),
                asset('images/rooms/atlas.jpg'),
                asset('images/rooms/suite.jpg'),
                asset('images/hero.jpg'),
            ],
        ],
        'suite' => [
            'slug' => 'suite',
            'name' => 'Suite Dar Anawat',
            'price' => 1690,
            'description' => 'Un espace raffiné et spacieux, pensé pour une expérience unique à Dar Anawat. Profitez d’un cadre intime où l’artisanat marocain rencontre un confort généreux et contemporain.',
            'short_description' => 'Un espace raffiné et spacieux, pensé pour une expérience unique à Dar Anawat.',
            'capacity' => 'Jusqu’à 4 personnes',
            'bed' => 'Lit king size et canapé-lit',
            'surface' => '38 m²',
            'image' => asset('images/rooms/suite.jpg'),
            'gallery' => [
                asset('images/rooms/suite.jpg'),
                asset('images/rooms/palm.jpg'),
                asset('images/rooms/atlas.jpg'),
                asset('images/hero.jpg'),
            ],
        ],
    ];

    abort_unless(isset($rooms[$type]), 404);

    return view('chambre-details', [
        'room' => $rooms[$type],
        'rooms' => array_values($rooms),
    ]);
})->name('chambre.details');
