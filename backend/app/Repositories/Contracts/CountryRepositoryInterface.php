<?php

namespace App\Repositories\Contracts;

use App\DTOs\CountryDetailDto;
use App\DTOs\CountryDto;

interface CountryRepositoryInterface
{
    /** @return CountryDto[] */
    public function all(): array;

    public function findByCode(string $code): ?CountryDetailDto;
}
