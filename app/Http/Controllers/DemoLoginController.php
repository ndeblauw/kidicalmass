<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class DemoLoginController extends Controller
{
    public function __invoke(Request $request, string $role): RedirectResponse
    {
        $emails = [
            'user' => 'user@kidi.be',
            'pinkvest' => 'pinkvest@kidi.be',
            'captain' => 'captain@kidi.be',
            'admin' => 'admin@kidi.be',
        ];

        abort_unless(isset($emails[$role]), 404);

        $user = User::where('email', $emails[$role])->firstOrFail();
        Auth::login($user);

        return app(LoginResponseContract::class)->toResponse($request);
    }
}
