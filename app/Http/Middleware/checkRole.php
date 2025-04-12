<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{

    public function handle(Request $request, Closure $next, ...$levels)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Connexion requise');
        }

        $user = Auth::user();

        if (in_array($user->level, $levels)) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Accès non autorisé');
    }
}