<?php

namespace App\Http\Controllers;
use App\Models\UserAction;

class UserActions extends Controller
{
    public function index()
    {
        $usersActions = UserAction::with(['user:id,login,name', 'connectedObject:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json([
            'success' => true,
            'data' => $usersActions
        ]);
    }
}