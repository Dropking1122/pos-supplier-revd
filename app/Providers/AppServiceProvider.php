<?php

namespace App\Providers;

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
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('setting', \App\Models\Setting::getSettings());
            } else {
                $view->with('setting', new \App\Models\Setting(['company_name' => config('app.name')]));
            }
        });
    }
}
