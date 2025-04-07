<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{

    // Retourne simplement les données de l'utilisateur connecté de manière sécurisée.
    public function getData()
    {
        $user = Auth::user();
        return response()->json([
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,others',
            'member_type' => 'required|in:resident,visitor,official,worker',
            'profile_picture' => 'nullable|image|max:2048',
            'password' => 'nullable|min:8|max:255|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $user->name = $request->name;
        $user->firstname = $request->firstname;
        $user->email = $request->email;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->member_type = $request->member_type;


        if ($request->hasFile('profile_picture')) {

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => $user
        ]);
    }

    public function showProfile()
    {
        $user = Auth::user();

        return view('profile', [
            'user' => $user
        ]);
    }


}