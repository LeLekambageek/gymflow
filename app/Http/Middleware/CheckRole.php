<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }


        if (auth()->user()->role !== $role && $role !== 'any') {
            //L'utilisateur n'a pas le bon rôle
            abort(403, 'Accès réservé au ' . $role);
        }

        return $next($request);
    }
}
