<?php

namespace App\Providers;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Auth0::class, function () {
            return new Auth0(new SdkConfiguration(
                strategy: SdkConfiguration::STRATEGY_API,
                domain: config('auth0.domain'),
                audience: [config('auth0.audience')],
            ));
        });
    }

    public function boot(): void {}
}
