<?php

namespace App\Providers;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsOwner;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Livewire::addPersistentMiddleware([
            IsAdmin::class,
            IsOwner::class,
        ]);
    }
}
