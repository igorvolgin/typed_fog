<?php

namespace App\Providers;

use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Repositories\Country\CachedCountryRepository;
use App\Repositories\Country\RestCountriesRepository;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;
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

        $this->app->bind(CountryRepositoryInterface::class, function ($app) {
            return new CachedCountryRepository(
                new RestCountriesRepository,
                $app['cache.store'],
            );
        });
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
