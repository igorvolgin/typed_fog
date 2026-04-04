<?php

namespace App\Providers;

use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Repositories\Country\CachedCountryRepository;
use App\Repositories\Country\RestCountriesRepository;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CacheItemPoolInterface::class, fn ($app) => new Psr16Adapter($app['cache.store']));

        $this->app->singleton(Auth0::class, fn ($app) => new Auth0(new SdkConfiguration(
            strategy: SdkConfiguration::STRATEGY_API,
            domain: config('auth0.domain'),
            audience: [config('auth0.audience')],
            tokenCache: $app->make(CacheItemPoolInterface::class),
            tokenCacheTtl: 3600,
        )));

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
