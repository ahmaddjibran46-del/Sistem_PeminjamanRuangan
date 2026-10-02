<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan user login pada guard tertentu (admin / mahasiswa), akun aktif,
 * dan menjadikan guard tersebut default agar Gate/Policy memakai user yang benar.
 */
class EnsureGuard
{
    public function handle(Request $request, Closure $next, string $guard): Response
    {
        $auth = Auth::guard($guard);
        $loginRoute = $guard === 'admin' ? 'admin.login' : 'login';

        if (! $auth->check()) {
            return redirect()->guest(route($loginRoute));
        }

        if ($auth->user()->status_akun !== 'aktif') {
            $auth->logout();

            return redirect()->route($loginRoute)
                ->withErrors(['username' => 'Akun Anda dinonaktifkan. Hubungi pengelola.']);
        }

        Auth::shouldUse($guard);

        return $next($request);
    }
}
