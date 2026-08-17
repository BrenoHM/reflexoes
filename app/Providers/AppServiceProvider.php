<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;

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
        // Um administrador já autenticado que visite /login deve cair no
        // painel administrativo, não na página pública ("home").
        RedirectIfAuthenticated::redirectUsing(fn () => route('admin.dashboard'));

        Carbon::setLocale(config('app.locale'));
    }
}
