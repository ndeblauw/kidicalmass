<?php

namespace App\Providers;

use App\Models\Group;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerBladeDirectives();
    }

    protected function registerBladeDirectives(): void
    {
        Blade::if('admin', fn (): bool => Auth::user()?->isSuperAdmin() ?? false);

        Blade::if('pinkvest', fn (Group $group): bool => Auth::user()?->isPinkVestOf($group) ?? false);

        Blade::if('captain', fn (Group $group): bool => Auth::user()?->isCaptainOf($group) ?? false);
    }

    protected function configureDefaults(): void
    {
        // Fallback for non-HTTP contexts (artisan, queued jobs); HTTP requests
        // override this per-request via the SetLocale middleware.
        URL::defaults(['locale' => config('app.locale')]);

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
