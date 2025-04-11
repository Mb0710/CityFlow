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
            'login' => 'required|string|max:255|unique:users,login,' . $user->id,
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

        $user->login = $request->login;
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

    public function showProfile($username = null)
    {
        if ($username) {

            $user = User::where('login', $username)->first();

            if (!$user) {

                abort(404, "Utilisateur non trouvé.");
            }
        } else {

            $user = Auth::user();
        }

        return view('profil', [
            'user' => $user,
            'currentUser' => Auth::user()
        ]);
    }

    public function getPendingUsers()
    {

        $pendingUsers = User::whereNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pendingUsers);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->save();

        return response()->json(['success' => true]);
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('login', 'like', '%' . $query . '%')
            ->orWhere('name', 'like', '%' . $query . '%')
            ->orWhere('firstname', 'like', '%' . $query . '%')
            ->where('id', '!=', Auth::id()) // Exclure l'utilisateur actuel
            ->select('id', 'login', 'name', 'firstname', 'profile_picture')
            ->limit(10)
            ->get();

        return response()->json($users);
    }


}