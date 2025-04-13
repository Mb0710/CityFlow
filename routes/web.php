<?php

use App\Http\Controllers\ObjectTypeController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserActions;
use App\Http\Controllers\ConnectedObjectsController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ChartController;




// Routes publiques
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    $report = \App\Models\ConnectedObject::generateReport();
    $usersCount = \App\Models\User::count();
    $pollutionSensorsCount = \App\Models\ConnectedObject::where('type', 'capteur_pollution')->count();

    return view('login', compact('report', 'usersCount', 'pollutionSensorsCount'));
})->name('login');

Route::get('/freetour', function () {
    return view('freetour');
});

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'passwordReset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->middleware('guest')->name('password.update');


Route::get('/check-login', function (Illuminate\Http\Request $request) {
    $login = $request->input('login');
    $exists = \App\Models\User::where('login', $login)->exists();
    return response()->json(['available' => !$exists]);
});

Route::get('/check-email', function (Illuminate\Http\Request $request) {
    $email = $request->input('email');
    $exists = \App\Models\User::where('email', $email)->exists();
    return response()->json(['available' => !$exists]);
});

Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'verifyHandler'])->middleware('throttle:6,1')->name('verification.send');

// Routes testpoint (à sécuriser si nécessaire)
Route::post('/testpoint', 'App\Http\Controllers\ConnectedObjectController@searchConnectedObject');
Route::post('/testpoint/store', 'App\Http\Controllers\ConnectedObjectController@store');

// ========== ROUTES AUTHENTIFIÉES ==========
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard', ['user' => Auth::user()]);
    })->name('dashboard');

    Route::get('/map', function () {
        return view('map');
    })->name('map');

    Route::get('/profil/{username?}', [UserController::class, 'showProfile'])->name('profile');
    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('update.profile');
    Route::get('/user/data', [UserController::class, 'getData'])->name('user.data');



    // Routes accessibles aux utilisateurs de niveau intermédiaire et supérieur
    Route::middleware('level:intermédiaire,avancé,expert')->group(function () {
        Route::get('/Gestion', function () {
            return view('gestion');
        })->name('gestion');

        Route::get('/connected-objects', [ConnectedObjectsController::class, 'index']);
        Route::get('/connected-objects/{id}', [ConnectedObjectsController::class, 'show']);
        Route::put('/connected-objects/{id}', [ConnectedObjectsController::class, 'update']);

        Route::get('/search-users', [UserController::class, 'searchUsers'])->name('search.users');
    });


    // Routes accessibles aux utilisateurs de niveau expert uniquement
    Route::middleware('level:avancé,expert')->group(function () {
        // Création et modification d'objets connectés
        Route::post('/connected-objects', [ConnectedObjectsController::class, 'store']);
    });

    Route::middleware('level:expert')->group(function () {

        Route::get('/rapport', function () {
            return view('rapport');
        });
        Route::get('/rapport', [App\Http\Controllers\RapportController::class, 'showReport'])->name('rapport');

        Route::get('/stats', [ChartController::class, 'stats'])
            ->name('stats'); //  Nom de la route


        Route::put('/connected-objects/{id}/report', [ConnectedObjectsController::class, 'report']);
    });


    // ========== ROUTES ADMINISTRATEUR ==========
    Route::middleware('isAdmin:admin')->group(function () {
        // Interface d'administration principale
        Route::get('/admin', function () {
            return view('admin');
        })->name('admin');

        // Gestion des utilisateurs
        Route::get('/admin/ajout', function () {
            return view('ajout');
        })->name('ajout');


        Route::get('admin/rapportUtilisateur', [App\Http\Controllers\UserActionReportController::class, 'showReport'])
            ->name('user-actions');


        Route::post('/admin/users/{id}/update-points', [UserController::class, 'updatePoints']);


        Route::get('/admin/inscriptions', function () {
            return view('inscription');
        })->name('inscriptions');

        Route::get('/admin/users/pending', [UserController::class, 'getPendingUsers'])->name('users.pending');
        Route::post('/admin/users/{id}/approve', [UserController::class, 'approveUser']);
        Route::post('/admin/users/{id}/reject', [UserController::class, 'rejectUser']);

        // Gestion des objets connectés (suppression/modération)
        Route::get('/admin/suppression', function () {
            return view('suppression');
        })->name('suppression');

        Route::get('/admin/inspections', function () {
            return view('inspections');
        })->name('inspections');

        Route::get('/admin/object-types', [ObjectTypeController::class, 'index'])->name('object.types');
        Route::post('/admin/object-types', [ObjectTypeController::class, 'store']);
        Route::delete('/admin/object-types/{id}', [ObjectTypeController::class, 'destroy']);

        Route::get('/admin/user-actions', [UserActions::class, 'index'])->name('user.actions');

        Route::get('admin/rapportUtilisateur/pdf', [App\Http\Controllers\UserActionReportController::class, 'downloadPDF'])
            ->name('user-actions.pdf');

        Route::get('/reported', [ConnectedObjectsController::class, 'getReportedObjects']);
        Route::delete('/connected-objects/{id}', [ConnectedObjectsController::class, 'destroy']);
        Route::post('/connected-objects/{id}/cancel-report', [ConnectedObjectsController::class, 'cancelReport']);
    });
});

// Route de test email (à supprimer en production)
Route::get('/test-email', function () {
    $to = "mouloud.choupouloude@gmail.com";
    Mail::raw('Ceci est un test d\'envoi d\'email depuis Laravel City Flow', function ($message) use ($to) {
        $message->to($to)
            ->subject('Test Email City Flow');
    });
    return 'Email de test envoyé à ' . $to;
});