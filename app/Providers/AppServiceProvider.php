<?php

namespace App\Providers;

use App\Contracts\Auth\GoogleOAuthBroker;
use App\Services\Auth\SocialiteGoogleOAuthBroker;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GoogleOAuthBroker::class, SocialiteGoogleOAuthBroker::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
