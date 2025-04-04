<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    
}