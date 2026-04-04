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
                    'flags' => ['png' => 'ua.png', 'svg' => 'ua.svg'],
                ],
                [
                    'cca2' => 'DE',
                    'cca3' => 'DEU',
                    'ccn3' => '276',
                    'cioc' => 'GER',
                    'name' => ['common' => 'Germany'],
                    'flags' => ['png' => 'de.png', 'svg' => 'de.svg'],
                ],
            ]),
        ]);

        $repo = new RestCountriesRepository;
        $result = $repo->all();

        $this->assertCount(2, $result);
        $this->assertSame('UA', $result[0]->cca2);
        $this->assertSame('Ukraine', $result[0]->name);
        $this->assertSame('ua.svg', $result[0]->flagSvg);
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
    public function it_throws_external_api_exception_on_connection_failure(): void
    {
        Http::fake([
            'restcountries.com/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('timeout'),
        ]);

        $this->expectException(ExternalApiException::class);

        (new RestCountriesRepository)->all();
    }
}
