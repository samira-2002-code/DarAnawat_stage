<?php

use App\Http\Controllers\ReservationController;
use App\Support\RoomCatalog;
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
    return view('chambres', ['rooms' => RoomCatalog::all()]);
})->name('chambres');

Route::get('/reservation', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservation', [ReservationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('reservations.store');

Route::get('/chambres/{type}', function (string $type) {
    $rooms = RoomCatalog::all();

    abort_unless(isset($rooms[$type]), 404);

    return view('chambre-details', [
        'room' => $rooms[$type],
        'rooms' => array_values($rooms),
    ]);
})->name('chambre.details');
