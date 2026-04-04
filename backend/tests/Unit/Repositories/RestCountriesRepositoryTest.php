<?php

namespace Tests\Unit\Repositories;

use App\Exceptions\ExternalApiException;
use App\Repositories\Country\RestCountriesRepository;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestCountriesRepositoryTest extends TestCase
{
    #[Test]
    public function it_maps_api_response_to_dtos(): void
    {
        Http::fake([
            'restcountries.com/*' => Http::response([
                [
                    'cca2' => 'UA',
                    'cca3' => 'UKR',
                    'ccn3' => '804',
                    'cioc' => 'UKR',
                    'name' => ['common' => 'Ukraine'],
                    'flags' => ['png' => 'ua.png', 'svg' => 'ua.svg', 'alt' => 'The flag of Ukraine'],
                ],
                [
                    'cca2' => 'DE',
                    'cca3' => 'DEU',
                    'ccn3' => '276',
                    'cioc' => 'GER',
                    'name' => ['common' => 'Germany'],
                    'flags' => ['png' => 'de.png', 'svg' => 'de.svg', 'alt' => 'The flag of Germany'],
                ],
            ]),
        ]);

        $repo = new RestCountriesRepository;
        $result = $repo->all();

        $this->assertCount(2, $result);
        $this->assertSame('UA', $result[0]->cca2);
        $this->assertSame('Ukraine', $result[0]->name);
        $this->assertSame('ua.svg', $result[0]->flagSvg);
        $this->assertSame('The flag of Ukraine', $result[0]->flagAlt);
        $this->assertSame('DE', $result[1]->cca2);
    }

    #[Test]
    public function it_throws_external_api_exception_on_server_error(): void
    {
        Http::fake([
            'restcountries.com/*' => Http::response('Server Error', 500),
        ]);

        $this->expectException(ExternalApiException::class);

        (new RestCountriesRepository)->all();
    }

    #[Test]
    public function it_finds_country_detail_by_code(): void
    {
        Http::fake([
            'restcountries.com/v3.1/alpha/UA*' => Http::response([
                'cca2' => 'UA',
                'name' => ['common' => 'Ukraine', 'official' => 'Ukraine'],
                'flags' => ['png' => 'ua.png', 'svg' => 'ua.svg', 'alt' => 'The flag of Ukraine'],
                'region' => 'Europe',
                'subregion' => 'Eastern Europe',
                'population' => 44134693,
                'capital' => ['Kyiv'],
                'timezones' => ['UTC+02:00'],
                'borders' => ['BLR', 'HUN', 'MDA', 'POL', 'ROU', 'RUS', 'SVK'],
                'languages' => ['ukr' => 'Ukrainian'],
                'currencies' => ['UAH' => ['name' => 'Ukrainian hryvnia', 'symbol' => '₴']],
            ]),
        ]);

        $result = (new RestCountriesRepository)->findByCode('UA');

        $this->assertNotNull($result);
        $this->assertSame('UA', $result->cca2);
        $this->assertSame('Ukraine', $result->name);
        $this->assertSame('Europe', $result->region);
        $this->assertSame('Eastern Europe', $result->subregion);
        $this->assertSame(44134693, $result->population);
        $this->assertSame(['Kyiv'], $result->capital);
        $this->assertSame(['Ukrainian'], $result->languages);
        $this->assertSame(['UAH' => ['name' => 'Ukrainian hryvnia', 'symbol' => '₴']], $result->currencies);
        $this->assertContains('BLR', $result->borders);
    }

    #[Test]
    public function it_returns_null_when_country_not_found(): void
    {
        Http::fake([
            'restcountries.com/*' => Http::response(['status' => 404, 'message' => 'Not Found'], 404),
        ]);

        $result = (new RestCountriesRepository)->findByCode('XX');

        $this->assertNull($result);
    }

    #[Test]
    public function it_throws_external_api_exception_on_connection_failure(): void
    {
        Http::fake([
            'restcountries.com/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('timeout'),
        ]);

        $this->expectException(ExternalApiException::class);

        (new RestCountriesRepository)->all();
    }
}
