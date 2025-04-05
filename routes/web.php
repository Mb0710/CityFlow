<?php # test push

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return view('login');
})->name('login');



Route::get('/freetour', function () {
    return view('freetour');
});

Route::post('/testpoint','testpointController@testpoint');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::apiResource('user', AuthController::class);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/map', function () {
        return view('map');
    })->name('map');

    Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'verifyHandler'])->middleware('throttle:6,1')->name('verification.send');
});

Route::get('/check-login', function (Illuminate\Http\Request $request) {
    $login = $request->input('login');
    $exists = \App\Models\User::where('login', $login)->exists();

    return response()->json([
        'available' => !$exists
    ]);
});

Route::get('/check-email', function (Illuminate\Http\Request $request) {
    $login = $request->input('email');
    $exists = \App\Models\User::where('email', $login)->exists();

    return response()->json([
        'available' => !$exists
    ]);
});



Route::get('/test-email', function () {
    $to = "mouloud.choupouloude@gmail.com"; // Remplacez par votre adresse email

    Mail::raw('Ceci est un test d\'envoi d\'email depuis Laravel City Flow', function ($message) use ($to) {
        $message->to($to)
            ->subject('Test Email City Flow');
    });

    return 'Email de test envoyé à ' . $to;
});

