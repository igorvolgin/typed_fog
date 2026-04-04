<?php

namespace App\Repositories\Contracts;

use App\DTOs\CountryDto;

interface CountryRepositoryInterface
{
    /** @return CountryDto[] */
    public function all(): array;
}
