<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request and enforce strict role separation.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $currentRole = $request->session()->get('auth_role');

        if (! is_string($currentRole) || $currentRole === '') {
            return redirect()->route('login');
        }

        if (! in_array($currentRole, $roles, true)) {
            $targetRoute = $currentRole === 'manajer'
                ? 'admin.dashboard'
                : 'karyawan.dashboard';

            return redirect()->route($targetRoute)->with('access_error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
