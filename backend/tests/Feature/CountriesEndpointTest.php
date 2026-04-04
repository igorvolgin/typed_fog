<?php

namespace Tests\Feature;

use App\DTOs\CountryDto;
use App\Exceptions\ExternalApiException;
use App\Http\Middleware\Auth0Middleware;
use App\Repositories\Contracts\CountryRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CountriesEndpointTest extends TestCase
{
    private function fakeCountries(array $countries): void
    {
        $this->app->instance(
            CountryRepositoryInterface::class,
            new class($countries) implements CountryRepositoryInterface
            {
                public function __construct(private readonly array $countries) {}

                public function all(): array
                {
                    return $this->countries;
                }
            }
        );
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/countries')
            ->assertUnauthorized();
    }

    #[Test]
    public function it_returns_countries_list(): void
    {
        $this->withoutMiddleware(Auth0Middleware::class);

        $this->fakeCountries([
            new CountryDto('UA', 'UKR', '804', 'UKR', 'Ukraine', 'ua.png', 'ua.svg'),
            new CountryDto('DE', 'DEU', '276', 'GER', 'Germany', 'de.png', 'de.svg'),
        ]);

        $this->getJson('/api/countries')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'code' => 'UA',
                'name' => 'Ukraine',
                'flag' => ['png' => 'ua.png', 'svg' => 'ua.svg'],
            ])
            ->assertJsonFragment([
                'code' => 'DE',
                'name' => 'Germany',
                'flag' => ['png' => 'de.png', 'svg' => 'de.svg'],
            ]);
    }

    #[Test]
    public function it_returns_empty_array_when_no_countries(): void
    {
        $this->withoutMiddleware(Auth0Middleware::class);
        $this->fakeCountries([]);

        $this->getJson('/api/countries')
            ->assertOk()
            ->assertJsonCount(0);
    }

    #[Test]
    public function it_returns_503_when_external_api_fails(): void
    {
        $this->withoutMiddleware(Auth0Middleware::class);

        $this->app->instance(
            CountryRepositoryInterface::class,
            new class implements CountryRepositoryInterface
            {
                public function all(): array
                {
                    throw new ExternalApiException('restcountries.com');
                }
            }
        );

        $this->getJson('/api/countries')
            ->assertStatus(503)
            ->assertJsonFragment(['error' => 'External API unavailable: restcountries.com']);
    }
}
