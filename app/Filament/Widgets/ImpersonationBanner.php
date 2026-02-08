<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Http\Controllers\ImpersonateController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationBanner extends Widget
{
    protected string $view = 'filament.widgets.impersonation-banner';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return session()->has('impersonate_from');
    }

    public function getOriginalUserName(): ?string
    {
        $originalUserId = session()->get('impersonate_from');
        if (!$originalUserId) {
            return null;
        }

        $originalUser = User::find($originalUserId);

        return $originalUser?->name;
    }

    public function getCurrentUserName(): string
    {
        return Auth::user()->name;
    }
}
