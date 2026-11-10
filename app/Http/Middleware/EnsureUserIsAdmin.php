<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    // Libera a rota apenas para usuários do grupo administrador.
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->loadMissing('group')->isAdmin(), 403);

        return $next($request);
    }
}
