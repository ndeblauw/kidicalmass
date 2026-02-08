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
        // Basic authorization - in production, add a proper admin check
        // For example: if (!Auth::user()->is_admin) { abort(403); }
        
        // Prevent impersonating yourself
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        // Store the original user ID and flash message before switching
        session()->flash('success', "You are now logged in as {$user->name}.");
        $request->session()->put('impersonate_from', Auth::id());

        // Log in as the target user
        Auth::login($user);

        return redirect()->route('dashboard');
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
        $originalUser = User::find($originalUserId);
        
        if (!$originalUser) {
            Auth::logout();

            return redirect()->route('filament.admin.auth.login')->with('error', 'Original user account not found.');
        }

        // Flash message before switching users
        session()->flash('success', 'You have stopped impersonating.');
        Auth::login($originalUser);

        return redirect()->route('filament.admin.pages.dashboard');
    }
}
