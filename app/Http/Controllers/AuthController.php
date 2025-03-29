<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'login' => 'required|unique:users,login|max:255',
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,others',
            'member_type' => 'required|in:resident,visitor,official,worker'
        ]);

        // Si la validation échoue
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Création de l'utilisateur
        $user = User::create([
            'login' => $request->login,
            'name' => $request->name,
            'firstname' => $request->firstname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'member_type' => $request->member_type,
            'profile_picture' => $request->hasFile('profilPicture')
                ? $request->file('profilPicture')->store('profile_pictures', 'public')
                : null
        ]);

        // Connexion automatique après l'inscription
        Auth::login($user);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Compte créé avec succès',
                'redirect' => route('login')
            ], 201);

        }

        return redirect()->route('login')->with('success', 'Compte créé avec succès');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Connexion réussie',
                'redirect' => route('dashboard')
            ]);
        }

        return response()->json([
            'errors' => ['username' => 'Les identifiants sont incorrects']
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Ici, vous devriez implémenter la logique d'envoi d'email de réinitialisation
        // Laravel a un système intégré pour cela : Password::sendResetLink()

        return response()->json([
            'message' => 'Un email de réinitialisation a été envoyé'
        ]);
    }
}