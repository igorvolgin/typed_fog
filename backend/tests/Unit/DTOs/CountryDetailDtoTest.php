<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CountryDetailDto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CountryDetailDtoTest extends TestCase
{
    #[Test]
    public function it_creates_from_rest_countries_v31_response(): void
    {
        $data = [
            'cca2' => 'UA',
            'name' => ['common' => 'Ukraine', 'official' => 'Ukraine'],
            'flags' => [
                'png' => 'https://flagcdn.com/w320/ua.png',
                'svg' => 'https://flagcdn.com/ua.svg',
                'alt' => 'The flag of Ukraine',
            ],
            'region' => 'Europe',
            'subregion' => 'Eastern Europe',
            'population' => 44134693,
            'capital' => ['Kyiv'],
            'timezones' => ['UTC+02:00'],
            'borders' => ['BLR', 'HUN', 'MDA', 'POL', 'ROU', 'RUS', 'SVK'],
            'languages' => ['ukr' => 'Ukrainian'],
            'currencies' => [
                'UAH' => ['name' => 'Ukrainian hryvnia', 'symbol' => '₴'],
            ],
        ];

        $dto = CountryDetailDto::fromRestCountriesV31($data);

        $this->assertSame('UA', $dto->cca2);
        $this->assertSame('Ukraine', $dto->name);
        $this->assertSame('Ukraine', $dto->officialName);
        $this->assertSame('Europe', $dto->region);
        $this->assertSame('Eastern Europe', $dto->subregion);
        $this->assertSame(44134693, $dto->population);
        $this->assertSame(['Kyiv'], $dto->capital);
        $this->assertSame(['UTC+02:00'], $dto->timezones);
        $this->assertContains('BLR', $dto->borders);
        $this->assertSame(['Ukrainian'], $dto->languages);
        $this->assertSame(['UAH' => ['name' => 'Ukrainian hryvnia', 'symbol' => '₴']], $dto->currencies);
    }

    #[Test]
    public function it_handles_missing_optional_fields(): void
    {
        $data = [
            'cca2' => 'AQ',
            'name' => ['common' => 'Antarctica'],
            'flags' => [],
        ];

        $dto = CountryDetailDto::fromRestCountriesV31($data);

        $this->assertSame('AQ', $dto->cca2);
        $this->assertSame('Antarctica', $dto->name);
        $this->assertSame('Antarctica', $dto->officialName);
        $this->assertSame('', $dto->region);
        $this->assertSame('', $dto->subregion);
        $this->assertSame(0, $dto->population);
        $this->assertSame([], $dto->capital);
        $this->assertSame([], $dto->timezones);
        $this->assertSame([], $dto->borders);
        $this->assertSame([], $dto->languages);
        $this->assertSame([], $dto->currencies);
    }

    #[Test]
    public function it_roundtrips_through_array(): void
    {
        $dto = new CountryDetailDto(
            cca2: 'DE',
            name: 'Germany',
            officialName: 'Federal Republic of Germany',
            flagPng: 'de.png',
            flagSvg: 'de.svg',
            flagAlt: 'The flag of Germany',
            region: 'Europe',
            subregion: 'Western Europe',
            population: 83240525,
            capital: ['Berlin'],
            timezones: ['UTC+01:00'],
            borders: ['AUT', 'BEL', 'CZE', 'DNK', 'FRA', 'LUX', 'NLD', 'POL', 'CHE'],
            languages: ['German'],
            currencies: ['EUR' => ['name' => 'Euro', 'symbol' => '€']],
        );

        $restored = CountryDetailDto::fromArray($dto->toArray());

        $this->assertEquals($dto, $restored);
    }
}
