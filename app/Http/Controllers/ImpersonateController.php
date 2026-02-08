<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        // Prevent impersonating yourself
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        // Store the original user ID in the session
        $request->session()->put('impersonate_from', Auth::id());

        // Log in as the target user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "You are now logged in as {$user->name}.");
    }

    public function stop(Request $request): RedirectResponse
    {
        // Get the original user ID
        $originalUserId = $request->session()->get('impersonate_from');

        if (!$originalUserId) {
            return redirect()->route('dashboard')->with('error', 'No impersonation session found.');
        }

        // Remove the impersonation session
        $request->session()->forget('impersonate_from');

        // Log back in as the original user
        $originalUser = User::findOrFail($originalUserId);
        Auth::login($originalUser);

        return redirect()->route('filament.admin.pages.dashboard')->with('success', 'You have stopped impersonating.');
    }
}
