<?php

namespace Tests\Unit\Repositories;

use App\DTOs\CountryDetailDto;
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
            flagAlt: '',
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

    private function makeCountryDetail(string $code, string $name): CountryDetailDto
    {
        return new CountryDetailDto(
            cca2: $code,
            name: $name,
            officialName: $name,
            flagPng: '',
            flagSvg: '',
            flagAlt: '',
            region: 'Europe',
            subregion: 'Eastern Europe',
            population: 44000000,
            capital: ['Kyiv'],
            timezones: ['UTC+02:00'],
            borders: [],
            languages: ['Ukrainian'],
            currencies: [],
        );
    }

    #[Test]
    public function it_returns_country_detail_by_code_from_inner(): void
    {
        $detail = $this->makeCountryDetail('UA', 'Ukraine');

        $inner = $this->createMock(CountryRepositoryInterface::class);
        $inner->expects($this->once())->method('findByCode')->with('UA')->willReturn($detail);

        $cache = new CacheRepository(new ArrayStore);
        $repo = new CachedCountryRepository($inner, $cache);

        $result = $repo->findByCode('UA');

        $this->assertNotNull($result);
        $this->assertInstanceOf(CountryDetailDto::class, $result);
        $this->assertSame('UA', $result->cca2);
        $this->assertSame('Europe', $result->region);
    }

    #[Test]
    public function it_caches_find_by_code_and_does_not_call_inner_twice(): void
    {
        $detail = $this->makeCountryDetail('UA', 'Ukraine');

        $inner = $this->createMock(CountryRepositoryInterface::class);
        $inner->expects($this->once())->method('findByCode')->with('UA')->willReturn($detail);

        $cache = new CacheRepository(new ArrayStore);
        $repo = new CachedCountryRepository($inner, $cache);

        $repo->findByCode('UA');
        $result = $repo->findByCode('UA');

        $this->assertSame('Ukraine', $result->name);
    }

    #[Test]
    public function it_returns_null_for_unknown_code(): void
    {
        $inner = $this->createMock(CountryRepositoryInterface::class);
        $inner->expects($this->once())->method('findByCode')->with('XX')->willReturn(null);

        $cache = new CacheRepository(new ArrayStore);
        $repo = new CachedCountryRepository($inner, $cache);

        $this->assertNull($repo->findByCode('XX'));
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
