<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $isActive = $user ? $user->newQuery()->whereKey($user->getKey())->value('is_active') : false;

        abort_unless($user && (bool) $isActive, 403, 'Akun ini tidak aktif. Hubungi administrator.');

        return $next($request);
    }
}
