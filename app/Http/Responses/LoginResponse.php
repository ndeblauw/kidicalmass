<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return redirect()->intended('/admin');
        }

        $group = $user->groups()
            ->wherePivotIn('role', ['captain', 'pinkvest'])
            ->orderBy('name')
            ->first();

        if ($group) {
            return redirect()->intended(route('groups.show', [
                'locale' => 'nl',
                'group' => $group,
            ]));
        }

        return redirect()->intended(config('fortify.home'));
    }
}
