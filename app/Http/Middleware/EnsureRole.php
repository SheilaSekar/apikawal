<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->getRawOriginal('role');
        $isActive = $user ? $user->newQuery()->whereKey($user->getKey())->value('is_active') : false;

        abort_unless($user && (bool) $isActive && in_array($role, $roles, true), 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $next($request);
    }
}
