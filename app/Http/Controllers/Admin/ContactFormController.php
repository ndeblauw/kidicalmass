<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactForm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Ndeblauw\BlueAdmin\Http\Controllers\AdminController;

class ContactFormController extends AdminController
{
    public function convertToUser(ContactForm $contactform): RedirectResponse
    {
        abort_unless($contactform->group_id, 400, 'This submission is not linked to a group.');

        $user = User::firstOrCreate(
            ['email' => $contactform->email],
            [
                'name' => $contactform->name,
                'password' => bcrypt(Str::random(32)),
            ],
        );

        if (! $user->groups()->whereKey($contactform->group)->exists()) {
            $contactform->group->users()->attach($user, ['role' => null]);
        }

        $contactform->update(['handled_at' => now()]);

        return redirect()->back()->with('success', "{$user->name} has been added as an interested member of {$contactform->group->name}.");
    }
}
