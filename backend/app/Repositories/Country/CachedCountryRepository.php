<?php

namespace App\Repositories\Country;

use App\DTOs\CountryDetailDto;
use App\DTOs\CountryDto;
use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class CachedCountryRepository implements CountryRepositoryInterface
{
    private const string CACHE_PREFIX = 'countries';

    private const int FRESH_TTL = 3600;      // 1 hour - serve without refresh

    private const int STALE_TTL = 86400;     // 24 hours - serve stale while one process refreshes

    public function __construct(
        private readonly CountryRepositoryInterface $inner,
        private readonly CacheRepository $cache,
    ) {}

    /** @return CountryDto[] */
    public function all(): array
    {
        $raw = $this->cache->flexible(
            $this->cacheKey('all'),
            [self::FRESH_TTL, self::STALE_TTL],
            fn () => array_map(
                fn (CountryDto $dto) => $dto->toArray(),
                $this->inner->all(),
            ),
        );

        return array_map(
            fn (array $item) => CountryDto::fromArray($item),
            $raw,
        );
    }

    public function findByCode(string $code): ?CountryDetailDto
    {
        $code = strtoupper($code);

        $raw = $this->cache->flexible(
            $this->cacheKey('detail', $code),
            [self::FRESH_TTL, self::STALE_TTL],
            fn () => $this->inner->findByCode($code)?->toArray(),
        );

        return $raw ? CountryDetailDto::fromArray($raw) : null;
    }

    private function cacheKey(string $method, string ...$params): string
    {
        return self::CACHE_PREFIX.':'.$method.($params ? ':'.implode(':', $params) : '');
    }
}
