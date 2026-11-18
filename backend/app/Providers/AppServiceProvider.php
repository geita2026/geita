<?php

namespace App\Providers;

use App\Services\Gepg\GepgService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance per request, so the simulator's held callbacks survive until flushed.
        $this->app->singleton(GepgService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
