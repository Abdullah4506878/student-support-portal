<?php

namespace App\Http\Responses;

use App\Enums\RoleName;
use App\Models\User;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Only honor the intended URL if the logged-in user's role can
     * actually reach it; otherwise send them to their own dashboard.
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $intended = $request->session()->pull('url.intended');

        if ($intended && $this->roleCanAccess($request->user(), $intended)) {
            return redirect()->to($intended);
        }

        return redirect()->route('dashboard');
    }

    private function roleCanAccess(User $user, string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        return match (true) {
            str_starts_with($path, '/student') => $user->hasRole(RoleName::Student->value),
            str_starts_with($path, '/admin') => $user->hasRole(RoleName::AdminOfficer->value),
            str_starts_with($path, '/super-admin') => $user->hasRole(RoleName::SuperAdmin->value),
            default => true,
        };
    }
}
