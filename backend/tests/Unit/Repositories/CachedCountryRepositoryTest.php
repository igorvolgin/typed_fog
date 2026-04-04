<?php

namespace Tests\Unit\Repositories;

use App\DTOs\CountryDto;
use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Repositories\Country\CachedCountryRepository;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CachedCountryRepositoryTest extends TestCase
{
    private function makeCountry(string $code, string $name): CountryDto
    {
        return new CountryDto(
            cca2: $code,
            cca3: '',
            ccn3: '',
            cioc: '',
            name: $name,
            flagPng: '',
            flagSvg: '',
        );
    }

    #[Test]
    public function it_returns_countries_from_inner_repository(): void
    {
        $countries = [
            $this->makeCountry('UA', 'Ukraine'),
            $this->makeCountry('DE', 'Germany'),
        ];

        $inner = $this->createMock(CountryRepositoryInterface::class);
        $inner->expects($this->once())->method('all')->willReturn($countries);

        $cache = new CacheRepository(new ArrayStore);
        $repo = new CachedCountryRepository($inner, $cache);

        $result = $repo->all();

        $this->assertCount(2, $result);
        $this->assertSame('UA', $result[0]->cca2);
        $this->assertSame('Germany', $result[1]->name);
    }

    #[Test]
    public function it_caches_results_and_does_not_call_inner_twice(): void
    {
        $countries = [$this->makeCountry('UA', 'Ukraine')];

        $inner = $this->createMock(CountryRepositoryInterface::class);
        $inner->expects($this->once())->method('all')->willReturn($countries);

        $cache = new CacheRepository(new ArrayStore);
        $repo = new CachedCountryRepository($inner, $cache);

        $repo->all();
        $result = $repo->all();

        $this->assertCount(1, $result);
        $this->assertSame('Ukraine', $result[0]->name);
    }
}
