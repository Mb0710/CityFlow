<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    private function updateLoginPoints($user)
    {
        $today = now()->toDateString();

        if ($user->last_login_date === null || $user->last_login_date < $today) {
            $user->points += 5;
            $user->last_login_date = $today;
            $user->save();
        }
    }

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


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


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


        Auth::login($user);
        $this->updateLoginPoints($user);

        event(new Registered($user));



        return redirect()->route('verification.notice')->with('success', 'Compte créé avec succès');
    }

    public function verifyNotice()
    {
        return view('auth.verify-email');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('login');
    }

    public function verifyHandler(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            $this->updateLoginPoints($user);

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

        return redirect('/');
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


        return response()->json([
            'message' => 'Un email de réinitialisation a été envoyé'
        ]);
    }
}