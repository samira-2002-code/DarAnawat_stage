<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerPaymentController;
use App\Http\Controllers\OwnerReservationController;
use App\Http\Controllers\OwnerRoomController;
use App\Http\Controllers\PaymentController;
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

Route::middleware(['auth', 'owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('rooms', OwnerRoomController::class)->except('show');
    Route::resource('reservations', OwnerReservationController::class);
    Route::post('/reservations/{reservation}/assign-room', [OwnerReservationController::class, 'assignRoom'])
        ->name('reservations.assign-room');
    Route::patch('/reservations/{reservation}/cancel', [OwnerReservationController::class, 'cancel'])
        ->name('reservations.cancel');
    Route::post('/payments/{payment}/verify', [OwnerPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/receipts/{receipt}/reject', [OwnerPaymentController::class, 'rejectReceipt'])->name('receipts.reject');
    Route::post('/payments/{payment}/not-received', [OwnerPaymentController::class, 'markNotReceived'])->name('payments.not-received');
    Route::post('/reservations/{reservation}/confirm', [OwnerPaymentController::class, 'confirmReservation'])->name('reservations.confirm');
    Route::get('/receipts/{receipt}', [OwnerDashboardController::class, 'downloadReceipt'])->name('receipts.download');
});

Route::get('/chambres/{type}', function (string $type) {
    $rooms = RoomCatalog::all();

    abort_unless(isset($rooms[$type]), 404);

    return view('chambre-details', [
        'room' => $rooms[$type],
        'rooms' => $rooms,
    ]);
})->name('chambre.details');
