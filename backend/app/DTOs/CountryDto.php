<?php

namespace App\DTOs;

readonly class CountryDto
{
    public function __construct(
        public string $cca2,
        public string $cca3,
        public string $ccn3,
        public string $cioc,
        public string $name,
        public string $flagPng,
        public string $flagSvg,
    ) {}

    public static function fromRestCountriesV31(array $data): self
    {
        return new self(
            cca2: $data['cca2'],
            cca3: $data['cca3'] ?? '',
            ccn3: $data['ccn3'] ?? '',
            cioc: $data['cioc'] ?? '',
            name: $data['name']['common'],
            flagPng: $data['flags']['png'] ?? '',
            flagSvg: $data['flags']['svg'] ?? '',
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            cca2: $data['cca2'],
            cca3: $data['cca3'],
            ccn3: $data['ccn3'],
            cioc: $data['cioc'],
            name: $data['name'],
            flagPng: $data['flagPng'],
            flagSvg: $data['flagSvg'],
        );
    }

    public function toArray(): array
    {
        return [
            'cca2' => $this->cca2,
            'cca3' => $this->cca3,
            'ccn3' => $this->ccn3,
            'cioc' => $this->cioc,
            'name' => $this->name,
            'flagPng' => $this->flagPng,
            'flagSvg' => $this->flagSvg,
        ];
    }
}
