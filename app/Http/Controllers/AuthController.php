<?php
    namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Afficher le formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/map');
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.']);
    }

    // Déconnexion
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    // Afficher le formulaire d'inscription
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Traiter l'inscription
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'name' => 'required',
            'firstname' => 'required',
            'birth_date' => 'required',
            'gender' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'firstname' => $request->firstname,
            'birth-date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        Auth::login($user);

        return redirect('/map');
    }
}
?>