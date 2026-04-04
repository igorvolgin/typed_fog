<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CountryDto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CountryDtoTest extends TestCase
{
    #[Test]
    public function it_creates_from_rest_countries_v31_response(): void
    {
        $data = [
            'cca2' => 'UA',
            'cca3' => 'UKR',
            'ccn3' => '804',
            'cioc' => 'UKR',
            'name' => ['common' => 'Ukraine', 'official' => 'Ukraine'],
            'flags' => [
                'png' => 'https://flagcdn.com/w320/ua.png',
                'svg' => 'https://flagcdn.com/ua.svg',
            ],
        ];

        $dto = CountryDto::fromRestCountriesV31($data);

        $this->assertSame('UA', $dto->cca2);
        $this->assertSame('UKR', $dto->cca3);
        $this->assertSame('804', $dto->ccn3);
        $this->assertSame('UKR', $dto->cioc);
        $this->assertSame('Ukraine', $dto->name);
        $this->assertSame('https://flagcdn.com/w320/ua.png', $dto->flagPng);
        $this->assertSame('https://flagcdn.com/ua.svg', $dto->flagSvg);
        $this->assertSame('', $dto->flagAlt);
    }

    #[Test]
    public function it_handles_missing_optional_fields(): void
    {
        $data = [
            'cca2' => 'AQ',
            'name' => ['common' => 'Antarctica'],
            'flags' => [],
        ];

        $dto = CountryDto::fromRestCountriesV31($data);

        $this->assertSame('AQ', $dto->cca2);
        $this->assertSame('', $dto->cca3);
        $this->assertSame('', $dto->ccn3);
        $this->assertSame('', $dto->cioc);
        $this->assertSame('Antarctica', $dto->name);
        $this->assertSame('', $dto->flagPng);
        $this->assertSame('', $dto->flagSvg);
        $this->assertSame('', $dto->flagAlt);
    }

    #[Test]
    public function it_roundtrips_through_array(): void
    {
        $dto = new CountryDto(
            cca2: 'DE',
            cca3: 'DEU',
            ccn3: '276',
            cioc: 'GER',
            name: 'Germany',
            flagPng: 'https://flagcdn.com/w320/de.png',
            flagSvg: 'https://flagcdn.com/de.svg',
            flagAlt: 'The flag of Germany',
        );

        $restored = CountryDto::fromArray($dto->toArray());

        $this->assertEquals($dto, $restored);
    }
}
