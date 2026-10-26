<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['fr', 'en', 'es', 'ar'], true), 404);

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

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/reservation', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservation', [ReservationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('reservations.store');

Route::middleware('auth')->prefix('account')->name('payments.')->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('index');
    Route::get('/reservations/{reservation}/payment', [PaymentController::class, 'show'])
        ->name('show');
    Route::post('/reservations/{reservation}/payment/report', [PaymentController::class, 'reportTransfer'])
        ->middleware('throttle:5,1')
        ->name('report');
    Route::get('/payment-receipts/{receipt}', [PaymentController::class, 'downloadReceipt'])
        ->name('receipts.download');
});

Route::get('/chambres/{type}', function (string $type) {
    $rooms = [
        'single' => [
            'slug' => 'single',
            'name' => 'Chambre Single',
            'price' => config('rooms.rates_mad.single'),
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
            'price' => config('rooms.rates_mad.double'),
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
            'price' => config('rooms.rates_mad.suite'),
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
