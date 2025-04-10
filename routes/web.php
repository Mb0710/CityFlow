<?php

use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConnectedObjectsController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('login');
})->name('login');



Route::get('/freetour', function () {
    return view('freetour');
});

Route::post('/testpoint', 'App\Http\Controllers\ConnectedObjectController@searchConnectedObject');
Route::post('/testpoint/store', 'App\Http\Controllers\ConnectedObjectController@store');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {

    Route::get('/map', function () {
        return view('map');
    })->name('map');

    Route::post('/update-profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('update.profile');

    Route::get('/profil/{username?}', [UserController::class, 'showProfile'])->name('profile');

    Route::get('/Gestion', function () {
        return view('gestion');
    })->name('gestion');


    Route::get('/connected-objects', [App\Http\Controllers\ConnectedObjectsController::class, 'index']);
    Route::post('/connected-objects', [App\Http\Controllers\ConnectedObjectsController::class, 'store']);
    Route::get('/connected-objects/{id}', [App\Http\Controllers\ConnectedObjectsController::class, 'show']);
    Route::put('/connected-objects/{id}', [App\Http\Controllers\ConnectedObjectsController::class, 'update']);

    Route::get('/dashboard', function () {
        return view('dashboard', ['user' => Auth::user()]);
    })->middleware('verified')->name('dashboard');

    Route::get('/Profil', function () {
        return view('profil', ['user' => Auth::user()]);
    })->middleware('verified')->name('profil');

    Route::get('/admin', function () {
        return view('admin');
    })->middleware('verified')->name('admin');

    Route::get('/admin/ajout', function () {
        return view('ajout');
    })->middleware('verified')->name('ajout');

    Route::get('/admin/inscriptions', function () {
        return view('inscription');
    })->middleware('verified')->name('ajout');


    Route::get('/admin/suppression', function () {
        return view('suppression');
    })->middleware('verified')->name('suppression');

    Route::get('/reported', [ConnectedObjectsController::class, 'getReportedObjects']);
    Route::delete('/connected-objects/{id}', [ConnectedObjectsController::class, 'destroy']);
    Route::post('/connected-objects/{id}/cancel-report', [ConnectedObjectsController::class, 'cancelReport']);

    Route::put('/connected-objects/{id}/report', [ConnectedObjectsController::class, 'report']);
    Route::get('/admin/users/pending', [UserController::class, 'getPendingUsers'])->middleware('auth', 'verified')->name('users.pending');
    Route::get('/user/data', [UserController::class, 'getData'])->name('user.data');
    Route::post('/admin/users/{id}/approve', [UserController::class, 'approveUser'])->middleware('auth', 'verified');
    Route::post('/admin/users/{id}/reject', [UserController::class, 'rejectUser'])->middleware('auth', 'verified');
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

Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail'])->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'passwordReset'])->middleware('guest')->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->middleware('guest')->name('password.update');

Route::get('/test-email', function () {
    $to = "mouloud.choupouloude@gmail.com"; // Remplacez par votre adresse email

    Mail::raw('Ceci est un test d\'envoi d\'email depuis Laravel City Flow', function ($message) use ($to) {
        $message->to($to)
            ->subject('Test Email City Flow');
    });

    return 'Email de test envoyé à ' . $to;
});

