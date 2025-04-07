<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Carbon\Carbon;


class AuthController extends Controller
{
    private function updateLoginPoints($user)
    {
        $today = Carbon::now()->toDateString();
        $pointsToAdd = 0;


        $firstLoginToday = ($user->last_login_date === null || $user->last_login_date < $today);

        if ($firstLoginToday) {

            $yesterday = Carbon::now()->subDay()->toDateString();
            $isConsecutiveLogin = ($user->last_login_date === $yesterday);

            if ($isConsecutiveLogin) {

                $loginStreak = $user->login_streak ?? 0;
                $loginStreak++;


                if ($loginStreak > 7) {
                    $loginStreak = 1;
                }


                if ($loginStreak == 7) {
                    $pointsToAdd = 240;
                } else {
                    $pointsToAdd = 5 * pow(2, $loginStreak - 1);
                }

                $user->login_streak = $loginStreak;
            } else {

                $user->login_streak = 1;
                $pointsToAdd = 5;
            }

            $user->points += $pointsToAdd;

            if ($user->points >= 200) {
                $user->level = 'expert';
            } elseif ($user->points >= 100) {
                $user->level = 'avancé';
            } elseif ($user->points >= 50) {
                $user->level = 'intermédiaire';
            } else {
                $user->level = 'débutant';
            }

            $user->last_login_date = $today;
            $user->save();
        }


        \App\Models\UserConnection::create([
            'user_id' => $user->id,
            'connection_time' => now(),
            'points_earned' => $pointsToAdd
        ]);
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

        $userExists = User::where('login', $credentials['login'])->exists();

        if ($userExists) {
            return response()->json([
                'errors' => ['password' => 'Le mot de passe est incorrect']
            ], 401);
        } else {
            return response()->json([
                'errors' => ['login' => 'Aucun compte trouvé avec ce nom d\'utilisateur']
            ], 401);
        }
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