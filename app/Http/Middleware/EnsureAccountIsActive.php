<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs on every web request. A suspension must take effect immediately —
 * not just block the next login — so an already-authenticated session is
 * logged out the moment its user is found suspended.
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->status === UserStatus::Suspended) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with(
                'status',
                __('Your account has been suspended. Contact the SE Department Admin Office.'),
            );
        }

        return $next($request);
    }
}
