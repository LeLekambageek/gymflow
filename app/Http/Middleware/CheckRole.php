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
            // Le gérant essaie d'accéder à une page propriétaire
            abort(403, 'Accès réservé au propriétaire.');
        }

        return $next($request);
    }
}
