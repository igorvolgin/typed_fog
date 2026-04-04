<?php

namespace Tests\Feature;

use App\DTOs\CountryDetailDto;
use App\DTOs\CountryDto;
use App\Exceptions\ExternalApiException;
use App\Http\Middleware\Auth0Middleware;
use App\Repositories\Contracts\CountryRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CountriesEndpointTest extends TestCase
{
    /**
     * @param  CountryDto[]  $countries
     * @param  array<string, CountryDetailDto>  $details  keyed by cca2
     */
    private function fakeRepository(array $countries, array $details = []): void
    {
        $this->app->instance(
            CountryRepositoryInterface::class,
            new class($countries, $details) implements CountryRepositoryInterface
            {
                public function __construct(
                    private readonly array $countries,
                    private readonly array $details,
                ) {}

                public function all(): array
                {
                    return $this->countries;
                }

                public function findByCode(string $code): ?CountryDetailDto
                {
                    return $this->details[strtoupper($code)] ?? null;
                }
            }
        );
    }

    private function makeDetail(string $code, string $name): CountryDetailDto
    {
        return new CountryDetailDto(
            cca2: $code,
            name: $name,
            officialName: $name,
            flagPng: strtolower($code).'.png',
            flagSvg: strtolower($code).'.svg',
            flagAlt: "The flag of {$name}",
            region: 'Europe',
            subregion: 'Eastern Europe',
            population: 44000000,
            capital: ['Kyiv'],
            timezones: ['UTC+02:00'],
            borders: ['POL', 'ROU'],
            languages: ['Ukrainian'],
            currencies: ['UAH' => ['name' => 'Ukrainian hryvnia', 'symbol' => '₴']],
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

        $this->fakeRepository([
            new CountryDto('UA', 'UKR', '804', 'UKR', 'Ukraine', 'ua.png', 'ua.svg', 'The flag of Ukraine'),
            new CountryDto('DE', 'DEU', '276', 'GER', 'Germany', 'de.png', 'de.svg', 'The flag of Germany'),
        ]);

        $this->getJson('/api/countries')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'code' => 'UA',
                'name' => 'Ukraine',
                'flag' => ['png' => 'ua.png', 'svg' => 'ua.svg', 'alt' => 'The flag of Ukraine'],
            ])
            ->assertJsonFragment([
                'code' => 'DE',
                'name' => 'Germany',
                'flag' => ['png' => 'de.png', 'svg' => 'de.svg', 'alt' => 'The flag of Germany'],
            ]);
    }

    #[Test]
    public function it_returns_empty_array_when_no_countries(): void
    {
        $this->withoutMiddleware(Auth0Middleware::class);
        $this->fakeRepository([]);

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

                public function findByCode(string $code): ?CountryDetailDto
                {
                    throw new ExternalApiException('restcountries.com');
                }
            }
        );

        $this->getJson('/api/countries')
            ->assertStatus(503)
            ->assertJsonFragment(['error' => 'External API unavailable: restcountries.com']);
    }

    #[Test]
    public function show_requires_authentication(): void
    {
        $this->getJson('/api/countries/UA')
            ->assertUnauthorized();
    }

    #[Test]
    public function show_returns_country_detail(): void
    {
        $this->withoutMiddleware(Auth0Middleware::class);

        $this->fakeRepository([], [
            'UA' => $this->makeDetail('UA', 'Ukraine'),
        ]);

        $this->getJson('/api/countries/UA')
            ->assertOk()
            ->assertJson([
                'code' => 'UA',
                'name' => 'Ukraine',
                'officialName' => 'Ukraine',
                'region' => 'Europe',
                'subregion' => 'Eastern Europe',
                'population' => 44000000,
                'capital' => ['Kyiv'],
                'timezones' => ['UTC+02:00'],
                'borders' => ['POL', 'ROU'],
                'languages' => ['Ukrainian'],
                'flag' => [
                    'png' => 'ua.png',
                    'svg' => 'ua.svg',
                    'alt' => 'The flag of Ukraine',
                ],
            ]);
    }

    #[Test]
    public function show_returns_404_for_unknown_code(): void
    {
        $this->withoutMiddleware(Auth0Middleware::class);
        $this->fakeRepository([]);

        $this->getJson('/api/countries/XX')
            ->assertNotFound();
    }
}
